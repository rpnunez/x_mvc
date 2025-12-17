<?php

namespace App\Http\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Models\User;
use XMVC\Service\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (Auth::attempt($email, $password)) {
            return new Response("Login successful! Redirecting...", 302, ['Location' => '/profile']);
        }

        return new Response("Invalid credentials", 401);
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        // Basic validation
        if (!$name || !$email || !$password) {
            return new Response("All fields are required", 400);
        }

        // Check if user already exists
        if (User::where('email', $email)) {
            return new Response("User already exists", 400);
        }

        // Create user
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        return new Response("Registration successful! Please login.", 302, ['Location' => '/login']);
    }

    public function logout()
    {
        Auth::logout();
        return new Response("Logged out", 302, ['Location' => '/login']);
    }
}