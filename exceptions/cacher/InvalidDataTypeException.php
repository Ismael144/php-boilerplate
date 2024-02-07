<?php 

namespace App\exceptions\cacher; 

use Exception;

class InvalidDataTypeException extends Exception 
{
    public function __construct(string $dtype)
    {   
        $this->message = "The data type '$dtype' does not exist";
    }
}