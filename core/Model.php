<?php

namespace App\core;

use App\Database\Database;

class Model extends Cacher
{
    /**
     * Database object 
     *
     * @var Database
     */
    public Database $model;

    public string $tableName = "";

    public function initDB()
    {
        $this->model = new Database;
        return $this->model;
    }

    /**
     * Runs and executes SQL Statements 
     *
     * @param string $sql
     * @return array|bool|null
     */
    final public function runQuery(string $sql, bool $hasReturnValue = true, bool $multiple = false)
    {
        if (!strlen($sql)) {
            return null;
        }

        $stmt = $this->useModel()->query($sql);
        if ($hasReturnValue) {
            $results = $multiple ? $stmt->fetchAll() : $stmt->fetch();
            return $results;
        }
        return true;
    }

    public function makeCountQueriesOfTable(string $key, string $value)
    {
        $pdo = $this->useModel();
        $stmt = $pdo->prepare("SELECT count(*) FROM {$this->tableName} WHERE $key = :value");
        $stmt->execute(["value" => $value]);
        $fetchedRow = $stmt->fetch();
        $rowCount = $fetchedRow['count(*)'];
        return $rowCount;
    }

    public function getSingleItem(string $by, mixed $value, bool $multiple = false, array $what = []): array | bool
    {
        $what = count($what) ? implode(", ", $what) : "*";
        $stmt = $this->useModel()->prepare("SELECT $what FROM {$this->tableName} WHERE $by = ?");
        $stmt->execute([$value]);
        $record = !$multiple ? $stmt->fetch() : $stmt->fetchAll();
        return $record;
    }

    public function deleteItemFromTable(string $by, mixed $value)
    {
        $stmt = $this->useModel()->prepare("DELETE FROM $this->tableName WHERE $by = ?");
        $stmt->execute([$value]);
        return true;
    }

    /**
     * Returns the number of rows in a table
     *
     * @param string $tableName
     * @return integer|boolean
     */
    public function getTableRecordsCount(string $tableName = ""): int|bool
    {
        $tableName = strlen($tableName) ? $tableName : $this->tableName;

        $stmt = $this->useModel()->query("SELECT count(*) as records_count FROM $tableName");
        $count = $stmt->fetch();
        if ($stmt->rowCount()) {
            return $count['records_count'];
        }
        return false;
    }

    /**
     * Getter for model property
     *
     * @return object
     */
    final public function useModel(): object
    {
        return $this->initDB()->getDb();
    }

    /**
     * Will filter strings from unwanted characters
     *
     * @param mixed $value
     * @return string
     */
    final public function filter(mixed $value)
    {
        return trim(mb_convert_encoding(htmlspecialchars(html_entity_decode($value)), "utf-8"));
    }

    /**
     * Runs insert prepare statements
     *
     * @param array $data
     * @param string $tableName
     * @return bool|null
     */
    final public function doInsert(array $data, string $tableName = "")
    {
        $tableName = strlen($tableName) ? $tableName : $this->tableName;

        if (!count($data) || empty($tableName)) {
            return null;
        }

        # Gets column names and then turns it into comma values
        $columnNames = implode(", ", array_keys($data));

        # Gets prepare names(:name) and then turns it into comma values
        $prepareNames = [];
        foreach (array_keys($data) as $prepareName) {
            $prepareNames[] = ":$prepareName";
        }
        $prepareNames = implode(", ", $prepareNames);

        var_dump($prepareNames);
        var_dump($columnNames);

        # Performing the query
        $sqlStmt = "INSERT INTO $tableName($columnNames) VALUES($prepareNames)";
        $stmt = $this->initDB()->getDb()->prepare($sqlStmt);
        $stmt->execute($data);
        return true;
    }

    /**
     * Fetches All Data from database
     *
     * @param string $tableName
     * @return array|null
     */
    final public function fetchDataFromTable(string $tableName = ""): array|null
    {
        $tableName = strlen($tableName) ? $tableName : $this->tableName;

        $query = $this->initDB()->getDb()->query("SELECT * FROM $tableName");
        $results = $query->fetchAll();
        return $results;
    }
}
