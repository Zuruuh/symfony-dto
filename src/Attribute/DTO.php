<?php

declare(strict_types=1);

namespace App\Attribute;

use App\DTO\ClassDTOInterface;
use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class DTO
{
    /** @param class-string<ClassDTOInterface> $dtoClass */
    public function __construct(public readonly string $dtoClass) {}
}
