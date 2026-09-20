<?php

namespace app\Models;

class Cliente{
    private ?int $id;
    private string $nombre;
    private string $cedula;
    private string $contacto;
    private ?string $direccion;

    public function __construct(?int $id, string $nombre, string $cedula, string $contacto, string $direccion = null){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->cedula = $cedula;
        $this->contacto = $contacto;
        $this->direccion =$direccion;    

    }

    public function getId():?int {return $this->id; }
    public function getNombre():string {return $this->nombre; }
    public function getCedula(): string {return $this->cedula;}
    public function getContacto():string {return $this->contacto; }
    public function getdireccion():?string{return $this->direccion; }

      


}





?>
