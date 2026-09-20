<?php
namespace app\Controllers;
use App\Repositories\ClienteRepository;
use App\Models\Cliente;
use App\Models\Equipo;


class clienteController{
    private ClienteRepository $repository;

    public function __constructU(ClienteRepository $repository){
        $this->repository = $repository;
    
    }
        /** 
     * procesa la solicitud enviad desde la interfz (ajax/fetch)
        */
    public function registrar ():void{
        // estabecer la cabecera para devovler respuestas en joson 
        header('contect-type: application/json');

        //capturar los datos enviados en el cuerpo de la peticion (json)
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        //validaciones basicas de compos obligatorios segun RF01
        if (empty($data['nombre_cliente']) || empty($data['contacto_cliente']) || empty($data['nombre_equipo'])){
            echo json_encode([
                'success' =>false,
                'massege' =>'Los campos Nombre del Cliente, Contacto y Nombre del equipo son obligatorios.'
            ]);
            return;
        }
        try{
            //1.Instanciar la entidad cliente (POO)
            $cliente = new Cliente(
                null,
                $data['nombre_cliente'],
                $data['contacto_cliente'],
                $data['nombre_equipo'] ?? null
            );
            //2. persistir el cliente y obtener su ID asignado
            $clienteId = $this->repository->guardarCliente($cliente);

            //3. Instanciar la entidad Equipo vinculada al ID cliente
            $equipo = new Equipo(
                null,
                $clienteId,
                $data['nombre_equipo'],
                $data['marca'] ?? null,
                $data['modelo'] ?? null,
                $data['serie'] ?? null
            );

            // 4. psersistir el equipo en la BD
            $this -> repository->guardarEquipo($equipo);

            // reponder exito al javaScrip
            echo json_encode([
                'success' => true,
                'mesage' => 'Cliente y equipo registrados exitosamente con el ID de cliente: '. $clienteId
            ]);

        }catch (\Exception $e){
            echo json_encode([
                'success'=> false,
                'message'=> 'Error al procesar el registro: ' . $e->getMessage()
            ]);
        }
    }


}

?>