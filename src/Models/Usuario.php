<?php

namespace app\Models;

class Usuario{
    private ?int $id;
    private string $nombre;
    private string $correo;
    private string $password;
    private string $rol;

    public function __construct( ?int $id, string $nombre, string $correo, string $password, string $rol){
        $this->id = $id;
        $this->nombre=$nombre;
        $this->correo=$correo;
        $this->password=$password;
        $this->rol=$rol;
    }

    public function getId():?int{ return $this->id;}
    public function getNombre(): string {return $this->nombre;}
    public function getCorreo():string {return $this->correo;}
    public function getPassword():string {return $this->password;}
    public function getRol(): string { return $this->rol;}


    
}
