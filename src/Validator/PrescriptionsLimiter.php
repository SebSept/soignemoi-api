<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[Attribute(Attribute::TARGET_CLASS)]
class PrescriptionsLimiter extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message = 'La création de cet objet est limitée à 1 par jour par patient et par docteur';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
