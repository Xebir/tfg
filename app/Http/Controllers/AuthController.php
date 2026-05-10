<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // TODO: validar credenciales y autenticar
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // TODO: validar datos, crear usuario y autenticar
    }

    public function logout(Request $request)
    {
        // TODO: cerrar sesión y redirigir a HOME
    }
}
