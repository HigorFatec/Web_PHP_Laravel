<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Filial;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        // Validação dos dados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|digits:11|unique:users', // Validação do CPF com 11 dígitos
            'filial' => 'required|string',
            'password' => 'required|string|min:8|confirmed', // Verifica se 'password_confirmation' corresponde
        ]);

        $user = $request->all();
        $user['password'] = bcrypt($user['password']);
        $user = User::create($user);

        Auth::login($user);

        $filiais = Filial::all();

        return redirect()->route('reserva.home')->with('success', 'Cadastro realizado com sucesso!');
    }


}
