<?php

declare(strict_types=1);

namespace App\DTO\User;

class LoginDTO
{
    public readonly string $usernameOrEmail;
    public readonly string $password;
}
