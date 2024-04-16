<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\Validator;

use App\Entity\Doctor;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserIsDoctorValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * Le docteur choisi (pour la prescription, l'avis) ($value) correspond aux docteur identifié.
     *
     * @param Doctor $value
     */
    public function validate($value, Constraint $constraint): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($value !== $user->getDoctor()) {
            /* @var UserIsDoctor $constraint */
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
