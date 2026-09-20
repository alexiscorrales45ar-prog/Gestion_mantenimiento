<?php
namespace App\Repositories;

use App\Config\Database;
use App\Models\OrdenTrabajo;
use PDO;

class OrdenTrabajoRepository {
    private PDO $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    // Guardar una nueva orden de trabajo y actualizar el estado de la solicitud a EN_PROCESO
    public function guardar(OrdenTrabajo $orden): bool {
        $stmt = $this->db->prepare("
            INSERT INTO ordenes_trabajo (solicitud_id, tecnico_id, estado)
            VALUES (:solicitud_id, :tecnico_id, :estado)
        ");
        $result = $stmt->execute([
            ':solicitud_id' => $orden->getSolicitudId(),
            ':tecnico_id'   => $orden->getTecnicoId(),
            ':estado'       => $orden->getEstado()
        ]);
        
        if ($result){
            // Actualizar el estado de la solicitud correspondiente
            $stmtSolicitud = $this->db->prepare("UPDATE solicitudes SET estado = 'EN_PROCESO' WHERE id = :id");
            $stmtSolicitud->execute([':id' => $orden->getSolicitudId()]);
        }

        return $result;
    }

    public function actualizarEjecucion(int $ordenId, string $estado, string $actividades, float $costo, string $fechaInicio, string $fechaFin): bool {
        $stmt = $this->db->prepare("
            UPDATE ordenes_trabajo
            SET estado = :estado,
                actividades = :actividades,
                costo = :costo,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin
            WHERE id = :id
        ");
        return $stmt->execute([
            ':estado'       => $estado,
            ':actividades'  => $actividades,
            ':costo'        => $costo,
            ':fecha_inicio' => $fechaInicio,
            ':fecha_fin'    => $fechaFin,
            ':id'           => $ordenId
        ]);
    }
    
    public function obtenerHistorialReportes(string $filtro = ''): array {
        $sql = "
            SELECT ot.id as orden_id, ot.estado, ot.actividades, ot.costo, ot.fecha_inicio, ot.fecha_fin,
                   s.descripcion_problema as problema, s.prioridad,
                   c.nombre as cliente_nombre,
                   e.nombre as equipo_nombre, e.modelo as equipo_modelo,
                   t.nombre as tecnico_nombre
            FROM ordenes_trabajo ot
            JOIN solicitudes s ON ot.solicitud_id = s.id
            JOIN clientes c ON s.cliente_id = c.id
            JOIN equipos e ON s.equipo_id = e.id
            JOIN tecnicos t ON ot.tecnico_id = t.id
        ";
        
        if (!empty($filtro)){
            $sql .= " WHERE c.nombre LIKE :filtro OR e.nombre LIKE :filtro OR t.nombre LIKE :filtro OR ot.estado LIKE :filtro ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':filtro' => "%$filtro%"]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>