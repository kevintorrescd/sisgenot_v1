<?php
/**
 * Controlador de Administrador
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Tecnica.php';
require_once dirname(__DIR__) . '/models/Curso.php';
require_once dirname(__DIR__) . '/models/Semestre.php';
require_once dirname(__DIR__) . '/models/Asignacion.php';
require_once dirname(__DIR__) . '/models/Inscripcion.php';

class AdminController {
    private $db;
    private $user_model;
    private $tecnica_model;
    private $curso_model;
    private $semestre_model;
    private $asignacion_model;
    private $inscripcion_model;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        if (!$this->db) {
            SessionManager::establecer_mensaje('error', 'Error de conexión a la base de datos');
            redireccionar(APP_URL . '/index.php?action=login');
        }
        
        $this->user_model = new User($this->db);
        $this->tecnica_model = new Tecnica($this->db);
        $this->curso_model = new Curso($this->db);
        $this->semestre_model = new Semestre($this->db);
        $this->asignacion_model = new Asignacion($this->db);
        $this->inscripcion_model = new Inscripcion($this->db);
    }
    
    /**
     * Dashboard del administrador
     */
    public function dashboard() {
        try {
            // Obtener estadísticas
            $total_estudiantes = $this->user_model->contar_por_rol('estudiante');
            $total_docentes = $this->user_model->contar_por_rol('docente');
            $total_cursos = $this->curso_model->contar_activos();
            $semestre_activo = $this->semestre_model->obtener_activo();
            
            // Obtener datos adicionales para el dashboard
            $usuarios_recientes = $this->user_model->obtener_recientes(5);
            $cursos_recientes = $this->curso_model->obtener_recientes(5);
            
            $datos = [
                'total_estudiantes' => $total_estudiantes,
                'total_docentes' => $total_docentes,
                'total_cursos' => $total_cursos,
                'semestre_activo' => $semestre_activo,
                'usuarios_recientes' => $usuarios_recientes,
                'cursos_recientes' => $cursos_recientes
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/admin/dashboard.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en dashboard admin: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar el dashboard');
            redireccionar(APP_URL . '/index.php?action=login');
        }
    }
    
    /**
     * Gestión de usuarios
     */
    public function usuarios() {
        try {
            $usuarios = $this->user_model->obtener_todos();
            
            $datos = [
                'usuarios' => $usuarios
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/admin/usuarios.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en usuarios admin: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar usuarios');
            redireccionar(APP_URL . '/index.php?action=admin_dashboard');
        }
    }
    
    /**
     * Gestión de técnicas
     */
    public function tecnicas() {
        try {
            $tecnicas = $this->tecnica_model->obtener_todas();
            
            $datos = [
                'tecnicas' => $tecnicas
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/admin/tecnicas.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en técnicas admin: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar técnicas');
            redireccionar(APP_URL . '/index.php?action=admin_dashboard');
        }
    }
    
    /**
     * Gestión de cursos
     */
    public function cursos() {
        try {
            $cursos = $this->curso_model->obtener_todos_con_tecnica();
            $tecnicas = $this->tecnica_model->obtener_activas();
            
            $datos = [
                'cursos' => $cursos,
                'tecnicas' => $tecnicas
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/admin/cursos.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en cursos admin: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar cursos');
            redireccionar(APP_URL . '/index.php?action=admin_dashboard');
        }
    }
    
    /**
     * Gestión de semestres
     */
    public function semestres() {
        try {
            $semestres = $this->semestre_model->obtener_todos();
            
            $datos = [
                'semestres' => $semestres
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/admin/semestres.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en semestres admin: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar semestres');
            redireccionar(APP_URL . '/index.php?action=admin_dashboard');
        }
    }
    
    /**
     * Gestión de asignaciones
     */
    public function asignaciones() {
        try {
            $asignaciones = $this->asignacion_model->obtener_todas_con_detalles();
            $docentes = $this->user_model->obtener_docentes();
            $cursos = $this->curso_model->obtener_activos();
            $semestres = $this->semestre_model->obtener_todos();
            
            $datos = [
                'asignaciones' => $asignaciones,
                'docentes' => $docentes,
                'cursos' => $cursos,
                'semestres' => $semestres
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/admin/asignaciones.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en asignaciones admin: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar asignaciones');
            redireccionar(APP_URL . '/index.php?action=admin_dashboard');
        }
    }
    
    /**
     * Gestión de inscripciones
     */
    public function inscripciones() {
        try {
            $inscripciones = $this->inscripcion_model->obtener_todas_con_detalles();
            $estudiantes = $this->user_model->obtener_estudiantes();
            $cursos = $this->curso_model->obtener_activos();
            $semestres = $this->semestre_model->obtener_todos();
            
            $datos = [
                'inscripciones' => $inscripciones,
                'estudiantes' => $estudiantes,
                'cursos' => $cursos,
                'semestres' => $semestres
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/admin/inscripciones.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en inscripciones admin: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar inscripciones');
            redireccionar(APP_URL . '/index.php?action=admin_dashboard');
        }
    }
    
    /**
     * Manejar peticiones AJAX
     */
    public function manejar_ajax($action) {
        try {
            switch ($action) {
                // Usuarios
                case 'ajax_crear_usuario':
                    $this->ajax_crear_usuario();
                    break;
                case 'ajax_actualizar_usuario':
                    $this->ajax_actualizar_usuario();
                    break;
                case 'ajax_eliminar_usuario':
                    $this->ajax_eliminar_usuario();
                    break;
                case 'ajax_obtener_usuario':
                    $this->ajax_obtener_usuario();
                    break;
                    
                // Técnicas
                case 'ajax_crear_tecnica':
                    $this->ajax_crear_tecnica();
                    break;
                case 'ajax_actualizar_tecnica':
                    $this->ajax_actualizar_tecnica();
                    break;
                case 'ajax_eliminar_tecnica':
                    $this->ajax_eliminar_tecnica();
                    break;
                case 'ajax_obtener_tecnica':
                    $this->ajax_obtener_tecnica();
                    break;
                    
                // Cursos
                case 'ajax_crear_curso':
                    $this->ajax_crear_curso();
                    break;
                case 'ajax_actualizar_curso':
                    $this->ajax_actualizar_curso();
                    break;
                case 'ajax_eliminar_curso':
                    $this->ajax_eliminar_curso();
                    break;
                case 'ajax_obtener_curso':
                    $this->ajax_obtener_curso();
                    break;
                    
                // Semestres
                case 'ajax_crear_semestre':
                    $this->ajax_crear_semestre();
                    break;
                case 'ajax_actualizar_semestre':
                    $this->ajax_actualizar_semestre();
                    break;
                case 'ajax_eliminar_semestre':
                    $this->ajax_eliminar_semestre();
                    break;
                case 'ajax_obtener_semestre':
                    $this->ajax_obtener_semestre();
                    break;
                    
                // Asignaciones
                case 'ajax_crear_asignacion':
                    $this->ajax_crear_asignacion();
                    break;
                case 'ajax_eliminar_asignacion':
                    $this->ajax_eliminar_asignacion();
                    break;
                    
                // Inscripciones
                case 'ajax_crear_inscripcion':
                    $this->ajax_crear_inscripcion();
                    break;
                case 'ajax_eliminar_inscripcion':
                    $this->ajax_eliminar_inscripcion();
                    break;
                    
                default:
                    respuesta_json(false, 'Acción no válida');
            }
        } catch (Exception $e) {
            error_log("Error en AJAX admin: " . $e->getMessage());
            respuesta_json(false, 'Error interno del servidor');
        }
    }
    
    // Métodos AJAX para Usuarios
    private function ajax_crear_usuario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $datos = $_POST;
        $errores = $this->user_model->validar($datos);
        
        if (!empty($errores)) {
            respuesta_json(false, implode(', ', $errores));
        }
        
        if ($this->user_model->crear($datos)) {
            log_actividad("Usuario creado: {$datos['email']}", 'INFO');
            respuesta_json(true, 'Usuario creado exitosamente');
        } else {
            respuesta_json(false, 'Error al crear usuario');
        }
    }
    
    private function ajax_actualizar_usuario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        $datos = $_POST;
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de usuario inválido');
        }
        
        $errores = $this->user_model->validar($datos, true);
        
        if (!empty($errores)) {
            respuesta_json(false, implode(', ', $errores));
        }
        
        if ($this->user_model->actualizar($id, $datos)) {
            log_actividad("Usuario actualizado: ID {$id}", 'INFO');
            respuesta_json(true, 'Usuario actualizado exitosamente');
        } else {
            respuesta_json(false, 'Error al actualizar usuario');
        }
    }
    
    private function ajax_eliminar_usuario() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de usuario inválido');
        }
        
        // No permitir eliminar el usuario actual
        $usuario_actual = SessionManager::obtener_usuario();
        if ($id == $usuario_actual['id']) {
            respuesta_json(false, 'No puedes eliminar tu propio usuario');
        }
        
        if ($this->user_model->eliminar($id)) {
            log_actividad("Usuario eliminado: ID {$id}", 'WARNING');
            respuesta_json(true, 'Usuario eliminado exitosamente');
        } else {
            respuesta_json(false, 'Error al eliminar usuario');
        }
    }
    
    private function ajax_obtener_usuario() {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de usuario inválido');
        }
        
        $usuario = $this->user_model->obtener_por_id($id);
        
        if ($usuario) {
            respuesta_json(true, 'Usuario encontrado', $usuario);
        } else {
            respuesta_json(false, 'Usuario no encontrado');
        }
    }
    
    // Métodos AJAX para Técnicas
    private function ajax_crear_tecnica() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $datos = $_POST;
        $errores = $this->tecnica_model->validar($datos);
        
        if (!empty($errores)) {
            respuesta_json(false, implode(', ', $errores));
        }
        
        if ($this->tecnica_model->crear($datos)) {
            log_actividad("Técnica creada: {$datos['nombre']}", 'INFO');
            respuesta_json(true, 'Técnica creada exitosamente');
        } else {
            respuesta_json(false, 'Error al crear técnica');
        }
    }
    
    private function ajax_actualizar_tecnica() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        $datos = $_POST;
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de técnica inválido');
        }
        
        $errores = $this->tecnica_model->validar($datos, true);
        
        if (!empty($errores)) {
            respuesta_json(false, implode(', ', $errores));
        }
        
        if ($this->tecnica_model->actualizar($id, $datos)) {
            log_actividad("Técnica actualizada: ID {$id}", 'INFO');
            respuesta_json(true, 'Técnica actualizada exitosamente');
        } else {
            respuesta_json(false, 'Error al actualizar técnica');
        }
    }
    
    private function ajax_eliminar_tecnica() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de técnica inválido');
        }
        
        if ($this->tecnica_model->eliminar($id)) {
            log_actividad("Técnica eliminada: ID {$id}", 'WARNING');
            respuesta_json(true, 'Técnica eliminada exitosamente');
        } else {
            respuesta_json(false, 'Error al eliminar técnica');
        }
    }
    
    private function ajax_obtener_tecnica() {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de técnica inválido');
        }
        
        $tecnica = $this->tecnica_model->obtener_por_id($id);
        
        if ($tecnica) {
            respuesta_json(true, 'Técnica encontrada', $tecnica);
        } else {
            respuesta_json(false, 'Técnica no encontrada');
        }
    }
    
    // Métodos AJAX para Cursos
    private function ajax_crear_curso() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $datos = $_POST;
        $errores = $this->curso_model->validar($datos);
        
        if (!empty($errores)) {
            respuesta_json(false, implode(', ', $errores));
        }
        
        if ($this->curso_model->crear($datos)) {
            log_actividad("Curso creado: {$datos['nombre']}", 'INFO');
            respuesta_json(true, 'Curso creado exitosamente');
        } else {
            respuesta_json(false, 'Error al crear curso');
        }
    }
    
    private function ajax_actualizar_curso() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        $datos = $_POST;
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de curso inválido');
        }
        
        $errores = $this->curso_model->validar($datos, true);
        
        if (!empty($errores)) {
            respuesta_json(false, implode(', ', $errores));
        }
        
        if ($this->curso_model->actualizar($id, $datos)) {
            log_actividad("Curso actualizado: ID {$id}", 'INFO');
            respuesta_json(true, 'Curso actualizado exitosamente');
        } else {
            respuesta_json(false, 'Error al actualizar curso');
        }
    }
    
    private function ajax_eliminar_curso() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de curso inválido');
        }
        
        if ($this->curso_model->eliminar($id)) {
            log_actividad("Curso eliminado: ID {$id}", 'WARNING');
            respuesta_json(true, 'Curso eliminado exitosamente');
        } else {
            respuesta_json(false, 'Error al eliminar curso');
        }
    }
    
    private function ajax_obtener_curso() {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de curso inválido');
        }
        
        $curso = $this->curso_model->obtener_por_id($id);
        
        if ($curso) {
            respuesta_json(true, 'Curso encontrado', $curso);
        } else {
            respuesta_json(false, 'Curso no encontrado');
        }
    }
    
    // Métodos AJAX para Semestres
    private function ajax_crear_semestre() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                respuesta_json(false, 'Método no permitido');
            }
            
            $datos = $_POST;
            $errores = $this->semestre_model->validar($datos);
            
            if (!empty($errores)) {
                respuesta_json(false, implode(', ', $errores));
            }
            
            if ($this->semestre_model->crear($datos)) {
                log_actividad("Semestre creado: {$datos['nombre']}", 'INFO');
                respuesta_json(true, 'Semestre creado exitosamente');
            } else {
                respuesta_json(false, 'Error al crear semestre en la base de datos');
            }
        } catch (Exception $e) {
            error_log("Error al crear semestre: " . $e->getMessage());
            respuesta_json(false, 'Error al crear semestre: ' . $e->getMessage());
        }
    }
    
    private function ajax_actualizar_semestre() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        $datos = $_POST;
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de semestre inválido');
        }
        
        $errores = $this->semestre_model->validar($datos, true);
        
        if (!empty($errores)) {
            respuesta_json(false, implode(', ', $errores));
        }
        
        if ($this->semestre_model->actualizar($id, $datos)) {
            log_actividad("Semestre actualizado: ID {$id}", 'INFO');
            respuesta_json(true, 'Semestre actualizado exitosamente');
        } else {
            respuesta_json(false, 'Error al actualizar semestre');
        }
    }
    
    private function ajax_eliminar_semestre() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de semestre inválido');
        }
        
        if ($this->semestre_model->eliminar($id)) {
            log_actividad("Semestre eliminado: ID {$id}", 'WARNING');
            respuesta_json(true, 'Semestre eliminado exitosamente');
        } else {
            respuesta_json(false, 'Error al eliminar semestre');
        }
    }
    
    private function ajax_obtener_semestre() {
        $id = intval($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de semestre inválido');
        }
        
        $semestre = $this->semestre_model->obtener_por_id($id);
        
        if ($semestre) {
            respuesta_json(true, 'Semestre encontrado', $semestre);
        } else {
            respuesta_json(false, 'Semestre no encontrado');
        }
    }
    
    // Métodos AJAX para Asignaciones
    private function ajax_crear_asignacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $datos = $_POST;
        $errores = $this->asignacion_model->validar($datos);
        
        if (!empty($errores)) {
            respuesta_json(false, implode(', ', $errores));
        }
        
        if ($this->asignacion_model->crear($datos)) {
            log_actividad("Asignación creada: Docente {$datos['docente_id']} - Curso {$datos['curso_id']}", 'INFO');
            respuesta_json(true, 'Asignación creada exitosamente');
        } else {
            respuesta_json(false, 'Error al crear asignación');
        }
    }
    
    private function ajax_eliminar_asignacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de asignación inválido');
        }
        
        if ($this->asignacion_model->eliminar($id)) {
            log_actividad("Asignación eliminada: ID {$id}", 'WARNING');
            respuesta_json(true, 'Asignación eliminada exitosamente');
        } else {
            respuesta_json(false, 'Error al eliminar asignación');
        }
    }
    
    // Métodos AJAX para Inscripciones
    private function ajax_crear_inscripcion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $datos = $_POST;
        $errores = $this->inscripcion_model->validar($datos);
        
        if (!empty($errores)) {
            respuesta_json(false, implode(', ', $errores));
        }
        
        if ($this->inscripcion_model->crear($datos)) {
            log_actividad("Inscripción creada: Estudiante {$datos['estudiante_id']} - Curso {$datos['curso_id']}", 'INFO');
            respuesta_json(true, 'Inscripción creada exitosamente');
        } else {
            respuesta_json(false, 'Error al crear inscripción');
        }
    }
    
    private function ajax_eliminar_inscripcion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            respuesta_json(false, 'ID de inscripción inválido');
        }
        
        if ($this->inscripcion_model->eliminar($id)) {
            log_actividad("Inscripción eliminada: ID {$id}", 'WARNING');
            respuesta_json(true, 'Inscripción eliminada exitosamente');
        } else {
            respuesta_json(false, 'Error al eliminar inscripción');
        }
    }
}
?>
