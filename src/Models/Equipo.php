<?php
namespace app\Models;

class Equipo{

    private ?int $id;
    private int $clienteId;
    private string $nombre;
    private ?string $marca;
    private ?string $modelo;
    private ?string $serie;

    public function __construct(?int $id, int $clienteId, string $nombre, ?string $marca, ?string $modelo, ?string $serie ){
        $this->id = $id;
        $this->clienteId = $clienteId;
        $this->nombre = $nombre;
        $this->marca = $marca;
        $this->modelo = $modelo;
        $this->serie = $serie;


    }
    
    public function getId():?int {return $this->id;}
    public function getClienteId():int {return  $this->clienteId;}
    public function getNombre():string {return $this->nombre; }
    public function getMarca(): string {return $this->marca;}
    public function getModelo(): string {return $this->modelo;}
    public function getSerie(): string {return $this->serie;}



}



?>