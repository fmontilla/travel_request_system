<?php

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\User\Repositories\UserRepositoryInterface;
use Infrastructure\Persistence\Eloquent\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(string $name, string $email, string $hashedPassword, bool $isAdmin = false): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'is_admin' => $isAdmin,
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }
}

