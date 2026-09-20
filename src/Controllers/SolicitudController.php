<?php
namespace App\Controllers;

use App\Repositories\SolicitudRepository;
use App\Models\Solicitud;

class SolicitudController {
    private SolicitudRepository $repository;

    public function __construct(SolicitudRepository $repository) {
        $this->repository = $repository;
    }

    // Obtener lista de equipos
    public function obtenerEquipos(): void {
        header('Content-Type: application/json');
        $clienteId = $_GET['cliente_id'] ?? null;

        if (!$clienteId) {
            echo json_encode(['success' => false, 'message' => 'ID de cliente requerido.']);
            return;
        }

        $equipos = $this->repository->obtenerEquiposPorCliente((int)$clienteId);
        echo json_encode(['success' => true, 'data' => $equipos]);
    }

    // Registrar solicitud
    public function crear(): void {
        header('Content-Type: application/json');

        $clienteId = $_POST['cliente_id'] ?? null;
        $equipoId = $_POST['equipo_id'] ?? null;
        $falla = $_POST['descripcion_falla'] ?? null;
        $prioridad = $_POST['prioridad'] ?? 'Media';

        if (empty($clienteId) || empty($equipoId) || empty($falla)) {
            echo json_encode(['success' => false, 'message' => 'Cliente, equipo y descripción son obligatorios.']);
            return;
        }

        try {
            // Generador del Código Único
            $codigoSeguimiento = 'SOL-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));

            $solicitud = new Solicitud(null, $codigoSeguimiento, (int)$clienteId, (int)$equipoId, $falla, $prioridad, 'Pendiente');
            $this->repository->guardarSolicitud($solicitud);

            echo json_encode([
                'success' => true,
                'message' => '¡Solicitud registrada exitosamente!',
                'codigo' => $codigoSeguimiento
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al registrar solicitud: ' . $e->getMessage()]);
        }
    }

    // Consultar por código
    public function consultar(): void {
        header('Content-Type: application/json');
        $codigo = $_GET['codigo'] ?? '';

        if (empty($codigo)) {
            echo json_encode(['success' => false, 'message' => 'Ingrese un código de seguimiento.']);
            return;
        }

        $solicitud = $this->repository->buscarPorCodigo($codigo);
        if ($solicitud) {
            echo json_encode(['success' => true, 'data' => $solicitud]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontró ninguna solicitud con ese código.']);
        }
    }
}