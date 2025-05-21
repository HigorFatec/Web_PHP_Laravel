<?php

namespace App\Http\Controllers;

use App\Models\Florestal_Pix;
use Illuminate\Http\Request;
use App\Models\Filial;
use App\Models\Produto_Arla;
use Illuminate\Support\Facades\Mail;



class FlorestalPixController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $filiais = Filial::orderBy('filial')->pluck('filial');
        $produtos = Produto_Arla::orderBy('nome')->pluck('nome');

        return view('florestal_pix.index', compact('filiais','produtos'));
        //
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
            'email' => 'required|email',
            'data' => 'required|string',
            'cupom' => 'required|string',
            'placa' => 'required|string',
            'km' => 'required|string',
            'cpf' => 'required|string',
            'name' => 'required|string',
            'cnpj' => 'required|string',
            'posto' => 'required|string',
            'produto' => ['required', 'string', 'not_regex:/^\s*$/'],
            'litragem' => 'required|string',
            'valor' => 'required|string',
            'pix' => 'required|string',
            'valor_3' => 'required|string',
            'email_gestor' => 'required|email',
            'filial' => ['required', 'string', 'not_regex:/^\s*$/'],
            'produto_arla' => 'required|string',
            'litragem_arla' => 'required|string',
            'valor_arla' => 'required|string',

        ]);

        $pagamentoPix = Florestal_Pix::create($validatedData);

        $foto = $request->file('foto');

        // Envia o email com os dados do formulário
        Mail::send('emails.florestal_pix', ['dados' => $validatedData], function($message) use ($validatedData, $foto, $pagamentoPix){
            $message->to(['felipe.brito@grupocargopolo.com.br','silvio.moura@grupocargopolo.com.br','contasapagar@grupocargopolo.com.br','michel.plevka@grupocargopolo.com.br','ludmylla.gomes@grupocargopolo.com.br','jenival.sampaio@grupocargopolo.com.br','jaine.paula@grupocargopolo.com.br','combustivel@grupocargopolo.com.br','taisa.pereira@grupocargopolo.com.br','wagner.mosna@grupocargopolo.com.br']);
            //$message->to('higor.05@hotmail.com');
            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            $message->cc([$validatedData['email'],$validatedData['email_gestor']]);
            $message->subject( ' TRANSFERÊNCIA DE PIX FLORESTAL; POSTO: '. $validatedData['cnpj'] . ' PLACA: ' . $validatedData['placa'] );

            //Verificar se existe imagem anexada
            if ($foto)  {
                $pathToFile = $foto->getPathname();
                $filename = $foto->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }

            if ($foto) {
                \Log::info('Foto anexada: ' . $foto->getClientOriginalName());
            } else {
                \Log::info('Foto não anexada.');
            }
            

        });
        

        // Redirecionar ou retornar uma resposta de sucesso
        return redirect()->route('florestal_pix.index')->with('success', 'Transferencia realizada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Florestal_Pix $florestal_Pix)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Florestal_Pix $florestal_Pix)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Florestal_Pix $florestal_Pix)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Florestal_Pix $florestal_Pix)
    {
        //
    }
}
