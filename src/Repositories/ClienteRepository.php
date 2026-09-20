<?php
namespace app\Repositories;

use App\Models\Cliente;
use App\Models\Equipo;
use PDO;
use ReturnTypeWillChange;

class ClienteRepository{
    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }
    //Guardar el client en la tabla "cliente" y vuelva su ID gerado

    public function guardarCliente(Cliente $cliente): int {
        $slq = "INSERT INTO clientes (nombre, contacto, direccion) VALUES (:nombre, :contacto. :direccion)";
        $stmt = $this->db->prepare($slq);
        $stmt->execute([
            ':nombre' => $cliente->getNombre(),
            ':contacto' => $cliente->getContacto(),
            ':direccion' =>$cliente->getdireccion()   
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function guardarEquipo(Equipo $equipo): bool {
        $sql = "INSERT INTO equipos (cliente_id, nombre, marca, modelo, serie)
                VALUES (:cliente_id, :nombre, :mcarca, :modelo, :serie)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cliente_id' =>$equipo->getClienteId(),
            ':nombre' =>$equipo->getNombre(),
            ':marca' => $equipo->getMarca(),
            ':serie' =>$equipo->getSerie()

        ]);

    }

    // consutlar todos los clientes registrados
    public function obtenertODOS():array {
        $stmt = $this->db->query("SELECT * FORM cliente ORDER BY id DESC");
        return $stmt ->fetchAll();
    }
    
}





?>