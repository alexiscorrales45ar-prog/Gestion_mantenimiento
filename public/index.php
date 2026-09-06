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

use App\Config\Database;
use App\Repositories\ClienteRepository;
use App\Repositories\SolicitudRepository;
use App\Repositories\TecnicoRepository;
use App\Repositories\OrdenTrabajoReporsitory;
use App\Controllers\ClienteController;
use App\Controllers\SolicitudController;
use App\Controllers\OrdenTrabajoController;

// Inicializar base de datos y repositorios usando el Singleton
$db = Database::getInstance()->getConnection(); // <-- ¡Aquí faltaba el punto y coma!

$clienteRepo = new ClienteRepository($db);
$solicitudRepo = new SolicitudRepository($db);
$tecnicoRepo = new TecnicoRepository(); 
$ordenRepo = new OrdenTrabajoReporsitory();

$clienteController = new ClienteController($clienteRepo);
$solicitudController = new SolicitudController($solicitudRepo);
$ordenTrabajoController = new OrdenTrabajoController(); // <-- Corregido a la clase correcta

// Capturar la variable de control por POST o GET
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

    case 4:
        // Caso de Uso 3: Generar y Asignar Orden de Trabajo (RF03)[cite: 1]
        $ordenTrabajoController->crearOrden();
        break;

    default:
        // Si no se envía ninguna acción, carga la vista de cliente por defecto
        require_once __DIR__ . '/cliente.html';
        break;

    case 5:
        // cas de uso 4: ejecutar y actualizar orden de trabajo
        $ordenTrabajoController->actualizarOrden();
        break;
}
?>