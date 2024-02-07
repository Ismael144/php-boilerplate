<?php 

namespace App\exceptions\validation; 

class InvalidValidationRuleException extends \Exception 
{
    public function __construct(string $rule)
    {
        $this->message = "Key '$rule' is not a valid validation rule";
    }
}