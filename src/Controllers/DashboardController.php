<?php
namespace App\Controllers;

use App\Repositories\DashboardRepository;

class DashboardController{
    private DashboardRepository $repository;

    public function __contruct(){
        $this->repository =  new DashboardRepository();

    }
    public function obtenerMetricas(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        while (ob_get_level()){
            ob_end_clean();
        }
        header('contect-Type: application/json; charset=utf-8');

        if (!isset($_SESSION['usuario_id'])){
            echo json_encode([
                'success'=>false,
                'message'=>'Sesión no valida o expirada. Por favor inicie sesión.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            $totales   = $this->repository->obtenerTotalesPorEstado();
            $ingresos  = $this->repository->obtenerTotalIngresos();
            $recientes = $this->repository->obtenerUltimasSolicitudes(5);

            echo json_encode([
                'success' => true,
                'data' => [
                    'totales' => [
                        'pendientes'       => $totales['Pendiente'] ?? 0,
                        'en_proceso'       => $totales['En Proceso'] ?? 0,
                        'finalizadas'      => $totales['Finalizada'] ?? 0,
                        'canceladas'       => $totales['Cancelada'] ?? 0,
                        'ingresos_totales' => $ingresos
                    ],
                    'recientes' => $recientes,
                    'usuario'   => [
                        'nombre' => $_SESSION['usuario_nombre'] ?? 'Usuario',
                        'rol'    => $_SESSION['rol'] ?? 'cliente'
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al consultar las métricas del dashboard: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

}