<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

use App\Models\Sinistro;
use Illuminate\Http\Request;
use App\Models\Filial;
use App\Models\Gasto;
use Illuminate\Support\Facades\Mail;
use App\Models\Custo;

class SinistroController extends Controller
{
    //
    
        public function index()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');

        return view('sinistro.index', compact('filiais'));
    }

    public function sem_terceiro()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');

        return view('sinistro.sem_terceiro', compact('filiais'));
    }



     public function store(Request $request)
    {
        $validatedData = $request->validate([
            'data' => 'required|date',
            'hora' => 'required|string',
            'name' => 'required|string',
            'telefone' => 'required|string',
            'email' => 'required|email',
            'placa' => 'required|string',
            'filial_origem' => ['required', 'string', 'not_regex:/^\s*$/'],
            'email_terceiro' => 'nullable|email',
            'telefone_terceiro' => 'nullable|string',
            'nome_terceiro' => 'nullable|string',
            'placa_terceiro' => 'nullable|string',
            'ocorrido' => 'required|string'
        ]);

        $tipo = $request->input('tipo_sinistro');

        $sinistro = Sinistro::create($validatedData);
        
        $foto = $request->file('boletim');

        $foto_2 = $request->file('foto_2');

        $foto_3 = $request->file('foto_3');

        $foto_4 = $request->file('foto_4');

        $foto_5 = $request->file('foto_5');

        $foto_2_terceiro = $request->file('foto_2_terceiro');

        $cnh_terceiro = $request->file('cnh_terceiro');

        $foto_ocorrido = $request->file('foto_ocorrido');


        // ENVIAR VÁRIOS E-MAILS
        $emailsString = $request->input('email_gestores');

        $emailsValidos = []; // inicializa como array vazio

        if (!empty($emailsString)) {

            // Divide a string em array usando ';' como separador
            $emailsArray = array_map('trim', explode(';', $emailsString));

            // Filtra apenas e-mails válidos
            $emailsValidos = array_filter($emailsArray, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            // Opcional: retornar erro se algum e-mail for inválido
            if (count($emailsValidos) !== count($emailsArray)) {
                return back()->withErrors(['emails' => 'Um ou mais e-mails são inválidos.']);
            }
        }
        //FIM
        $tipo = $request->input('tipo');
        //dd($tipo);


        if ($tipo === 'sem_terceiro') {
            $email = 'emails.sem_terceiro';
        } else {
            $email = 'emails.com_terceiro';
        }

        print($tipo);

        // Envia o email com os dados do formulário
        Mail::send($email, ['dados' => $validatedData], function($message) use ($emailsValidos, $validatedData, $foto, $foto_2, $foto_3, $foto_4, $foto_5 ,$sinistro){
            $message->to([$validatedData['email'],'documentos.sinistros@grupocargopolo.com.br','produtividade@grupocargopolo.com.br']);
            //$message->to('higor.05@hotmail.com');
            //$message->to(['cadastro.suprimentos@grupocargopolo.com.br', 'amanda.bellomo@grupocargopolo.com.br' ]);
            $message->cc($emailsValidos);
            $message->subject('##'. $sinistro->id . ' NOVO SINISTRO NA PLACA: '. $validatedData['placa'] . ' - ' . \Carbon\Carbon::parse($validatedData['data'])->format('d/m/Y'));

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


            if ($foto_5){
                $pathToFile = $foto_5->getPathname();
                $filename = $foto_5->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto_5->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }


            if ($foto_2_terceiro) {
                $pathToFile = $foto_2_terceiro->getPathname();
                $filename = $foto_2_terceiro->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto_2_terceiro->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }

            if ($cnh_terceiro) {
                $pathToFile = $cnh_terceiro->getPathname();
                $filename = $cnh_terceiro->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $cnh_terceiro->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }

            if ($foto_ocorrido) {
                $pathToFile = $foto_ocorrido->getPathname();
                $filename = $foto_ocorrido->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto_ocorrido->getClientMimeType(), // Tipo MIME do arquivo
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

                        if ($foto_5) {
                \Log::info('Foto 5 anexada: ' . $foto_5->getClientOriginalName());
            } else {
                \Log::info('Foto 5 não anexada.');
            }
        });

        // Redirecionar ou retornar uma resposta de sucesso
        return redirect()->route('sinistro.index')->with('success', 'Transferencia realizada com sucesso!');
    }
}
