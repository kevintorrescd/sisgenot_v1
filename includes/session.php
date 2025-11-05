<?php
/**
 * Manejo de sesiones del sistema
 */

// Incluir configuración
require_once dirname(__DIR__) . '/config/config.php';

class SessionManager {
    
    /**
     * Iniciar sesión
     */
    public static function iniciar_sesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
            
            // Regenerar ID de sesión por seguridad
            if (!isset($_SESSION['iniciada'])) {
                session_regenerate_id(true);
                $_SESSION['iniciada'] = true;
            }
        }
    }
    
    /**
     * Verificar si el usuario está autenticado
     * @return bool
     */
    public static function esta_autenticado() {
        self::iniciar_sesion();
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_rol']);
    }
    
    /**
     * Obtener datos del usuario de la sesión
     * @return array|null
     */
    public static function obtener_usuario() {
        if (!self::esta_autenticado()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'] ?? '',
            'apellido' => $_SESSION['usuario_apellido'] ?? '',
            'email' => $_SESSION['usuario_email'] ?? '',
            'rol' => $_SESSION['usuario_rol']
        ];
    }
    
    /**
     * Establecer datos del usuario en la sesión
     * @param array $usuario
     */
    public static function establecer_usuario($usuario) {
        self::iniciar_sesion();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_apellido'] = $usuario['apellido'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['ultimo_acceso'] = time();
    }
    
    /**
     * Verificar si el usuario tiene un rol específico
     * @param string $rol
     * @return bool
     */
    public static function tiene_rol($rol) {
        $usuario = self::obtener_usuario();
        return $usuario && $usuario['rol'] === $rol;
    }
    
    /**
     * Verificar si el usuario es administrador
     * @return bool
     */
    public static function es_admin() {
        return self::tiene_rol('admin');
    }
    
    /**
     * Verificar si el usuario es docente
     * @return bool
     */
    public static function es_docente() {
        return self::tiene_rol('docente');
    }
    
    /**
     * Verificar si el usuario es estudiante
     * @return bool
     */
    public static function es_estudiante() {
        return self::tiene_rol('estudiante');
    }
    
    /**
     * Verificar si la sesión ha expirado
     * @return bool
     */
    public static function sesion_expirada() {
        if (!isset($_SESSION['ultimo_acceso'])) {
            return true;
        }
        
        return (time() - $_SESSION['ultimo_acceso']) > SESSION_LIFETIME;
    }
    
    /**
     * Actualizar tiempo de último acceso
     */
    public static function actualizar_acceso() {
        if (self::esta_autenticado()) {
            $_SESSION['ultimo_acceso'] = time();
        }
    }
    
    /**
     * Cerrar sesión
     */
    public static function cerrar_sesion() {
        self::iniciar_sesion();
        
        // Limpiar todas las variables de sesión
        $_SESSION = array();
        
        // Eliminar cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir sesión
        session_destroy();
    }
    
    /**
     * Requerir autenticación - redirige al login si no está autenticado
     */
    public static function requerir_autenticacion() {
        if (!self::esta_autenticado() || self::sesion_expirada()) {
            self::cerrar_sesion();
            redireccionar(APP_URL . '/index.php?action=login');
        }
        
        self::actualizar_acceso();
    }
    
    /**
     * Requerir rol específico - redirige si no tiene el rol
     * @param string $rol_requerido
     */
    public static function requerir_rol($rol_requerido) {
        self::requerir_autenticacion();
        
        if (!self::tiene_rol($rol_requerido)) {
            // Redirigir según el rol actual
            $usuario = self::obtener_usuario();
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
                    redireccionar(APP_URL . '/index.php?action=login');
            }
        }
    }
    
    /**
     * Establecer mensaje flash
     * @param string $tipo (success, error, warning, info)
     * @param string $mensaje
     */
    public static function establecer_mensaje($tipo, $mensaje) {
        self::iniciar_sesion();
        $_SESSION['mensaje_flash'] = [
            'tipo' => $tipo,
            'mensaje' => $mensaje
        ];
    }
    
    /**
     * Obtener y limpiar mensaje flash
     * @return array|null
     */
    public static function obtener_mensaje() {
        self::iniciar_sesion();
        
        if (isset($_SESSION['mensaje_flash'])) {
            $mensaje = $_SESSION['mensaje_flash'];
            unset($_SESSION['mensaje_flash']);
            return $mensaje;
        }
        
        return null;
    }
}
?>
