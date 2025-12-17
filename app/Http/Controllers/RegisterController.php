<?php

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        return new Response($this->view('auth/register'));
    }
}