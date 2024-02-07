<?php 

namespace App\interfaces; 

interface ControllerInterface 
{
    public function getTableData(): array; 
    public function getTableRecordsCount(): int|bool;
    public function getTableName(): string; 
}