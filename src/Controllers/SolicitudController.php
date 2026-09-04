<?php
namespace App\Controllers;

use App\Repositories\SolicitudRepository;
use App\Models\Solicitud;

class SolicitudController {
    private SolicitudRepository $repository;

    public function __construct(SolicitudRepository $repository) {
        $this->repository = $repository;
    }

    // Procesa el registro de la solicitud de mantenimiento (RF02)
    public function registrar(): void {
        header('Content-Type: application/json');

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (empty($data['equipo_id']) || empty($data['descripcion_problema']) || empty($data['prioridad'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'El equipo, la descripción del problema y la prioridad son obligatorios.'
            ]);
            return;
        }

        try {
            $solicitud = new Solicitud(
                null,
                (int)$data['equipo_id'],
                $data['descripcion_problema'],
                $data['prioridad'],
                'PENDIENTE'
            );
            
            $this->repository->guardar($solicitud);

            echo json_encode([
                'success' => true,
                'message' => 'Solicitud de mantenimiento registrada exitosamente con estado PENDIENTE.'
            ]);
        
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al registrar la solicitud: ' . $e->getMessage()
            ]);
        }
    }
}