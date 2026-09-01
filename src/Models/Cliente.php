<?php

namespace app\Models;

class Cliente{
    private ?int $id;
    private string $nombre;
    private string $contacto;
    private ?string $direccion;

    public function __construct(?int $id, string $nombre, string $contacto, string $direccion = null){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->contacto = $contacto;
        $this->direccion =$direccion;    

    }

    public function getId():?int {return $this->id; }
    public function getNombre():string {return $this->nombre; }
    public function getContacto():string {return $this->contacto; }
    public function getdireccion():?string{return $this->direccion; }

      


}





?>
