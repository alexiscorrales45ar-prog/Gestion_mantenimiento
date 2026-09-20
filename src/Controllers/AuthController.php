<?php
namespace App\Controllers;

// 1. Asegúrate de importar el repositorio correctamente
use App\Repositories\UsuarioRepository;

class AuthController {
    
    // 2. Declara explícitamente la propiedad con su tipo para que el editor la reconozca
    private UsuarioRepository $usuarioRepo;

    public function __construct() {
        $this->usuarioRepo = new UsuarioRepository();
    }

    public function login(): void {
        session_start();
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $correo = trim($input['usuario'] ?? '');
        $password = trim($input['password'] ?? '');

        if (empty($correo) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Ingrese correo y contraseña.']);
            return;
        }

        // Aquí ya no te marcará error el editor
        $usuario = $this->usuarioRepo->buscarCorreo($correo);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario'] = $usuario['correo'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            echo json_encode([
                'success' => true,
                'rol' => $usuario['rol'],
                'message' => '¡Bienvenido al sistema, ' . $usuario['nombre'] . '!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciales inválidas.']);
        }
    }

    public function registrar(): void {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $nombre = trim($input['nombre'] ?? '');
        $correo = trim($input['correo'] ?? '');
        $password = trim($input['password'] ?? '');
        $rol = trim($input['rol'] ?? 'cliente');

        if (empty($nombre) || empty($correo) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
            return;
        }

        // Validación usando el método del repositorio
        if ($this->usuarioRepo->buscarCorreo($correo)) {
            echo json_encode(['success' => false, 'message' => 'El correo ya se encuentra registrado.']);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $creado = $this->usuarioRepo->registrar($nombre, $correo, $passwordHash, $rol);

        if ($creado) {
            echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario.']);
        }
    }
}
?>