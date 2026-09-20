<?php

namespace app\Repositories;

use App\Models\Solicitud;
use PDO;

class SolicitudRepository{
    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    // Guardar una vueva solicitud de mantenimiento
    public function guardar(Solicitud $solicitud): bool {
        $sql = "INSERT INTO solicitudes (equipo_id, descripcion_problema, prioridad, estado)
                VALUES (:equipo_id, :descripcion_problema, :prioridad, :estado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':equipo_id'               =>$solicitud->getEquipoId(),
            ':descripcion_problema'     =>$solicitud->getDescripcionProblema(),
            ':prioridad'                =>$solicitud->getPrioridad(),
            ':estado'                  =>$solicitud->getEstado()
        ]);
    }

    // obtener lsita de quipos para llenar el selector en la formulario html
    public function obtenerEquiposConclientesU(): array{
        $sql = "SELECT e.id, e.nombre AS equipo_nombre, c.nombre AS cliente_nombre
                FROM equipos e
                JOIN cliente c ON e.clientge_id = c.id
                ORDER BY e.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchALL();
    }
   

}
