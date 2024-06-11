<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ImageValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var Image $constraint */

        

        if (!$constraint instanceof Image) {
            throw new UnexpectedTypeException($constraint, Image::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            throw new UnexpectedValueException($value, 'Symfony\Component\HttpFoundation\File\UploadedFile');
        }

        
        $imageSize = getimagesize($value->getPathname());
        if ($imageSize[0] !== $constraint->width || $imageSize[1] !== $constraint->height) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ width }}', $constraint->width)
                ->setParameter('{{ height }}', $constraint->height)
                ->addViolation();
        }

       
    }
}


 // // TODO: implement the validation here
        // $this->context->buildViolation($constraint->message)
        //     ->setParameter('{{ value }}', $value)
        //     ->addViolation();
