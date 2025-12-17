<?php

namespace App\Http\Models;

class User extends Model
{
    // Table name is automatically inferred as 'users'

    public static function create(array $data)
    {
        if (isset($data['password'])) {
            // Password hashing is already handled in AuthController,
            // but we could enforce it here if we wanted to be strict.
            // For now, we'll assume the controller handles it or we can add a mutator logic later.
        }
        return parent::create($data);
    }
}