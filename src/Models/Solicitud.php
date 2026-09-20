<?php
namespace app\Models;

class Solicitud{
    private ?int $id;
    private string $codigoSeguimiento;
    private int $clienteId;
    private int $equipoId;
    private string $descripcionFalla;
    private string $prioridad;
    private string $estado;
    private ?string $fechaRegistro;

    public function __construct(
        ?int $id,
        string $codigoSeguimiento,
        int  $clienteId,
        int $equipoId,
        string $descripcionFalla,
        string $prioridad = 'Media',
        string $estado = 'Pendiente',
        ?string $fechaRegistro = null
    ){
        $this->id = $id;
        $this->codigoSeguimiento = $codigoSeguimiento;
        $this->clienteId = $clienteId;
        $this->equipoId = $equipoId;
        $this->descripcionFalla = $descripcionFalla;
        $this->prioridad = $prioridad;
        $this->estado = $estado;
        $this->fechaRegistro = $fechaRegistro;
    }

    // getters

    public function getId ():?int {return $this->id;}
    public function getCodigoSeguimiento (): string {return $this->codigoSeguimiento;}
    public function getClienteId(): int {return $this->clienteId;}
    public function getEquipoId():int {return $this->equipoId;}
    public function getDescripcionFalla():string {return $this->descripcionFalla;}
    public function getPrioridad(): string {return $this->prioridad;}
    public function getEstado():string {return $this->estado;}
    public function getFechaRegistro(): string {return $this->fechaRegistro;}
    
   
}