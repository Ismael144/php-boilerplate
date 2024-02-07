<?php 

namespace App\exceptions\cacher; 

use Exception;

class CacheKeyExistsException extends Exception 
{
    public function __construct(string $key)
    {   
        $this->message = "The cache key '$key' already exists.";
    }
}