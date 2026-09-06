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
   
}