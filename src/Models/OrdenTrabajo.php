<?php

namespace app\Models;

class OrdenTrabajo{
    private ?int $id;
    private int $solicitudId;
    private int $tecnicoId;
    private ?string $fechaAsignacion;
    private string $estado;

    public function __construct(
        ?int $id,
        int $solicitudId,
        int $tecnicoId,
        ?string $fechaAsignacion = null,
        string $estado = ' ASIGNADO' 
    ){
        $this->id = $id;
        $this->solicitudId = $solicitudId;
        $this->tecnicoId = $tecnicoId;
        $this->fechaAsignacion = $fechaAsignacion;
        $this->estado = $estado;
    }

    public function getId(): ?int {return $this->id;}
    public function getSolicitudId(): int {return $this->solicitudId;}
    public function getTecnicoId(): int {return $this->tecnicoId;}
    public function getFechaAsignacion():?string {return $this->fechaAsignacion;}
    public function getEstado(): string {return $this->estado;}

    
}