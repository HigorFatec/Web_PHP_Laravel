<?php

namespace App\Http\Controllers;

use App\Models\Transf_Veiculo;
use Illuminate\Http\Request;
use App\Models\Filial;
use App\Models\Gasto;
use Illuminate\Support\Facades\Mail;
use App\Models\Custo;


class TransfVeiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');
        $veiculos = Custo::orderBy('nome')->pluck('nome');
        $gastos = Gasto::orderBy('nome')->pluck('nome');

        return view('transf_veiculo.index', compact('filiais', 'veiculos', 'gastos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'data' => 'required|date',
            'name' => 'required|string',
            'email' => 'required|email',
            'placa' => 'required|string',
            'placa_carreta' => 'nullable|string',
            'placa_carreta_2' => 'nullable|string',
            'placa_carreta_3' => 'nullable|string',
            'filial_origem' => ['required', 'string', 'not_regex:/^\s*$/'],
            'filial_destino' => ['required', 'string', 'not_regex:/^\s*$/'],
            'centro_custo' => ['required', 'string', 'not_regex:/^\s*$/'],
            'centro_gasto' => ['required', 'string', 'not_regex:/^\s*$/'],
            'previsao_chegada' => 'required|date',
            'email_responsavel' => 'required|email',
            'conferencia_pneus' => 'required|string',
        ]);

        $transfVeiculo = Transf_Veiculo::create($validatedData);
        
        $foto = $request->file('foto');

        $foto_2 = $request->file('foto_2');

        $foto_3 = $request->file('foto_3');

        $foto_4 = $request->file('foto_4');

        // Envia o email com os dados do formulário
        Mail::send('emails.transf_veiculo', ['dados' => $validatedData], function($message) use ($validatedData, $foto, $foto_2, $foto_3, $foto_4, $transfVeiculo){
            $message->to([$validatedData['email'],'transferenciaveiculo@grupocargopolo.com.br','vinicius.nardini@grupocargopolo.com.br','liderdeturno@grupocargopolo.com.br','celularisco@grupocargopolo.com.br','celulalogistica@grupocargopolo.com.br', 'marcos.simonassi@grupocargopolo.com.br']);
            //$message->to('higor.05@hotmail.com');
            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            $message->cc([$validatedData['email_responsavel']]);
            $message->subject('##'. $transfVeiculo->id . ' TRANSFERÊNCIA DE VEÍCULOS : '. $validatedData['placa'] . ' - ' . \Carbon\Carbon::parse($validatedData['previsao_chegada'])->format('d/m/Y'));

            //Verificar se existe imagem anexada
            if ($foto)  {
                $pathToFile = $foto->getPathname();
                $filename = $foto->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }
            if ($foto_2)  {
                $pathToFile = $foto_2->getPathname();
                $filename = $foto_2->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto_2->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }
            if ($foto_3){
                $pathToFile = $foto_3->getPathname();
                $filename = $foto_3->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto_3->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }

            if ($foto_4){
                $pathToFile = $foto_4->getPathname();
                $filename = $foto_4->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto_4->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }

            if ($foto) {
                \Log::info('Foto anexada: ' . $foto->getClientOriginalName());
            } else {
                \Log::info('Foto não anexada.');
            }
            
            if ($foto_2) {
                \Log::info('Foto 2 anexada: ' . $foto_2->getClientOriginalName());
            } else {
                \Log::info('Foto 2 não anexada.');
            }

            if ($foto_3) {
                \Log::info('Foto 3 anexada: ' . $foto_3->getClientOriginalName());
            } else {
                \Log::info('Foto 3 não anexada.');
            }

            if ($foto_4) {
                \Log::info('Foto 4 anexada: ' . $foto_4->getClientOriginalName());
            } else {
                \Log::info('Foto 4 não anexada.');
            }

        });

        // Redirecionar ou retornar uma resposta de sucesso
        return redirect()->route('transf_veiculo.index')->with('success', 'Transferencia realizada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transf_Veiculo $transf_Veiculo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transf_Veiculo $transf_Veiculo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transf_Veiculo $transf_Veiculo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transf_Veiculo $transf_Veiculo)
    {
        //
    }
}
