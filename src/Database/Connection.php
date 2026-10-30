<?php

namespace App\Database;

use PDO;
use PDOException;

class Connection {
    // Instancia única de la clase (Patrón Singleton)
    private static ?Connection $instance = null;
    private ?PDO $connection = null;

    // Configuración de la base de datos (puedes adaptarla según tu entorno)
    private string $host = 'localhost';
    private string $db   = 'laboratorio_pedidos';
    private string $user = 'root';
    private string $pass = '';
    private string $charset = 'utf8mb4';

    // Constructor privado para evitar que se instancie externamente con `new Connection()`
    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // No silenciar errores, lanzarlos
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Manejo adecuado de la excepción de conexión sin silenciarla
            throw new PDOException("Error de conexión a la base de datos: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    // Método estático global para obtener la única instancia de la clase
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Método para obtener el objeto PDO y realizar consultas
    public function getConnection(): PDO {
        return $this->connection;
    }

    // Prevenir la clonación de la instancia
    private function __clone() {}

    // Prevenir la deserialización de la instancia
    public function __wakeup() {
        throw new \Exception("No se puede unserialize una instancia de Singleton.");
    }
}