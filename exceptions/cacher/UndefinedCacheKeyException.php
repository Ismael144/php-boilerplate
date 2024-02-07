<?php

namespace App\exceptions\cacher;

use Exception;

class UndefinedCacheKeyException extends Exception
{
    public function __construct(string $key)
    {
        $this->message = "The key '$key' referenced to, does not exist.";
    }
}
