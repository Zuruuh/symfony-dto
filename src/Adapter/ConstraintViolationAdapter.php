<?php

declare(strict_types=1);

namespace App\Adapter;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationAdapter
{
    public static function toArray(ConstraintViolationListInterface $constraintViolationList): array
    {
        $errors = [];
        foreach ($constraintViolationList as $constraintViolation) {
            $errors[$constraintViolation->getPropertyPath()][] = $constraintViolation->getMessage();
        }

        return $errors;
    }
}
