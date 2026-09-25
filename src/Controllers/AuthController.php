<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;

class AuthController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Procesa el inicio de sesión
     */
    public function login(): void {
        while (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');

        $correo   = trim($_POST['correo'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($correo) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'El correo y la contraseña son obligatorios.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        try {
            // CORREGIDO: Cambiado 'usuario' por 'usuarios'
            $sql = "SELECT id, nombre, correo, password, rol FROM usuarios WHERE correo = :correo LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':correo' => $correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificación de credenciales (Soporta hash con password_verify o texto plano)
            if ($usuario && ($password === $usuario['password'] || password_verify($password, $usuario['password']))) {
                
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Guardamos los datos clave en la Sesión de PHP
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_correo'] = $usuario['correo'];
                $_SESSION['rol']            = strtolower($usuario['rol']);

                $rolFormateado = strtolower($usuario['rol']);

                echo json_encode([
                    'success' => true,
                    'message' => '¡Inicio de sesión exitoso!',
                    'rol'     => $rolFormateado, // Agregado aquí para que coincida directo con resultado.rol en el JS
                    'data'    => [
                        'id'     => $usuario['id'],
                        'nombre' => $usuario['nombre'],
                        'rol'    => $rolFormateado
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Credenciales incorrectas. Verifique su correo o contraseña.'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    /**
     * Cierra la sesión activa
     */
    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_destroy();

        while (ob_get_level()) { ob_end_clean(); }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}