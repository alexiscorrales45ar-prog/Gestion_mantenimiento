<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private ?PDO $conn = null;

    public function getConnetion():PDO{
        if ($this->conn == null){
            try{
                // crea o se conecta al archivo de base de datos dentor de la carpeta config
                $dbpath = __DIR__ . '/mantenimiento.db';
                $this->conn = new PDO("sqlite:". $dbpath);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                //ACTIVAR LLAVES FORANEAS EN SQLite

                $this->conn->exec("PRAGMA foreing_keys = ON;");

                //crear la tablas del caso de Uso 1 automaticamente si no exiten
                $this->inicializarTabla();
            } catch (PDOException $e){
                die("Error de conexión a SQLite: " . $e->getMessage());
            }
        }
        return $this->conn;
    }
    private function inicializarTabla(): void{  
        //tablas para RF01:cliente y equipos (caso de uso 1)
        $sqlCliente = "
            CREATE TABLE IF NOT EXISTS cliente (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre  TEXT NOT NULL,
                contacto TEXT NOT NULL,
                direccion TEXT,
                creado_en DATETINE DEFAULT CURRENT_TIMESTAMP
            );
        
        ";

        $sqlEquipo = "
            CREATE TABLE IF NOT EXITS equipos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cliente_id INTEGER NOT NULL,
                nombre TEXT NOT NULL,
                marca TEXT,
                modelo TEXT,
                serie TEXT,
                creado_en DATETIME DEFAULT CURRENT_TIMETAMP,
                FOREING KEY (cliente_id) REFERENCES cliente(id) ON DELETE CASCADE
            );
        ";

        $this->conn->exec($sqlCliente);
        $this->conn->exec($sqlEquipo);
    }



}



?>