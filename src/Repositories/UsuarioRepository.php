<?php

namespace app\Repositories;

use App\config\Database;
use PDO;

class UsuarioRepository{
    private PDO $db;

    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    public function buscarCorreo (string $correo): ?array{
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->execute([$correo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return  $resultado ?: null;
    }

    public function registrar(string $nombre, string $correo, string $passwordHash, string $rol):bool {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?,?,?,?)");
        return $stmt->execute([$nombre,$correo,$passwordHash, $rol]);
    }
   
}