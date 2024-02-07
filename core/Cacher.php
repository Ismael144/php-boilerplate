<?php

namespace App\core;

use Predis\Client;
use App\exceptions\cacher\CacheKeyExistsException;
use App\exceptions\cacher\InvalidDataTypeException;
use App\exceptions\cacher\UndefinedCacheKeyException;
use App\exceptions\cacher\CacheKeyDoesntExistException;

class Cacher
{
    /**
     * Contains the data types used to be used in this class
     *
     * @var array
     */
    protected array $dtypes = ["normal", "set", "list", "hash"];

    public function cacheConnect(): Client
    {
        try {
            $cacheDb = new Client(["host" => "localhost", "port" => 6379]);
            return $cacheDb;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Getter for Redis Client
     *
     * @return Client
     */
    public function getCacheDb(): Client
    {
        return $this->cacheConnect();
    }

    /**
     * Checks if a key exists in the cache
     *
     * @param string $key
     * @return boolean
     */
    final public function keyExists(string $key): bool
    {
        return $this->getCacheDb()->exists($key);
    }

    /**
     * Count cached data
     *
     * @param string $key
     * @return int|null
     * @throws UndefinedCacheKeyException | InvalidDataTypeException
     */
    final public function getDataCount($key, $dtype = "list"): UndefinedCacheKeyException|InvalidDataTypeException|int|null
    {
        if (!$this->keyExists($key)) {
            throw new UndefinedCacheKeyException($key);
        } else if (!in_array($dtype, $this->dtypes)) {
            throw new InvalidDataTypeException($dtype);
        }

        return match (strtolower($dtype)) {
            "list" => (int)$this->getCacheDb()->llen($key),
            "set" || "hash" => (int)count($this->fetchCachedData($key, $dtype))
        };
    }

    /**
     * Cache different data types depending on $dtype variable
     * #### Data types: hash, list, normal, set
     * 
     * @param string      $key
     * @param string      $dtype
     * @param string|null $field
     * @return mixed
     * @throws InvalidDataTypeException|UndefinedCacheKeyException
     */
    final public function fetchCachedData($key, $dtype = "normal", $field = null): mixed
    {
        if (!in_array($dtype, $this->dtypes)) {
            throw new InvalidDataTypeException($dtype);
        } elseif (!$this->keyExists($key)) {
            throw new UndefinedCacheKeyException($key);
        }

        $operations = match (strtolower($dtype)) {
            "normal" => $this->getCacheDb()->get($key),
            "list"   => $this->getCacheDb()->lrange($key, 0, -1),
            "hash"   => $this->getCacheDb()->hgetall($key),
            "set"    => $this->getCacheDb()->smembers($key),
        };

        return $operations;
    }

    /**
     * Caches data depending on the data type entered
     * #### Data types: hash, list, normal, set
     *
     * @param string $key
     * @param mixed  $data
     * @param string $dtype
     * @return int|null
     * @throws UndefinedCacheKeyException|InvalidDataTypeException
     */
    final function cacheData(string $key, mixed $data = null, string $dtype = "normal", $field = null): UndefinedCacheKeyException | InvalidDataTypeException | bool
    {
        if ($this->keyExists($key)) {
            throw new CacheKeyExistsException($key);
        } else if (!in_array($dtype, $this->dtypes)) {
            throw new InvalidDataTypeException($dtype);
        }

        $operations = match ($dtype) {
            "normal" => $this->getCacheDb()->set($key, $data),
            "list"   => $this->getCacheDb()->lpush($key, $data),
            "hash"   => $this->getCacheDb()->hset($key, $field, $data),
            "set"    => $this->getCacheDb()->smembers($key),
        };

        if ($operations) {
            return true;
        }

        return false;
    }

    /**
     * Deletes cached data
     *
     * @param string $key
     * @param array  $data
     * @return bool
     * @throws CacheKeyDoesntExistException
     */
    final function deleteCachedData(string $key): bool|null
    {
        if ($this->keyExists($key)) {
            $this->getCacheDb()->del($key);
            return true;
        }
        throw new CacheKeyDoesntExistException($key);
    }

    /**
     * Updates data on corresponding key
     *
     * @param string $key
     * @param mixed  $data
     * @param string $dtype
     * @param string $field
     * @return bool
     * @throws UndefinedCacheKeyException|InvalidDataTypeException
     */
    final function updateCachedData(string $key, mixed $data = null, string $dtype = "normal", string $field = null): UndefinedCacheKeyException | InvalidDataTypeException | bool
    {
        if (!$this->keyExists($key)) {
            throw new UndefinedCacheKeyException($key);
        } else if (!in_array($dtype, $this->dtypes)) {
            throw new InvalidDataTypeException($dtype);
        }

        $delOperation = $this->getCacheDb()->del($key);

        $operation = $this->cacheData($key, $data, $dtype, $field);

        if ($operation && $delOperation) {
            return true;
        }
        return false;
    }
}
