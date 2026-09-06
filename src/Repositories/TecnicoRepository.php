<?php
namespace App\Repositories;

use App\config\Database;
use App\Models\Tecnico;
use PDO;

class TecnicoRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Obtener todos los técnicos disponibles
    public function obtenerDisponibles(): array {
        $stmt = $this->db->prepare("SELECT * FROM tecnicos WHERE estado = 'DISPONIBLE'");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tecnicos = [];
        foreach ($rows as $row) {
            $tecnicos[] = new Tecnico(
                (int)$row['id'],
                $row['nombre'],
                $row['especialidad'],
                $row['contacto'],
                $row['estado']
            );
        }
        return $tecnicos;
    }
}

