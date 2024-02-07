<?php

namespace App\Database;

use PDOException, PDO;

use App\constants\DBCredentialsEnum;

use App\constants\FileDirPathEnum;

class Database
{
    /**
     * Database object
     *
     * @var PDO
     */
    protected PDO $db;

    public string $sqlVersion = "";

    readonly protected string $backUpDir;

    public function __construct(
        private string $dbName = DBCredentialsEnum::DBNAME->value,
        private string $dbHost = DBCredentialsEnum::DBHOST->value,
        private string $username = DBCredentialsEnum::USERNAME->value,
        private string $password = DBCredentialsEnum::PASSWORD->value,
    ) {
        $this->backUpDir = FileDirPathEnum::PATH_TO_BKUP_DIR->value;
        try {
            $dsn = "mysql:host={$dbHost};dbname={$dbName}";
            $this->db = new PDO($dsn, $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            # Getting SQL Version
            $this->sqlVersion = $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Getter for the database object
     *
     * @return PDO
     */
    public function getDb(): PDO
    {
        return $this->db;
    }

    /**
     * Will sql database backup
     *
     * @return string
     */
    public function makeDBBackUp(): string
    {
        # This prepares the backup folder.
        $this->resetDBBackUpDir();

        $backupFileName = $this->dbName . '_backup_' . date('Y-m-d') . uniqid('_') . '.sql';
        $backupPath = FileDirPathEnum::PATH_TO_BKUP_DIR->value;
        $backupFile = $backupPath . $backupFileName;
        $mysqlDumpPath = FileDirPathEnum::PATH_TO_MYSQLDUMP->value;

        $command =  $mysqlDumpPath . "/mysqldump.exe --opt --user=$this->username --host=" . $this->dbHost . " " . $this->dbName . " > $backupFile";

        system($command);
        return $backupFile;
    }

    /**
     * Check if the database backup is the latest
     * if not, it is deleted
     *
     * @return void
     */
    private function resetDBBackUpDir()
    {
        foreach (scandir($this->backUpDir) as $file) {
            $completeFile = $this->backUpDir . $file;
            if (is_dir($completeFile)) {
                continue;
            }

            unlink($completeFile);
        }
    }
}
