<?php 

namespace App\exceptions\cacher; 

use Exception;

class CacheKeyDoesntExistException extends Exception 
{
    public function __construct(string $key)
    {   
        $this->message = "The cache key '$key' referenced to does not exist.";
    }
}