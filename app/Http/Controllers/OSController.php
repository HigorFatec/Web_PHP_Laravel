<?php

namespace App\Http\Controllers;

use App\Models\OS;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OSAssignment;
use App\Models\UserCredential;
use App\Models\OSPause;


class OSController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function index()
    {
        $codfun = Auth::user()->CODFUN;

        // Verifica se o usuário é admin
        $credencial = UserCredential::where('matricula', $codfun)->first();
        $isAdmin = $credencial && $credencial->is_admin;

        if ($isAdmin) {
            // Para admin, pega todas as ordens normalmente
            $ordens = OS::getByMatriculaQuery($codfun)
                ->orderBy('O.DATREF', 'desc')
                ->paginate(3);

            // Pega os assignments do MySQL relacionados a essas ordens
            $assignments = OSAssignment::on('mysql')
                ->whereIn('os_id', $ordens->pluck('ORDEM'))
                ->get();

            return view('os.index', compact('ordens', 'assignments'));
        } else {
            // Para usuários normais, filtra pelas OS atribuídas no MySQL
            $assignments = OSAssignment::on('mysql')
                ->where('matricula', $codfun)
                ->whereNull('finish_time')
                ->get(['os_id', 'sequencia']);

            $ordensQuery = OS::getByMatriculaQuery($codfun);

            if ($assignments->isNotEmpty()) {
                $ordensQuery->where(function ($query) use ($assignments) {
                    foreach ($assignments as $assignment) {
                        $query->orWhere(function ($sub) use ($assignment) {
                            $sub->where('O.CODORD', $assignment->os_id)
                                ->where('IOS.SEQUEN', $assignment->sequencia);
                        });
                    }
                });
            } else {
                // Nenhuma OS atribuída → resultado vazio
                $ordensQuery->whereRaw('1 = 0');
            }

            $ordens = $ordensQuery
                ->orderBy('O.DATREF', 'desc')
                ->paginate(3);

            return view('os.index', compact('ordens'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */


    public function details($codord, $sequen)
    {
        $ordem = OS::getByOrdemSequencia($codord, $sequen)->first();

        $funcionarios = Funcionario::all();

        // Pega o status APENAS pra essa OS
        $assignment = \App\Models\OSAssignment::with('pauses')
            ->where('matricula', Auth::user()->CODFUN)
            ->where('os_id', $ordem->ORDEM)
            ->where('sequencia', $ordem->SEQUENCIA)
            ->first();


        if ($assignment) {
            if ($assignment->finish_time) {
                $ordem->status = 'Finalizada';
                $ordem->progress = 100;
                $ordem->progress_color = 'green';
            } elseif ($assignment->pause_time && !$assignment->resume_time) {
                $ordem->status = 'Em Pausa';
                $ordem->progress = 50;
                $ordem->progress_color = 'orange';
            } elseif ($assignment->resume_time) {
                $ordem->status = 'Em Execução';
                $ordem->progress = 75;
                $ordem->progress_color = 'blue';
            } elseif ($assignment->start_time) {
                $ordem->status = 'Iniciada';
                $ordem->progress = 25;
                $ordem->progress_color = 'blue';
            } else {
                $ordem->status = 'Pendente';
                $ordem->progress = 0;
                $ordem->progress_color = 'grey';
            }
        } else {
            $ordem->status = 'Pendente';
            $ordem->progress = 0;
            $ordem->progress_color = 'grey';
        }


        return view('os.details', compact('ordem', 'funcionarios','assignment'));
    }





        public function assign(Request $request, $codord, $sequen)
    {
        // Validação básica
        $request->validate([
            'matricula' => 'required|string',
        ]);

        // Cria o registro de atribuição
        OSAssignment::create([
            'matricula' => $request->matricula,
            'os_id' => $codord,  // ou use seu ID real da O.S.
            'sequencia' => $sequen, // 👈 Armazena a sequência!

        ]);

        return back()->with('success', 'Ordem de Serviço atribuída com sucesso!');
    }


    public function updateStatus(Request $request, $codord, $sequen)
{
    $request->validate(['action' => 'required|string']);

    $assignment = OSAssignment::where('os_id', $codord)->first();
    if (!$assignment) {
        return back()->with('error', 'OS não atribuída ainda.');
    }

    switch ($request->action) {
        case 'start':
            $assignment->start_time = now();
            break;
        case 'pause':
            $assignment->pause_time = now();
            break;
        case 'resume':
            $assignment->resume_time = now();
            break;
        case 'finish':
            $assignment->finish_time = now();
            break;
    }

    $assignment->save();

    return back()->with('success', 'Status atualizado: ' . $request->action);
}



public function start($codord, $sequen)
{
    $codfun = Auth::user()->CODFUN;

    OSAssignment::where('matricula', $codfun)
        ->where('os_id', $codord)
        ->where('sequencia', $sequen)
        ->update(['start_time' => now()]);

    return back()->with('success', 'Ordem de Serviço iniciada.');
}

public function pause(Request $request, $codord, $sequen)
{

        $codfun = Auth::user()->CODFUN;

    $request->validate([
        'reason_code' => 'required',
    ]);

    // Recupera ou cria o assignment
    $assignment = OSAssignment::where('os_id', $codord)
        ->where('sequencia', $sequen)
        ->where('matricula', Auth::user()->CODFUN)
        ->firstOrFail();

    // Próximo número de pausa
    $pauseNumber = OSPause::where('assignment_id', $assignment->id)->count() + 1;

    OSPause::create([
        'assignment_id' => $assignment->id,
        'pause_number' => $pauseNumber,
        'reason_code' => $request->reason_code,
        'start_time' => now(),
    ]);

    
    OSAssignment::where('matricula', $codfun)
        ->where('os_id', $codord)
        ->where('sequencia', $sequen)
        ->update(['pause_time' => now()]);

    return back()->with('success', 'Pausa iniciada!');
}

public function resume($codord, $sequen)
{
    $codfun = Auth::user()->CODFUN;

    OSAssignment::where('matricula', $codfun)
        ->where('os_id', $codord)
        ->where('sequencia', $sequen)
        ->update(['resume_time' => now()]);

    // Buscar a última pausa aberta (sem end_time) para este usuário, OS e sequência
    $pause = \App\Models\OSPause::where('assignment_id', function ($query) use ($codfun, $codord, $sequen) {
            $query->select('id')
                ->from('os_assignments')
                ->where('matricula', $codfun)
                ->where('os_id', $codord)
                ->where('sequencia', $sequen)
                ->limit(1);
        })
        ->whereNull('end_time')
        ->orderBy('start_time', 'desc')
        ->first();

    if ($pause) {
        $pause->end_time = now();
        $pause->save();
        return back()->with('success', 'Pausa finalizada com sucesso!');
    }



    return back()->with('error', 'Nenhuma pausa ativa encontrada para finalizar.');
}

public function finish($codord, $sequen)
{
    $codfun = Auth::user()->CODFUN;

    OSAssignment::where('matricula', $codfun)
        ->where('os_id', $codord)
        ->where('sequencia', $sequen)
        ->update(['finish_time' => now()]);

    return back()->with('success', 'Ordem de Serviço finalizada.');
}



}
