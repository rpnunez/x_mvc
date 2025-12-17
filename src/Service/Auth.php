<?php

namespace XMVC\Service;

use App\Http\Models\User;
use XMVC\Service\Session;

class Auth
{
    public static function attempt($email, $password)
    {
        $user = User::where('email', $email);

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            Session::start();
            Session::set('user_id', $user->id);
            return true;
        }

        return false;
    }

    public static function check()
    {
        Session::start();
        return Session::has('user_id');
    }

    public static function user()
    {
        if (static::check()) {
            return User::find(Session::get('user_id'));
        }
        return null;
    }

    public static function logout()
    {
        Session::start();
        Session::forget('user_id');
    }
}