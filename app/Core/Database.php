<?php

namespace app\core;

use PDO;
use PDOException;

class Database
{
    public PDO $pdo;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $dbName = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];

        $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            echo "Database connection failed! Error message: " . $e->getMessage();
            exit;
        }
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];
        $files = scandir(App::$ROOT_DIR . '/database/migrations');
        // [., .., '001_create_roles_table.php', '002_create_users_table.php']
        $toApplyMigrations = array_diff($files, $appliedMigrations);
        // echo "\$toApplyMigrations: ". PHP_EOL;
        // var_dump($toApplyMigrations);

        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === '..') {
                continue;
            }
            // $migration = "001_create_roles_table.php"
            require_once App::$ROOT_DIR . '/database/migrations/' . $migration;
            
            // "001_create_roles_table"
            $className = pathinfo($migration, PATHINFO_FILENAME);

            // --Convert something like 001_create_roles_table to CreateRolesTable--
            // [001, create, roles, table]
            $classNameParts = explode('_', $className);
            
            // [create, roles, table]
            array_shift($classNameParts); // remove 001
            
            // "CreateRolesTable"
            $classNameStr = implode('', array_map('ucfirst', $classNameParts));

            $instance = new $classNameStr();
            $this->log("Applying migration $migration");
            $instance->up($this->pdo);
            $this->log("Applied migration $migration");

            // newMigrations = ["001_create_roles_table.php", "002_create_users_table.php" , ...] After end of for loop
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        }
        else {
            $this->log("All migrations are applied");
        }
    }

    protected function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;");
    }

    protected function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    protected function saveMigrations(array $migrations)
    {   
        // "('001_create_roles_table.php'),('002_create_users_table.php'),('003_create_personal_access_tokens_table.php')"
        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES 
            $str
        ");
        $statement->execute();
    }

    protected function log($message)
    {   
        // [2026-03-08 15:11:51] - Applying migration 001_create_roles_table.php
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}