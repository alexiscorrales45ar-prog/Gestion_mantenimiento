<?php
namespace app\Models;

class tecnico{
    private ?int $id;
    private string $nombre;
    private ?string $especialidades;
    private string $contacto;
    private string $estado;

    public function __construct(
        ?int $id,
        string $nombre,
        ?string $especialidades,
        string $contacto,
        string $estado = 'DISPONIBLE'
    ) {
        $this ->id = $id;
        $this ->nombre = $nombre;
        $this ->especialidades = $especialidades;
        $this ->contacto = $contacto;
        $this -> estado = $estado;

    }

    public function getId(): ?int {return $this->id;}
    public function getNombre():string {return $this->nombre;}
    public function getEspecialidades():?string {return $this->especialidades;}
    public function getContacto(): string {return $this->contacto;}
    public function getEstado(): string {return $this->estado;}
   
}