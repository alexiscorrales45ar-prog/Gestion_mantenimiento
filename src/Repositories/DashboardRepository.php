<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;

class DashboardRepository{
    private PDO $db;
    
    public function __construct(){
        $this->db = Database::getInstance()->getConnection();

    }

    // consultar el conteo de solicitudes agrupadas por estado

    public function obtenerTotalesPorEstado():array{
        $sql = "SELECT estado, COUNT(*) as total FROM solicitudes GROUP BY estado";
        $stmt = $this->db->query($sql);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totales =[
            'Pendiente' => 0,
            'En Proceso'=> 0,
            'Finalizada'=>0,
            'Cancelada'=>0
        ];
        foreach ($filas as $fila){
            $estado = $fila['estado'];
            if (array_key_exists($estado, $totales)){
                $totales[$estado] = (int)$fila['total'];
            }
        }
        return $totales;
    }

    // calcular la suma de costos estimados de las solicitudes finalizadas

    public function obtenerUltimasSolicitudes(int $limite = 5): array{
        $sql = "SELECT s.id,
                       s.codigo_seguimiento,
                       s.descripcion_falla AS descripcion_problema,
                       s.prioridad,
                       s.estado,
                       s.fecha_creacion,
                       c.nombre AS cliente_nombre,
                       e.nombre AS equipo_nombre
                FROM solicitudes s
                LEFT JOIN clientes c ON cliente_id = c.id
                LEFT JOIN equipo e ON s.equipo_id = e.id
                ORDER BY s.id DESC
                LIMIT :limite";
        $stmt = $this->db-> prepare($sql);
        $stmt->bindValue(':limite',$limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}