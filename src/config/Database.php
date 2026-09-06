<?php

namespace App\config;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;
    private ?PDO $conn = null;
    private string $dbFile;

    private function __construct() {
        // Ruta del archivo de base de datos SQLite dentro del proyecto
        $this->dbFile = __DIR__ . '/../../database.sqlite';
        
        try {
            $this->conn = new PDO("sqlite:" . $this->dbFile);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->inicializarTablas();
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    // Patrón Singleton para obtener la única instancia de la clase
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Obtener el objeto de conexión PDO nativo
    public function getConnection(): ?PDO {
        return $this->conn;
    }

    // Inicializar y crear tablas automáticamente si no existen
    private function inicializarTablas(): void {
        $sqlClientes = "
            CREATE TABLE IF NOT EXISTS clientes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                telefono TEXT NOT NULL,
                email TEXT,
                direccion TEXT
            );
        ";

        $sqlEquipos = "
            CREATE TABLE IF NOT EXISTS equipos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cliente_id INTEGER NOT NULL,
                nombre TEXT NOT NULL,
                modelo TEXT,
                numero_serie TEXT,
                FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
            );
        ";

        $sqlSolicitudes = "
            CREATE TABLE IF NOT EXISTS solicitudes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                equipo_id INTEGER NOT NULL,
                descripcion_problema TEXT NOT NULL,
                prioridad TEXT CHECK(prioridad IN ('BAJA', 'MEDIA', 'ALTA')) DEFAULT 'MEDIA',
                estado TEXT CHECK(estado IN ('PENDIENTE', 'EN_PROCESO', 'FINALIZADA')) DEFAULT 'PENDIENTE',
                fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE
            );
        ";

        $sqlTecnicos = "
            CREATE TABLE IF NOT EXISTS tecnicos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                especialidad TEXT,
                contacto TEXT NOT NULL,
                estado TEXT CHECK(estado IN ('DISPONIBLE', 'OCUPADO')) DEFAULT 'DISPONIBLE'
            );
        ";

        $sqlOrdenes = "
            CREATE TABLE IF NOT EXISTS ordenes_trabajo (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                solicitud_id INTEGER NOT NULL,
                tecnico_id INTEGER NOT NULL,
                fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                estado TEXT CHECK(estado IN ('ASIGNADA', 'EN_PROCESO', 'FINALIZADA')) DEFAULT 'ASIGNADA',
                FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id) ON DELETE CASCADE,
                FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id) ON DELETE RESTRICT
            );
        ";

        // Ejecutar la creación de todas las tablas en orden de dependencias
        $this->conn->exec($sqlClientes);
        $this->conn->exec($sqlEquipos);
        $this->conn->exec($sqlSolicitudes);
        $this->conn->exec($sqlTecnicos);
        $this->conn->exec($sqlOrdenes);
    }
}


?>