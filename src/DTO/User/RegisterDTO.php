<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;
use App\DTO\ClassDTOInterface;
use App\Entity\User;

class RegisterDTO implements ClassDTOInterface
{
    #[Assert\NotBlank()]
    public readonly string $username;

    #[Assert\NotBlank()]
    #[Assert\Email()]
    public readonly string $email;

    #[Assert\NotBlank()]
    public readonly string $password;

    public static final function getClass(): string
    {
        return User::class;
    }
}
