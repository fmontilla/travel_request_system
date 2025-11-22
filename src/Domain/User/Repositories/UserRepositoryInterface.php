<?php

namespace Domain\User\Repositories;

use Infrastructure\Persistence\Eloquent\Models\User;

interface UserRepositoryInterface
{
    public function create(string $name, string $email, string $hashedPassword, bool $isAdmin = false): User;
    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;
    public function emailExists(string $email): bool;
}

