<?php

spl_autoload_register(function($class){
    if (str_starts_with($class, 'App\\')){
        $class = 'src/' . substr($class, 4);
    }

    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)){
        require_once $file;
    }
});


// Importaciones con las mayúsculas correctas (App)
use App\Config\Database;
use App\Repositories\ClienteRepository;
use App\Repositories\SolicitudRepository;
use App\Repositories\TecnicoRepository;
use App\Repositories\OrdenTrabajoRepository; // Corregido el typo
use App\Repositories\UsuarioRepository;    // Corregido a App\
use App\Controllers\ClienteController;
use App\Controllers\SolicitudController;
use App\Controllers\OrdenTrabajoController;
use App\Controllers\AuthController;     // Corregido a App\
use App\Controllers\TecnicoController;
use App\Controllers\DashboardController;

// Inicializar base de datos y repositorios usando el Singleton
$db = Database::getInstance()->getConnection(); 

$clienteRepo = new ClienteRepository($db);
$solicitudRepo = new SolicitudRepository($db);
$tecnicoRepo = new TecnicoRepository($db); 
$ordenRepo = new OrdenTrabajoRepository(); // Corregido el typo
$usuarioRepo = new UsuarioRepository();    // Añadido para el login

$TecnicoController = new TecnicoController($tecnicoRepo);
$clienteController = new ClienteController($clienteRepo);
$solicitudController = new SolicitudController($solicitudRepo);
$ordenTrabajoController = new OrdenTrabajoController();
$authController = new AuthController();     // Añadido para gestionar la autenticación


// Capturar la variable de control por POST o GET
$cargar_archivo = $_REQUEST['cargar_archivo'] ?? $_POST['cargar_archivo'] ?? 0;

// Enrutamiento mediante switch-case


switch ((int)$cargar_archivo) {
    case'1':
    case 'dashboard_metricas':
        $dashboard = new DashboardController();
        $dashboard->obtenerMetricas();
        break;


    case 'login':
        $auth = new AuthController();
        $auth->login();
        break;

    case 'logout':
        $auth = new AuthController();
        $auth->logout();
        break;

    case 'dashboard_metricas':
        $dashboard = new DashboardController();
        $dashboard->obtenerMetricas();
        break;



    case 2:
        // Caso de Uso 1: Registrar Cliente y Equipo (RF01)[cite: 1]
        $clienteController->registrar();
        break;

    case 3:
        // Caso de Uso 2: Registrar Solicitud de Mantenimiento (RF02)[cite: 1]
        $solicitudController->crear();
        break;

    case 4:
        // Endpoint auxiliar opcional para listar equipos en el selector de solicitudes
        header('Content-Type: application/json');
        echo json_encode($solicitudRepo->obtenerEquiposConclientesU());
        break;

    case 5:
        // Caso de Uso 3: Generar y Asignar Orden de Trabajo (RF03)[cite: 1]
        $ordenTrabajoController->crearOrden();
        break;

    case 6:
        // Caso de Uso 4: Ejecutar y actualizar orden de trabajo
        $ordenTrabajoController->actualizarOrden();
        break;
    
    case 7:
        // Caso de Uso 5: Consultar historial y generar reportes
        $ordenTrabajoController->generarReporteHistorial();
        break;

    case 8:
        // Módulo de Autenticación: Login
        $authController->login();
        break;

    case 9:
        // Módulo de Autenticación: Registro
        $authController->crearUsuario();
        break;

    case 10:
       // Registrar equipo adicional a un cliente exitoso
        $clienteController->registrarEquipo();
        break;
    
    case 11:
        // Buscar cliente por docuemnto / telefono
        $clienteController->buscar();
        break;
        
    case 12:
        $solicitudController->obtenerEquipos();
        break;
    
    case 13:
        $solicitudController->consultar();
        break;

    case 14:
        // Obtener lista de solicitudes para el tecnico
        $TecnicoController->listar();
        break;

   case 15:
        header('Content-Type: application/json; charset=utf-8');
        try {
            // Llamamos al controlador de técnico
            $TecnicoController->actualizar();
        } catch (\Throwable $e) {
            // Si ocurre cualquier error o excepción en el Controller/Repository, lo capturamos
            echo json_encode([
                'success' => false,
                'message' => 'Error capturado en index.php: ' . $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine()
            ]);
        }
        break;

    default:
    // Forzar la ruta absoluta saliendo de public si es necesario o unificándola
    $rutaLogin = __DIR__ . '/login.html';
    if (!file_exists($rutaLogin)) {
        $rutaLogin = dirname(__DIR__) . '/public/login.html';
    }
    require_once $rutaLogin;
    break;
}



?>