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
use App\Controllers\clienteController;

//Instanciar dependicias de SQLite y Repository

$database = new Database();
$db = $database->getConnetion();
$clienteRepository =new ClienteRepository($db);
$clienteController =new clienteController($clienteRepository);

// Enrutamiento basico para procesar el registro de cliente
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri=== '/api/clientes' && $method === 'POST'){
    $clienteController->registrar();
    exit;
}

//Si se accede a la raiz, redirigir a la vista del formulario

if ($uri === '/' || str_contains($uri, 'index.php')){
    require_once __DIR__ . '/cliente.html';
    exit;
}

http_response_code(404);
echo json_encode(['success'=> false, 'massege'=>'Ruta no encontrada']);


?>