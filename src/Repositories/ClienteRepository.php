<?php
namespace App\Repositories;

use App\Models\Cliente;
use App\Models\Equipo;
use PDO;

class ClienteRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Buscar cliente por teléfono o cédula
    public function buscarPorTelefonoODocumento(string $criterio): ?array {
        $sql = "SELECT id, nombre, cedula, telefono, direccion FROM clientes WHERE cedula = :criterio OR telefono = :criterio LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':criterio' => $criterio]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    // Guardar cliente nuevo y retornar su ID
    public function guardarCliente(Cliente $cliente): int {
        $sql = "INSERT INTO clientes (nombre, cedula, telefono, direccion) VALUES (:nombre, :cedula, :telefono, :direccion)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre'    => $cliente->getNombre(),
            ':cedula'    =>$cliente->getCedula(),
            ':telefono'  => $cliente->getContacto(), // mapea el teléfono/contacto
            ':direccion' => $cliente->getDireccion()
        ]);
        return (int) $this->db->lastInsertId();
    }

    // Guardar equipo asociado a un cliente_id
    public function guardarEquipo(Equipo $equipo): int {
        $sql = "INSERT INTO equipos (cliente_id, nombre, marca, modelo, serie) VALUES (:cliente_id, :nombre, :marca, :modelo, :serie)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cliente_id' => $equipo->getClienteId(),
            ':nombre'     => $equipo->getNombre(),
            ':marca'      => $equipo->getMarca(),
            ':modelo'     => $equipo->getModelo(),
            ':serie'      => $equipo->getSerie()
        ]);
        return (int) $this->db->lastInsertId();
    }
}