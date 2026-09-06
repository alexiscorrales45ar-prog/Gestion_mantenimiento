<?php

namespace app\Repositories;

use App\config\Database;
use app\Models\OrdenTrabajo;
use PDO;

class OrdenTrabajoReporsitory {
    private PDO $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    // Guardar una nueva orden de trabajo y actualiasar el estado de la solicitud a EN_PROCESO
    public function guardar(OrdenTrabajo $orden): bool {
        $stmt = $this->db->prepare("
            INSERT INTO ordenes_trabajo (solicitud_id, tecnico_id, estado)
            VALUES (:solicitud_id, :tecnico_id, :estado )
        ");
        $result = $stmt->execute([
            ':solicitud_id'=>$orden->getSolicitudId(),
            ':tecnico_id' =>$orden->getTecnicoId(),
            ':estado' => $orden->getEstado()
        ]);
        
        if ($result){
            //Actualizar el estado de la sollicitud correspondiente
            $stmtSolicitud = $this->db->prepare("UPDATE solicitudes SET estado = 'EN_PROCESO' WHERE id = :id");
            $stmtSolicitud->execute([':id'=>$orden->getSolicitudId()]);
        }

        return $result;
    }

    public function actualizarEjecucion( int $ordenId, string $estado, string $actividades, float $costo, string $fechaInicio, string $fechaFin): bool{
        $stmt = $this->db->prepare("
            UPDATE ordenes_trabajo
            SET estado = :estado,
                actividades= :estado,
                costo = : costo,
                fecha_inicio = : fecha_inicio,
                fecha_fin = : fecha_fin
            WHERE id = : id
            
        ");
        return $stmt->execute([
            ':estado'=>$estado,
            ':actividades'=>$actividades,
            ':costo'=>$costo,
            ':fecha_inicio'=>$fechaInicio,
            ':fecha_fin'=> $fechaFin,
            ':id'=>$ordenId
        ]);
    }
   
}