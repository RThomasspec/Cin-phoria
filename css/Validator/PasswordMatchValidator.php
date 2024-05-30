<?php

// src/Validator/PasswordMatchValidator.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use App\Entity\Utilisateur;

class PasswordMatchValidator extends ConstraintValidator
{
    public function validate($object, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordMatch) {
            throw new UnexpectedTypeException($constraint, PasswordMatch::class);
        }

        if (!$object instanceof Utilisateur) {
            throw new UnexpectedValueException($object, Utilisateur::class);
        }

    }
}
