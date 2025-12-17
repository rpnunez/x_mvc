<?php

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        return new Response($this->view('auth/login'));
    }
}