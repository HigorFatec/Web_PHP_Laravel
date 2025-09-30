<?php

namespace App\Http\Controllers;

use App\Models\Descarte_Pneus;
use App\Models\Filial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class DescartePneusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = Filial::orderBy('filial')->pluck('filial');
        //
        return view('descarte.index', compact('filiais'));
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
            'name' => 'required|string',
            'email' => 'required|email',
            'filial_origem' => ['required', 'string', 'not_regex:/^\s*$/'],
            'data' => 'required|date',
            'hora' => 'required|string',
            'cod_pneu' => 'required|string',
            'n_dot' => 'required|string',
            'status_pneu' => ['required', 'string', 'not_regex:/^\s*$/'],
            'placa' => 'nullable|string',
            'motivo_descarte' => ['required', 'string', 'not_regex:/^\s*$/'],
            'observacoes' => 'required|string'
        ]);

        $descarte = Descarte_Pneus::create($validatedData);

        $foto_n_fogo = $request->file('foto_n_fogo');

        $foto_dot = $request->file('foto_dot');

        $foto_descarte = $request->file('foto_descarte');

        Mail::send('emails.descarte_pneus', ['dados' => $validatedData], function ($message) use ($validatedData, $foto_n_fogo, $foto_dot, $foto_descarte) {
            $message->to(['pneus@grupocargopolo.com.br']);
            $message->cc(['alef.bondezan@grupocargopolo.com.br', 'rene.paludetti@grupocargopolo.com.br']);
            $message->subject('Formulário de Descarte de Pneus - ' . $validatedData['filial_origem']);

            if ($foto_n_fogo) {
                $pathToFile = $foto_n_fogo->getPathname();
                $filename = $foto_n_fogo->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto_n_fogo->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }

            if ($foto_dot) {
                $pathToFile = $foto_dot->getPathname();
                $filename = $foto_dot->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto_dot->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }

            if ($foto_descarte) {
                $pathToFile = $foto_descarte->getPathname();
                $filename = $foto_descarte->getClientOriginalName();
                $message->attach($pathToFile, [
                    'as' => $filename, // Nome do arquivo que será mostrado no email
                    'mime' => $foto_descarte->getClientMimeType(), // Tipo MIME do arquivo
                ]);
            }}
        );
        return redirect()->route('descarte.index')->with('success', 'Formulário enviado com sucesso!');
    }



    

    /**
     * Display the specified resource.
     */
    public function show(Descarte_Pneus $descarte_Pneus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Descarte_Pneus $descarte_Pneus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Descarte_Pneus $descarte_Pneus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Descarte_Pneus $descarte_Pneus)
    {
        //
    }
}
