<?php
namespace App\Controllers;

use App\Repositories\TecnicoRepository;

class TecnicoController {
    private TecnicoRepository $repository;

    public function __construct(TecnicoRepository $repository) {
        $this->repository = $repository;
    }

    public function listar(): void {
        while (ob_get_level()) { 
            ob_end_clean(); 
        }
        header('Content-Type: application/json; charset=utf-8');
        
        $solicitudes = $this->repository->obtenerSolicitudes();
        echo json_encode([
            'success' => true, 
            'data'    => $solicitudes
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function actualizar(): void {
        // 1. Limpieza de cualquier búfer de salida previo para no corromper la cabecera JSON
        while (ob_get_level()) { 
            ob_end_clean(); 
        }
        header('Content-Type: application/json; charset=utf-8');

        // 2. Captura y sanitización de datos enviados por POST
        $solicitudId = filter_input(INPUT_POST, 'solicitud_id', FILTER_VALIDATE_INT);
        $diagnostico = trim($_POST['diagnostico'] ?? '');
        $costoInput  = $_POST['costo'] ?? 0;
        $costo       = is_numeric($costoInput) ? (float)$costoInput : 0.0;

        // 3. Normalización y mapeo de estado
        // Permite la entrada en minúsculas/título y la homologa a valores válidos
        $estadoRaw = trim($_POST['estado'] ?? '');
        $mapaEstados = [
            'pendiente'  => 'Pendiente',
            'en proceso' => 'En Proceso',
            'finalizada' => 'Finalizada',
            'cancelada'  => 'Cancelada',
            // Variantes en mayúsculas por compatibilidad
            'PENDIENTE'  => 'Pendiente',
            'EN_PROCESO' => 'En Proceso',
            'FINALIZADA' => 'Finalizada',
            'CANCELADA'  => 'Cancelada'
        ];

        $estadoNormalizado = strtolower($estadoRaw);
        $estado = $mapaEstados[$estadoRaw] 
                  ?? $mapaEstados[$estadoNormalizado] 
                  ?? 'En Proceso';

        // 4. Validación de campos obligatorios
        if (!$solicitudId || empty($diagnostico)) {
            echo json_encode([
                'success' => false, 
                'message' => 'El ID de la solicitud y el diagnóstico son campos obligatorios.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 5. Ejecución en la capa de persistencia
        try {
            $exito = $this->repository->actualizarOrden($solicitudId, $estado, $diagnostico, $costo);

            if ($exito) {
                echo json_encode([
                    'success' => true, 
                    'message' => '¡Diagnóstico y estado de la orden guardados con éxito!'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'No se realizaron cambios en la base de datos o el ID ingresado no existe.'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (\PDOException $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error en la Base de Datos: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error inesperado en el servidor: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}