<?php
namespace App\Controllers;

use App\Repositories\ClienteRepository;
use App\Models\Cliente;
use App\Models\Equipo;

class ClienteController {
    private ClienteRepository $repository;

    public function __construct(ClienteRepository $repository) {
        $this->repository = $repository;
    }

    // Buscar cliente
    public function buscar(): void {
        header('Content-Type: application/json');
        $criterio = $_GET['documento'] ?? $_POST['documento'] ?? '';

        if (empty($criterio)) {
            echo json_encode(['success' => false, 'message' => 'Ingrese un parámetro de búsqueda.']);
            return;
        }

        $cliente = $this->repository->buscarPorTelefonoODocumento($criterio);

        if ($cliente) {
            echo json_encode(['success' => true, 'data' => $cliente]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cliente no encontrado.']);
        }
    }

    // Registrar cliente nuevo + primer equipo
    // Registrar cliente nuevo + primer equipo
    public function registrar(): void {
        header('Content-Type: application/json');

        $nombre = $_POST['nombre'] ?? null;
        $cedula = $_POST['cedula'] ?? null;
        $telefono = $_POST['telefono'] ?? null;
        $direccion = $_POST['direccion'] ?? null;
        
        // Verifica que coincida exactamente con el name="" de tu HTML
        $nombreEquipo = $_POST['nombre_equipo'] ?? null;
        $marca = $_POST['marca'] ?? '';
        $modelo = $_POST['modelo'] ?? '';
        $serie = $_POST['serie'] ?? '';

        if (empty($nombre) || empty($cedula) || empty($telefono) || empty($nombreEquipo)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Nombre, Cédula, Teléfono y Nombre del Equipo son obligatorios.'
            ]);
            return;
        }

        try {
            // 1. Instanciar y guardar Cliente
            $cliente = new Cliente(null, $nombre, $cedula, $telefono, $direccion);
            $clienteId = $this->repository->guardarCliente($cliente);

            // 2. Instanciar y guardar el primer Equipo con el ID obtenido
            if ($clienteId > 0) {
                $equipo = new Equipo(null, (int)$clienteId, $nombreEquipo, $marca, $modelo, $serie);
                $this->repository->guardarEquipo($equipo);
            }

            // Devuelve respuesta exitosa
            echo json_encode([
                'success' => true, 
                'message' => '¡Cliente y su primer equipo registrados con éxito!'
            ]);
        } catch (\PDOException $e) {
            // Captura si la Cédula ya existe en SQLite (violación de restricción UNIQUE)
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'UNIQUE')) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'La cédula o documento ingresado ya se encuentra registrado en el sistema.'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error en base de datos: ' . $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al guardar: ' . $e->getMessage()
            ]);
        }
    }

    // Registrar equipo adicional a cliente existente
    public function registrarEquipo(): void {
        header('Content-Type: application/json');

        $clienteId = $_POST['cliente_id'] ?? null;
        $nombreEquipo = $_POST['nombre'] ?? null;
        $marca = $_POST['marca'] ?? '';
        $modelo = $_POST['modelo'] ?? '';
        $serie = $_POST['serie'] ?? '';

        if (empty($clienteId) || empty($nombreEquipo)) {
            echo json_encode(['success' => false, 'message' => 'El cliente y el nombre del equipo son obligatorios.']);
            return;
        }

        try {
            $equipo = new Equipo(null, (int)$clienteId, $nombreEquipo, $marca, $modelo, $serie);
            $this->repository->guardarEquipo($equipo);

            echo json_encode(['success' => true, 'message' => '¡Nuevo equipo vinculado exitosamente al cliente!']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al vincular equipo: ' . $e->getMessage()]);
        }
    }
}