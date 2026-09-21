<?php
namespace App\Repositories;

use PDO;

class TecnicoRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function obtenerSolicitudes(): array {
        $sql = "SELECT s.*, 
                       c.nombre AS cliente_nombre, 
                       c.telefono AS cliente_telefono, 
                       e.nombre AS equipo_nombre, 
                       e.marca, 
                       e.modelo, 
                       e.serie
                FROM solicitudes s
                INNER JOIN clientes c ON s.cliente_id = c.id
                INNER JOIN equipos e ON s.equipo_id = e.id
                ORDER BY s.id DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarOrden(int $solicitudId, string $estado, string $diagnostico, float $costo): bool {
        $sql = "UPDATE solicitudes 
                SET estado = :estado, 
                    diagnostico = :diagnostico, 
                    costo_estimado = :costo
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':estado'      => $estado,
            ':diagnostico' => $diagnostico,
            ':costo'       => $costo,
            ':id'          => $solicitudId
        ]);
    }
}