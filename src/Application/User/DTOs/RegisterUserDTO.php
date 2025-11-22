<?php

namespace Application\User\DTOs;

class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public bool $isAdmin = false
    ) {
    }
}

