<?php

declare(strict_types=1);

/*
 * SoigneMoi API - Projet ECF
 *
 * @author Sébastien Monterisi <sebastienmonterisi@gmail.com>
 * 2024
 */

namespace App\Validator;

use Attribute;
use Override;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_CLASS)]
class MedicalOpinionDateUnchanged extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message = 'La création de cet objet est limitée à 1 par jour par patient et par docteur';

    #[Override]
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
