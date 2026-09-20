<?php
namespace app\Controllers;

use App\Repositories\OrdenTrabajoRepository;
use App\Models\OrdenTrabajo;


class OrdenTrabajoController{
    private OrdenTrabajoRepository $ordenRepo;

    public function __construct(){
        $this->ordenRepo = new OrdenTrabajoRepository ();

    
    }

    // Metodo para  procesar la creacion  y asignacion de la orden de trabajo
    public function crearOrden(): void {
        header('Content-type: applicacion/json');

        // leer los datos JSON enviados desde el frontend
        $input = json_decode(file_get_contents('php://input'), true);

        $solicitudId = $input['solicitud_id'] ?? null;
        $tecnicoId = $input['tecnico_id'] ?? null;

        if (!$solicitudId || !$tecnicoId){
            echo Json_encode([
                'success'=> false,
                'message'=> 'Faltan datos obligatorios (solicitud o técnico).'
            ]);
            return;
        } 

        try{
            // Instanciar el modelo de ordn de trabajo
            $orden = new OrdenTrabajo(
                null,
                (int)$solicitudId,
                (int)$tecnicoId,
                null,
                'ASIGNADA'
            );
            $resultado = $this->ordenRepo->guardar($orden);

            if ($resultado){
                echo json_encode([
                    'success' => true,
                    'message' => '¡Orden de trabajo generado y asignada exitosamente!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se puedo guardar la orden de trabajo en la base de datos.'
                ]);
            }
        }catch (\Exception $e){
            echo json_encode([
                'success' => false,
                'message' => 'Error en la servidor: ' . $e->getMessage()
             ]);
        }
    }

    public function actualizarOrden():void {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        $ordenId = $input ['orden_id'] ?? null;
        $estado = $input['estado'] ?? null;
        $actividades = $input['actividades'] ?? '';
        $costo = $input['costo'] ?? null;
        $fechaInicio = $input['fecha_inicio'] ?? null;
        $fechaFin = $input['fecha_fin'] ?? null;

        if (!$ordenId || $estado){
            echo json_encode([
                'success' => false,
                'message' =>'El ID de la orden y el estado son obligatorios.'
            ]);
            return;
        }
         try {
        $resultado = $this->ordenRepo->actualizarEjecucion(
            (int)$ordenId,
            $estado,
            $actividades,
            (float)$costo,
            $fechaInicio,
            $fechaFin
        );

        if ($resultado){
            echo json_encode([
                'success' => true,
                'message' => 'No se pudo actualizar la orden de trabajo en la base de datos.'
            ]);
        }
    } catch (\Exception $e){
        echo json_encode([
            'success' => false,
            'message' => 'Error en el servidor' . $e->getMessage()
        ]);
    }
        
    }

    public function generarReporteHistorial(): void {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }

        $filtro = $_GET['filtro'] ?? '';

        try {
            $datos = $this->ordenRepo->obtenerHistorialReportes($filtro);
            echo json_encode([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar el reporte: ' . $e->getMessage()
            ]);
        }
    }

}

