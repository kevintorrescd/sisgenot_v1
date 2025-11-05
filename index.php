<?php
/**
 * Punto de entrada principal del sistema
 */

// Incluir archivos de configuración
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Iniciar sesión
SessionManager::iniciar_sesion();

// Obtener la acción solicitada
$action = $_GET['action'] ?? 'login';

// Enrutamiento básico
switch ($action) {
    // Autenticación
    case 'login':
        if (SessionManager::esta_autenticado()) {
            // Redirigir según el rol
            $usuario = SessionManager::obtener_usuario();
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
            }
        }
        include 'views/auth/login.php';
        break;
        
    case 'logout':
        SessionManager::cerrar_sesion();
        redireccionar(APP_URL . '/index.php?action=login');
        break;
        
    case 'authenticate':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->autenticar();
        break;
    
    // Panel de Administrador
    case 'admin_dashboard':
        SessionManager::requerir_rol('admin');
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->dashboard();
        break;
        
    case 'admin_usuarios':
        SessionManager::requerir_rol('admin');
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->usuarios();
        break;
        
    case 'admin_tecnicas':
        SessionManager::requerir_rol('admin');
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->tecnicas();
        break;
        
    case 'admin_cursos':
        SessionManager::requerir_rol('admin');
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->cursos();
        break;
        
    case 'admin_semestres':
        SessionManager::requerir_rol('admin');
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->semestres();
        break;
        
    case 'admin_asignaciones':
        SessionManager::requerir_rol('admin');
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->asignaciones();
        break;
        
    case 'admin_inscripciones':
        SessionManager::requerir_rol('admin');
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->inscripciones();
        break;
    
    // Panel de Docente
    case 'docente_dashboard':
        SessionManager::requerir_rol('docente');
        require_once 'controllers/DocenteController.php';
        $controller = new DocenteController();
        $controller->dashboard();
        break;
        
    case 'docente_cursos':
        SessionManager::requerir_rol('docente');
        require_once 'controllers/DocenteController.php';
        $controller = new DocenteController();
        $controller->cursos();
        break;
        
    case 'docente_calificar':
        SessionManager::requerir_rol('docente');
        require_once 'controllers/DocenteController.php';
        $controller = new DocenteController();
        $controller->calificar();
        break;
    
    // Panel de Estudiante
    case 'estudiante_dashboard':
        SessionManager::requerir_rol('estudiante');
        require_once 'controllers/EstudianteController.php';
        $controller = new EstudianteController();
        $controller->dashboard();
        break;
        
    case 'estudiante_notas':
        SessionManager::requerir_rol('estudiante');
        require_once 'controllers/EstudianteController.php';
        $controller = new EstudianteController();
        $controller->notas();
        break;
    
    // Acciones AJAX para Admin
    case 'ajax_crear_usuario':
    case 'ajax_actualizar_usuario':
    case 'ajax_eliminar_usuario':
    case 'ajax_obtener_usuario':
    case 'ajax_crear_tecnica':
    case 'ajax_actualizar_tecnica':
    case 'ajax_eliminar_tecnica':
    case 'ajax_obtener_tecnica':
    case 'ajax_crear_curso':
    case 'ajax_actualizar_curso':
    case 'ajax_eliminar_curso':
    case 'ajax_obtener_curso':
    case 'ajax_crear_semestre':
    case 'ajax_actualizar_semestre':
    case 'ajax_eliminar_semestre':
    case 'ajax_obtener_semestre':
    case 'ajax_crear_asignacion':
    case 'ajax_eliminar_asignacion':
    case 'ajax_crear_inscripcion':
    case 'ajax_eliminar_inscripcion':
        SessionManager::requerir_rol('admin');
        require_once 'controllers/AdminController.php';
        $controller = new AdminController();
        $controller->manejar_ajax($action);
        break;
    
    // Acciones AJAX para Docente
    case 'ajax_obtener_estudiantes_curso':
    case 'ajax_guardar_calificacion':
    case 'ajax_obtener_calificaciones_estudiante':
        SessionManager::requerir_rol('docente');
        require_once 'controllers/DocenteController.php';
        $controller = new DocenteController();
        $controller->manejar_ajax($action);
        break;
    
    // Acciones AJAX para Estudiante
    case 'ajax_obtener_notas_curso':
        SessionManager::requerir_rol('estudiante');
        require_once 'controllers/EstudianteController.php';
        $controller = new EstudianteController();
        $controller->manejar_ajax($action);
        break;
    
    // Página no encontrada
    default:
        http_response_code(404);
        echo "<h1>Página no encontrada</h1>";
        echo "<p><a href='" . APP_URL . "'>Volver al inicio</a></p>";
        break;
}
?>
