<?php

declare(strict_types=1);

namespace App\DTO;

interface ClassDTOInterface extends DTOInterface
{
    /**
     * @return class-string
     */
    public static function getClass(): string;
}
