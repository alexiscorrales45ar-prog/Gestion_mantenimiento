<?php
namespace App\Repositories;

use App\Models\Solicitud;
use PDO;

class SolicitudRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Obtener los equipos asociados a un cliente
    public function obtenerEquiposPorCliente(int $clienteId): array {
        $sql = "SELECT id, nombre, marca, modelo, serie FROM equipos WHERE cliente_id = :cliente_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cliente_id' => $clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Guardar solicitud en la BD
    public function guardarSolicitud(Solicitud $solicitud): int {
        $sql = "INSERT INTO solicitudes (codigo_seguimiento, cliente_id, equipo_id, descripcion_falla, prioridad, estado) 
                VALUES (:codigo, :cliente_id, :equipo_id, :falla, :prioridad, :estado)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':codigo'     => $solicitud->getCodigoSeguimiento(),
            ':cliente_id' => $solicitud->getClienteId(),
            ':equipo_id'  => $solicitud->getEquipoId(),
            ':falla'      => $solicitud->getDescripcionFalla(),
            ':prioridad'  => $solicitud->getPrioridad(),
            ':estado'     => $solicitud->getEstado()
        ]);
        return (int) $this->db->lastInsertId();
    }

    // Consultar por código único
    public function buscarPorCodigo(string $codigo): ?array {
        $sql = "SELECT s.*, c.nombre AS cliente_nombre, c.cedula, c.telefono, e.nombre AS equipo_nombre, e.marca, e.modelo 
                FROM solicitudes s
                INNER JOIN clientes c ON s.cliente_id = c.id
                INNER JOIN equipos e ON s.equipo_id = e.id
                WHERE s.codigo_seguimiento = :codigo LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }
}