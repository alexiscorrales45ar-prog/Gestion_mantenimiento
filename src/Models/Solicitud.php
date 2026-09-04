<?php
namespace app\Models;

class Solicitud{
    private ?int $id;
    private int $equipoId;
    private string $descripcionProblema;
    private string $prioridad;
    private string $estado;
    private ?string $fechaSolicitud;

    public function __construct(
        ?int $id,
        int $equipoId,
        string $descripcionProblema,
        string $prioridad = 'MEDIA',
        string $estado = 'PENDIENTE',
        ?string $fechaSolicitud = null
    ){
        $this->id = $id;
        $this->equipoId = $equipoId;
        $this->descripcionProblema = $descripcionProblema;
        $this->prioridad = $prioridad;
        $this->estado = $estado;
        $this->fechaSolicitud = $fechaSolicitud;
    }

    // getters

    public function getId ():?int {return $this->id;}
    public function getEquipoId():int {return $this->equipoId;}
    public function getDescripcionProblema():string {return $this->descripcionProblema;}
    public function getPrioridad(): string {return $this->prioridad;}
    public function getEstado():string {return $this->estado;}
    public function getFechaSolicitud(): string {return $this->fechaSolicitud;}
    
   
}