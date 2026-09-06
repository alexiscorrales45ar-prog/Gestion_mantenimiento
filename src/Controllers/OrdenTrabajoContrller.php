<?php
namespace app\Controllers;

use app\Repositories\OrdenTrabajoReporsitory;
use app\Models\OrdenTrabajo;


class OrdenTrabajoController{
    private OrdenTrabajoReporsitory $ordeRepo;

    public function __construct(){
        $this->ordeRepo = new OrdenTrabajoReporsitory ();

    
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
            $resultado = $this->ordeRepo->guardar($orden);

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
    

}