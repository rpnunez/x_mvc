<?php

use XMVC\Service\Router;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;

Router::get('', [HomeController::class, 'index']);
Router::get('users/{id}', [UserController::class, 'show']);
Router::get('profile', [ProfileController::class, 'index'])->middleware('auth');

// Auth Routes
Router::get('login', [LoginController::class, 'index']);
Router::post('login', [AuthController::class, 'login']);
Router::get('register', [RegisterController::class, 'index']);
Router::post('register', [AuthController::class, 'register']);
Router::post('logout', [AuthController::class, 'logout']);