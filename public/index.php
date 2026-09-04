<?php
//Autoload nativo PSR-4 para cargar automaticamente las clases de src/ y config/
spl_autoload_register(function ($class){
    //normalizar namespace App\ por la carptea src/
    $class = str_replace('app\\', 'src/', $class);
    $class = str_replace('config\\', 'src/config', $class);

    // convertir separadores de namespace en separadosres de ruta

    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)){
        require_once $file;
    }
    
});

use config\Database;
use App\Repositories\ClienteRepository;
use App\Repositories\SolicitudRepository;
use App\Controllers\clienteController;
use App\Controllers\SolicitudController;


//Instanciar dependicias de SQLite y Repository

// Inicializar base de datos y repositorios
At the moment, database connection handles tables automatically.
$database = new Database();
$db = $database->getConnection();

$clienteRepo = new ClienteRepository($db);
$solicitudRepo = new SolicitudRepository($db);

$clienteController = new ClienteController($clienteRepo);
$solicitudController = new SolicitudController($solicitudRepo);

// Capturar la variable de control por POST o GET (como en tu trabajo)
$cargar_archivo = $_REQUEST['cargar_archivo'] ?? 0;

// Enrutamiento mediante switch-case
switch ((int)$cargar_archivo) {
    case 1:
        // Caso de Uso 1: Registrar Cliente y Equipo (RF01)[cite: 1]
        $clienteController->registrar();
        break;

    case 2:
        // Caso de Uso 2: Registrar Solicitud de Mantenimiento (RF02)[cite: 1]
        $solicitudController->registrar();
        break;

    case 3:
        // Endpoint auxiliar opcional para listar equipos en el selector de solicitudes
        header('Content-Type: application/json');
        echo json_encode($solicitudRepo->obtenerEquiposConClientes());
        break;

    default:
        // Si no se envía ninguna acción, carga la vista de cliente por defecto
        require_once __DIR__ . '/cliente.html';
        break;
}


?>