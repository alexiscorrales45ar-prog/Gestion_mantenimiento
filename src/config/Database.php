<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;
    private ?PDO $conn = null;
    private string $dbFile;

    private function __construct() {
        $this->dbFile = __DIR__ . '/../../database.sqlite';
        
        try {
            $this->conn = new PDO("sqlite:" . $this->dbFile);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->inicializarTablas();
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): ?PDO {
        return $this->conn;
    }

    private function inicializarTablas(): void {
        $sqlClientes = "
            CREATE TABLE IF NOT EXISTS clientes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                cedula TEXT UNIQUE NOT NULL,
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
                descripcion_falla TEXT NOT NULL,
                prioridad TEXT DEFAULT 'Media',
                estado TEXT DEFAULT 'Pendiente',
                diagnostico TEXT,
                costo_estimado REAL DEFAULT 0.0,
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
                estado TEXT DEFAULT 'DISPONIBLE'
            );
        ";

        $sqlOrdenes = "
            CREATE TABLE IF NOT EXISTS ordenes_trabajo (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                solicitud_id INTEGER NOT NULL,
                tecnico_id INTEGER NOT NULL,
                fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
                estado TEXT DEFAULT 'ASIGNADA',
                actividades TEXT,
                costo REAL DEFAULT 0.0,
                fecha_inicio DATETIME,
                fecha_fin DATETIME,
                FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id) ON DELETE CASCADE,
                FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id) ON DELETE RESTRICT
            );
        ";

        $sqlUsuario = "
            CREATE TABLE IF NOT EXISTS usuario (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                correo TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                rol TEXT NOT NULL CHECK(rol IN ('admin', 'recepcionista', 'tecnico', 'supervisor', 'cliente'))
            );
        ";

        $this->conn->exec($sqlClientes);
        $this->conn->exec($sqlEquipos);
        $this->conn->exec($sqlSolicitudes);
        $this->conn->exec($sqlTecnicos);
        $this->conn->exec($sqlOrdenes);
        $this->conn->exec($sqlUsuario);
    }
}