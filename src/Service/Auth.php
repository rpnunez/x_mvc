<?php

namespace XMVC\Service;

use App\Http\Models\User;

class Auth
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function attempt($email, $password)
    {
        // This assumes a static 'where' method on the User model.
        // A proper repository or ORM would be a better long-term solution.
        $user = User::where('email', $email);

        if (!$user) {
            return false;
        }

        if (password_verify($password, $user->password)) {
            $this->session->set('user_id', $user->id);
            return true;
        }

        return false;
    }

    public function check()
    {
        return $this->session->has('user_id');
    }

    public function user()
    {
        if ($this->check()) {
            // This assumes a static 'find' method on the User model.
            return User::find($this->session->get('user_id'));
        }
        return null;
    }

    public function logout()
    {
        $this->session->forget('user_id');
    }
}