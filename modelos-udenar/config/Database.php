<?php
// config/Database.php

class Database
{
    private $host;
    private $user;
    private $pass;
    private $dbName;
    private $conn;

    public function __construct()
    {
        // Usamos __DIR__ para que la ruta sea relativa al archivo actual
        // Esto soluciona el problema de Windows vs Linux
        $configPath = __DIR__ . '/Config.php';

        if (file_exists($configPath)) {
            require_once $configPath;
            $this->host = DB_HOST;
            $this->user = DB_USER;
            $this->pass = DB_PASS;
            $this->dbName = DB_NAME;
        } else {
            die("Error crítico: El archivo de configuración no existe en $configPath");
        }
    }

    /**
     * Retorna la instancia de conexión única (Patrón similar a Singleton)
     */
    public function getConnection()
    {
        if ($this->conn === null) {
            try {
                $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbName);

                if ($this->conn->connect_error) {
                    throw new Exception('Error de conexión: ' . $this->conn->connect_error);
                }

                // Configuración de caracteres
                $this->conn->set_charset('utf8mb4');
            } catch (Exception $e) {
                // En producción podrías loguear esto en lugar de mostrarlo
                die('Error de Base de Datos: ' . $e->getMessage());
            }
        }
        return $this->conn;
    }
}
