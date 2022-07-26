<?php

declare(strict_types=1);

namespace App\DTO\User;

use App\DTO\ClassDTOInterface;
use App\DTO\DTOInterface;
use App\Entity\User;

class SessionDTO implements ClassDTOInterface {
    public readonly int $id;
    public readonly string $email;
    public readonly string $username;

    public static function getClass(): string
    {
        return User::class;
    }
}
