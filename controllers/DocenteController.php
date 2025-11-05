<?php
/**
 * Controlador de Docente
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Curso.php';
require_once dirname(__DIR__) . '/models/Semestre.php';
require_once dirname(__DIR__) . '/models/Asignacion.php';
require_once dirname(__DIR__) . '/models/Inscripcion.php';
require_once dirname(__DIR__) . '/models/Calificacion.php';

class DocenteController {
    private $db;
    private $user_model;
    private $curso_model;
    private $semestre_model;
    private $asignacion_model;
    private $inscripcion_model;
    private $calificacion_model;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        if (!$this->db) {
            SessionManager::establecer_mensaje('error', 'Error de conexión a la base de datos');
            redireccionar(APP_URL . '/index.php?action=login');
        }
        
        $this->user_model = new User($this->db);
        $this->curso_model = new Curso($this->db);
        $this->semestre_model = new Semestre($this->db);
        $this->asignacion_model = new Asignacion($this->db);
        $this->inscripcion_model = new Inscripcion($this->db);
        $this->calificacion_model = new Calificacion($this->db);
    }
    
    /**
     * Dashboard del docente
     */
    public function dashboard() {
        try {
            $usuario_actual = SessionManager::obtener_usuario();
            $docente_id = $usuario_actual['id'];
            
            // Obtener semestre activo
            $semestre_activo = $this->semestre_model->obtener_activo();
            
            if (!$semestre_activo) {
                SessionManager::establecer_mensaje('warning', 'No hay semestre activo configurado');
            }
            
            // Obtener cursos asignados al docente en el semestre activo
            $cursos_asignados = [];
            $total_estudiantes = 0;
            $total_calificaciones = 0;
            
            if ($semestre_activo) {
                $cursos_asignados = $this->asignacion_model->obtener_por_docente($docente_id, $semestre_activo['id']);
                
                // Calcular estadísticas
                foreach ($cursos_asignados as $curso) {
                    $estudiantes_curso = $this->inscripcion_model->obtener_por_curso($curso['curso_id'], $semestre_activo['id']);
                    $total_estudiantes += count($estudiantes_curso);
                    
                    foreach ($estudiantes_curso as $estudiante) {
                        // El ID de la inscripción viene como 'id', no 'inscripcion_id'
                        $calificaciones = $this->calificacion_model->obtener_por_inscripcion($estudiante['id']);
                        $total_calificaciones += count($calificaciones);
                    }
                }
            }
            
            // Obtener actividad reciente
            $actividad_reciente = $this->calificacion_model->obtener_recientes_por_docente($docente_id, 5);
            
            $datos = [
                'semestre_activo' => $semestre_activo,
                'cursos_asignados' => $cursos_asignados,
                'total_cursos' => count($cursos_asignados),
                'total_estudiantes' => $total_estudiantes,
                'total_calificaciones' => $total_calificaciones,
                'actividad_reciente' => $actividad_reciente
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/docente/dashboard.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en dashboard docente: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar el dashboard');
            redireccionar(APP_URL . '/index.php?action=login');
        }
    }
    
    /**
     * Ver cursos asignados
     */
    public function cursos() {
        try {
            $usuario_actual = SessionManager::obtener_usuario();
            $docente_id = $usuario_actual['id'];
            
            // Obtener semestre activo
            $semestre_activo = $this->semestre_model->obtener_activo();
            
            if (!$semestre_activo) {
                SessionManager::establecer_mensaje('warning', 'No hay semestre activo configurado');
                $cursos_con_estudiantes = [];
            } else {
                // Obtener cursos asignados con información de estudiantes
                $cursos_asignados = $this->asignacion_model->obtener_por_docente($docente_id, $semestre_activo['id']);
                $cursos_con_estudiantes = [];
                
                foreach ($cursos_asignados as $curso) {
                    $estudiantes = $this->inscripcion_model->obtener_estudiantes_curso($curso['curso_id'], $semestre_activo['id']);
                    $curso['estudiantes'] = $estudiantes;
                    $curso['total_estudiantes'] = count($estudiantes);
                    $cursos_con_estudiantes[] = $curso;
                }
            }
            
            $datos = [
                'semestre_activo' => $semestre_activo,
                'cursos_con_estudiantes' => $cursos_con_estudiantes
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/docente/cursos.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en cursos docente: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar cursos');
            redireccionar(APP_URL . '/index.php?action=docente_dashboard');
        }
    }
    
    /**
     * Calificar estudiantes
     */
    public function calificar() {
        try {
            $usuario_actual = SessionManager::obtener_usuario();
            $docente_id = $usuario_actual['id'];
            
            // Obtener parámetros
            $curso_id = intval($_GET['curso_id'] ?? 0);
            $estudiante_id = intval($_GET['estudiante_id'] ?? 0);
            
            // Obtener semestre activo
            $semestre_activo = $this->semestre_model->obtener_activo();
            
            if (!$semestre_activo) {
                SessionManager::establecer_mensaje('error', 'No hay semestre activo configurado');
                redireccionar(APP_URL . '/index.php?action=docente_cursos');
            }
            
            // Verificar que el docente esté asignado al curso
            $asignaciones = $this->asignacion_model->obtener_por_docente($docente_id, $semestre_activo['id']);
            $curso_asignado = null;
            
            foreach ($asignaciones as $asignacion) {
                // Convertir ambos a enteros para comparación segura
                if (intval($asignacion['curso_id']) === intval($curso_id)) {
                    $curso_asignado = $asignacion;
                    break;
                }
            }
            
            if (!$curso_asignado) {
                SessionManager::establecer_mensaje('error', 'No tienes permisos para calificar este curso. Ve a "Mis Cursos" para seleccionar un curso.');
                redireccionar(APP_URL . '/index.php?action=docente_cursos');
            }
            
            // Obtener estudiantes del curso
            $estudiantes = $this->inscripcion_model->obtener_estudiantes_curso($curso_id, $semestre_activo['id']);
            
            // Si se especifica un estudiante, obtener sus calificaciones
            $estudiante_seleccionado = null;
            $calificaciones_estudiante = [];
            $promedio_estudiante = 0;
            
            if ($estudiante_id > 0) {
                foreach ($estudiantes as $est) {
                    if ($est['estudiante_id'] == $estudiante_id) {
                        $estudiante_seleccionado = $est;
                        break;
                    }
                }
                
                if ($estudiante_seleccionado) {
                    $calificaciones_estudiante = $this->calificacion_model->obtener_por_inscripcion($estudiante_seleccionado['inscripcion_id']);
                    if (!empty($calificaciones_estudiante)) {
                        $notas = array_column($calificaciones_estudiante, 'nota');
                        $promedio_estudiante = calcular_promedio($notas);
                    }
                }
            }
            
            $datos = [
                'semestre_activo' => $semestre_activo,
                'curso_asignado' => $curso_asignado,
                'estudiantes' => $estudiantes,
                'estudiante_seleccionado' => $estudiante_seleccionado,
                'calificaciones_estudiante' => $calificaciones_estudiante,
                'promedio_estudiante' => $promedio_estudiante
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/docente/calificar.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en calificar docente: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar la página de calificaciones');
            redireccionar(APP_URL . '/index.php?action=docente_cursos');
        }
    }
    
    /**
     * Manejar peticiones AJAX
     */
    public function manejar_ajax($action) {
        try {
            switch ($action) {
                case 'ajax_obtener_estudiantes_curso':
                    $this->ajax_obtener_estudiantes_curso();
                    break;
                case 'ajax_guardar_calificacion':
                    $this->ajax_guardar_calificacion();
                    break;
                case 'ajax_obtener_calificaciones_estudiante':
                    $this->ajax_obtener_calificaciones_estudiante();
                    break;
                case 'ajax_eliminar_calificacion':
                    $this->ajax_eliminar_calificacion();
                    break;
                default:
                    respuesta_json(false, 'Acción no válida');
            }
        } catch (Exception $e) {
            error_log("Error en AJAX docente: " . $e->getMessage());
            respuesta_json(false, 'Error interno del servidor');
        }
    }
    
    /**
     * AJAX: Obtener estudiantes de un curso
     */
    private function ajax_obtener_estudiantes_curso() {
        $curso_id = intval($_GET['curso_id'] ?? 0);
        $usuario_actual = SessionManager::obtener_usuario();
        $docente_id = $usuario_actual['id'];
        
        if ($curso_id <= 0) {
            respuesta_json(false, 'ID de curso inválido');
        }
        
        // Obtener semestre activo
        $semestre_activo = $this->semestre_model->obtener_activo();
        
        if (!$semestre_activo) {
            respuesta_json(false, 'No hay semestre activo');
        }
        
        // Verificar que el docente esté asignado al curso
        $asignaciones = $this->asignacion_model->obtener_por_docente($docente_id, $semestre_activo['id']);
        $tiene_permiso = false;
        
        foreach ($asignaciones as $asignacion) {
            // Convertir ambos a enteros para comparación segura
            if (intval($asignacion['curso_id']) === intval($curso_id)) {
                $tiene_permiso = true;
                break;
            }
        }
        
        if (!$tiene_permiso) {
            respuesta_json(false, 'No tienes permisos para ver este curso');
        }
        
        // Obtener estudiantes
        $estudiantes = $this->inscripcion_model->obtener_estudiantes_curso($curso_id, $semestre_activo['id']);
        
        // Agregar información de calificaciones para cada estudiante
        foreach ($estudiantes as &$estudiante) {
            $calificaciones = $this->calificacion_model->obtener_por_inscripcion($estudiante['inscripcion_id']);
            $estudiante['total_calificaciones'] = count($calificaciones);
            
            if (!empty($calificaciones)) {
                $notas = array_column($calificaciones, 'nota');
                $estudiante['promedio'] = calcular_promedio($notas);
                $estudiante['estado'] = estado_aprobacion($estudiante['promedio']);
            } else {
                $estudiante['promedio'] = 0;
                $estudiante['estado'] = 'Sin calificar';
            }
        }
        
        respuesta_json(true, 'Estudiantes obtenidos exitosamente', $estudiantes);
    }
    
    /**
     * AJAX: Guardar calificación
     */
    private function ajax_guardar_calificacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $inscripcion_id = intval($_POST['inscripcion_id'] ?? 0);
        $nota = floatval($_POST['nota'] ?? 0);
        $observaciones = limpiar_entrada($_POST['observaciones'] ?? '');
        
        $usuario_actual = SessionManager::obtener_usuario();
        $docente_id = $usuario_actual['id'];
        
        // Validaciones
        if ($inscripcion_id <= 0) {
            respuesta_json(false, 'ID de inscripción inválido');
        }
        
        if (!validar_nota($nota)) {
            respuesta_json(false, 'La nota debe estar entre ' . NOTA_MINIMA . ' y ' . NOTA_MAXIMA);
        }
        
        // Verificar que la inscripción existe y el docente tiene permisos
        $inscripcion = $this->inscripcion_model->obtener_por_id($inscripcion_id);
        
        if (!$inscripcion) {
            respuesta_json(false, 'Inscripción no encontrada');
        }
        
        // Verificar permisos del docente
        $semestre_activo = $this->semestre_model->obtener_activo();
        
        if (!$semestre_activo || $inscripcion['semestre_id'] != $semestre_activo['id']) {
            respuesta_json(false, 'Solo se pueden calificar cursos del semestre activo');
        }
        
        $asignaciones = $this->asignacion_model->obtener_por_docente($docente_id, $semestre_activo['id']);
        $tiene_permiso = false;
        
        foreach ($asignaciones as $asignacion) {
            // Convertir ambos a enteros para comparación segura
            if (intval($asignacion['curso_id']) === intval($inscripcion['curso_id'])) {
                $tiene_permiso = true;
                break;
            }
        }
        
        if (!$tiene_permiso) {
            respuesta_json(false, 'No tienes permisos para calificar este curso');
        }
        
        // Crear la calificación
        $datos_calificacion = [
            'inscripcion_id' => $inscripcion_id,
            'nota' => $nota,
            'observaciones' => $observaciones
        ];
        
        if ($this->calificacion_model->crear($datos_calificacion)) {
            // Obtener información del estudiante para el log
            $estudiante = $this->user_model->obtener_por_id($inscripcion['estudiante_id']);
            $curso = $this->curso_model->obtener_por_id($inscripcion['curso_id']);
            
            log_actividad("Calificación registrada: {$estudiante['nombre']} {$estudiante['apellido']} - {$curso['nombre']} - Nota: {$nota}", 'INFO');
            
            respuesta_json(true, 'Calificación guardada exitosamente');
        } else {
            respuesta_json(false, 'Error al guardar la calificación');
        }
    }
    
    /**
     * AJAX: Obtener calificaciones de un estudiante
     */
    private function ajax_obtener_calificaciones_estudiante() {
        $inscripcion_id = intval($_GET['inscripcion_id'] ?? 0);
        $usuario_actual = SessionManager::obtener_usuario();
        $docente_id = $usuario_actual['id'];
        
        if ($inscripcion_id <= 0) {
            respuesta_json(false, 'ID de inscripción inválido');
        }
        
        // Verificar permisos
        $inscripcion = $this->inscripcion_model->obtener_por_id($inscripcion_id);
        
        if (!$inscripcion) {
            respuesta_json(false, 'Inscripción no encontrada');
        }
        
        $semestre_activo = $this->semestre_model->obtener_activo();
        $asignaciones = $this->asignacion_model->obtener_por_docente($docente_id, $semestre_activo['id']);
        $tiene_permiso = false;
        
        foreach ($asignaciones as $asignacion) {
            // Convertir ambos a enteros para comparación segura
            if (intval($asignacion['curso_id']) === intval($inscripcion['curso_id'])) {
                $tiene_permiso = true;
                break;
            }
        }
        
        if (!$tiene_permiso) {
            respuesta_json(false, 'No tienes permisos para ver estas calificaciones');
        }
        
        // Obtener calificaciones
        $calificaciones = $this->calificacion_model->obtener_por_inscripcion($inscripcion_id);
        
        // Calcular promedio
        $promedio = 0;
        if (!empty($calificaciones)) {
            $notas = array_column($calificaciones, 'nota');
            $promedio = calcular_promedio($notas);
        }
        
        $datos = [
            'calificaciones' => $calificaciones,
            'promedio' => $promedio,
            'estado' => estado_aprobacion($promedio),
            'total_calificaciones' => count($calificaciones)
        ];
        
        respuesta_json(true, 'Calificaciones obtenidas exitosamente', $datos);
    }
    
    /**
     * AJAX: Eliminar calificación
     */
    private function ajax_eliminar_calificacion() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            respuesta_json(false, 'Método no permitido');
        }
        
        $calificacion_id = intval($_POST['id'] ?? 0);
        $usuario_actual = SessionManager::obtener_usuario();
        $docente_id = $usuario_actual['id'];
        
        if ($calificacion_id <= 0) {
            respuesta_json(false, 'ID de calificación inválido');
        }
        
        // Obtener la calificación y verificar permisos
        $calificacion = $this->calificacion_model->obtener_por_id($calificacion_id);
        
        if (!$calificacion) {
            respuesta_json(false, 'Calificación no encontrada');
        }
        
        $inscripcion = $this->inscripcion_model->obtener_por_id($calificacion['inscripcion_id']);
        $semestre_activo = $this->semestre_model->obtener_activo();
        
        // Verificar que sea del semestre activo
        if (!$semestre_activo || $inscripcion['semestre_id'] != $semestre_activo['id']) {
            respuesta_json(false, 'Solo se pueden eliminar calificaciones del semestre activo');
        }
        
        // Verificar permisos del docente
        $asignaciones = $this->asignacion_model->obtener_por_docente($docente_id, $semestre_activo['id']);
        $tiene_permiso = false;
        
        foreach ($asignaciones as $asignacion) {
            // Convertir ambos a enteros para comparación segura
            if (intval($asignacion['curso_id']) === intval($inscripcion['curso_id'])) {
                $tiene_permiso = true;
                break;
            }
        }
        
        if (!$tiene_permiso) {
            respuesta_json(false, 'No tienes permisos para eliminar esta calificación');
        }
        
        if ($this->calificacion_model->eliminar($calificacion_id)) {
            log_actividad("Calificación eliminada: ID {$calificacion_id}", 'WARNING');
            respuesta_json(true, 'Calificación eliminada exitosamente');
        } else {
            respuesta_json(false, 'Error al eliminar la calificación');
        }
    }
}
?>
