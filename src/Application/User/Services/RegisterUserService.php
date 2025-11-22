<?php

namespace Application\User\Services;

use Application\User\DTOs\RegisterUserDTO;
use Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Infrastructure\Persistence\Eloquent\Models\User;

class RegisterUserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(RegisterUserDTO $dto): User
    {
        if ($this->userRepository->emailExists($dto->email)) {
            throw new \InvalidArgumentException('Email already registered');
        }

        $hashedPassword = Hash::make($dto->password);

        $user = $this->userRepository->create(
            $dto->name,
            $dto->email,
            $hashedPassword,
            $dto->isAdmin
        );

        return $user;
    }
}

