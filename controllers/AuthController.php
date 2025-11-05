<?php
/**
 * Controlador de Autenticación
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/User.php';

class AuthController {
    private $db;
    private $user_model;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user_model = new User($this->db);
    }
    
    /**
     * Procesar autenticación de usuario
     */
    public function autenticar() {
        // Verificar que sea una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            SessionManager::establecer_mensaje('error', 'Método no permitido');
            redireccionar(APP_URL . '/index.php?action=login');
        }
        
        // Obtener datos del formulario
        $email = limpiar_entrada($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validar campos requeridos
        if (empty($email) || empty($password)) {
            SessionManager::establecer_mensaje('error', 'Email y contraseña son requeridos');
            redireccionar(APP_URL . '/index.php?action=login');
        }
        
        // Validar formato de email
        if (!validar_email($email)) {
            SessionManager::establecer_mensaje('error', 'Formato de email inválido');
            redireccionar(APP_URL . '/index.php?action=login');
        }
        
        try {
            // Buscar usuario por email
            $usuario = $this->user_model->obtener_por_email($email);
            
            if (!$usuario) {
                SessionManager::establecer_mensaje('error', 'Credenciales incorrectas');
                redireccionar(APP_URL . '/index.php?action=login');
            }
            
            // Verificar que el usuario esté activo
            if ($usuario['estado'] !== 'activo') {
                SessionManager::establecer_mensaje('error', 'Usuario inactivo. Contacte al administrador');
                redireccionar(APP_URL . '/index.php?action=login');
            }
            
            // Verificar contraseña
            if (!verificar_password($password, $usuario['password'])) {
                SessionManager::establecer_mensaje('error', 'Credenciales incorrectas');
                redireccionar(APP_URL . '/index.php?action=login');
            }
            
            // Establecer sesión
            SessionManager::establecer_usuario($usuario);
            
            // Registrar log de actividad
            log_actividad("Usuario autenticado exitosamente", 'INFO');
            
            // Redirigir según el rol
            switch ($usuario['rol']) {
                case 'admin':
                    redireccionar(APP_URL . '/index.php?action=admin_dashboard');
                    break;
                case 'docente':
                    redireccionar(APP_URL . '/index.php?action=docente_dashboard');
                    break;
                case 'estudiante':
                    redireccionar(APP_URL . '/index.php?action=estudiante_dashboard');
                    break;
                default:
                    SessionManager::cerrar_sesion();
                    SessionManager::establecer_mensaje('error', 'Rol de usuario no válido');
                    redireccionar(APP_URL . '/index.php?action=login');
            }
            
        } catch (Exception $e) {
            error_log("Error en autenticación: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error interno del sistema. Intente nuevamente');
            redireccionar(APP_URL . '/index.php?action=login');
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function cerrar_sesion() {
        log_actividad("Usuario cerró sesión", 'INFO');
        SessionManager::cerrar_sesion();
        redireccionar(APP_URL . '/index.php?action=login');
    }
    
    /**
     * Verificar si el usuario está autenticado (para AJAX)
     */
    public function verificar_autenticacion() {
        header('Content-Type: application/json');
        
        if (SessionManager::esta_autenticado() && !SessionManager::sesion_expirada()) {
            $usuario = SessionManager::obtener_usuario();
            respuesta_json(true, 'Usuario autenticado', [
                'rol' => $usuario['rol'],
                'nombre' => $usuario['nombre'] . ' ' . $usuario['apellido']
            ]);
        } else {
            respuesta_json(false, 'Usuario no autenticado');
        }
    }
}
?>
