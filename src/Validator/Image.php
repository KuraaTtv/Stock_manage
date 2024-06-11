<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Image extends Constraint
{
    
    public $message = 'The image dimensions are not valid. Required dimensions are 320x213 pixels.';

    public $width = 320;
    public $height = 213;

    public function getRequiredOptions():array
    {
        return ['width', 'height'];
    }

    

    


    
    


    
}
