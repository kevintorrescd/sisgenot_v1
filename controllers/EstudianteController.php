<?php
/**
 * Controlador de Estudiante
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Curso.php';
require_once dirname(__DIR__) . '/models/Semestre.php';
require_once dirname(__DIR__) . '/models/Inscripcion.php';
require_once dirname(__DIR__) . '/models/Calificacion.php';

class EstudianteController {
    private $db;
    private $user_model;
    private $curso_model;
    private $semestre_model;
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
        $this->inscripcion_model = new Inscripcion($this->db);
        $this->calificacion_model = new Calificacion($this->db);
    }
    
    /**
     * Dashboard del estudiante
     */
    public function dashboard() {
        try {
            $usuario_actual = SessionManager::obtener_usuario();
            $estudiante_id = $usuario_actual['id'];
            
            // Obtener semestre activo
            $semestre_activo = $this->semestre_model->obtener_activo();
            
            if (!$semestre_activo) {
                SessionManager::establecer_mensaje('warning', 'No hay semestre activo configurado');
            }
            
            // Obtener inscripciones del estudiante
            $inscripciones = [];
            $total_cursos = 0;
            $total_calificaciones = 0;
            $promedio_general = 0;
            $cursos_aprobados = 0;
            $cursos_reprobados = 0;
            
            if ($semestre_activo) {
                $inscripciones = $this->inscripcion_model->obtener_por_estudiante($estudiante_id, $semestre_activo['id']);
                $total_cursos = count($inscripciones);
                
                $promedios_cursos = [];
                
                foreach ($inscripciones as &$inscripcion) {
                    // Obtener calificaciones para cada curso
                    $calificaciones = $this->calificacion_model->obtener_por_inscripcion($inscripcion['id']);
                    $inscripcion['calificaciones'] = $calificaciones;
                    $inscripcion['total_calificaciones'] = count($calificaciones);
                    $total_calificaciones += count($calificaciones);
                    
                    if (!empty($calificaciones)) {
                        $notas = array_column($calificaciones, 'nota');
                        $promedio_curso = calcular_promedio($notas);
                        $inscripcion['promedio'] = $promedio_curso;
                        $inscripcion['estado'] = estado_aprobacion($promedio_curso);
                        $promedios_cursos[] = $promedio_curso;
                        
                        if ($promedio_curso >= NOTA_APROBACION) {
                            $cursos_aprobados++;
                        } else {
                            $cursos_reprobados++;
                        }
                    } else {
                        $inscripcion['promedio'] = 0;
                        $inscripcion['estado'] = 'Sin calificar';
                    }
                }
                
                // Calcular promedio general
                if (!empty($promedios_cursos)) {
                    $promedio_general = calcular_promedio($promedios_cursos);
                }
            }
            
            // Obtener actividad reciente (últimas calificaciones)
            $actividad_reciente = $this->calificacion_model->obtener_por_estudiante($estudiante_id, $semestre_activo ? $semestre_activo['id'] : null);
            $actividad_reciente = array_slice($actividad_reciente, 0, 5); // Últimas 5
            
            $datos = [
                'semestre_activo' => $semestre_activo,
                'inscripciones' => $inscripciones,
                'total_cursos' => $total_cursos,
                'total_calificaciones' => $total_calificaciones,
                'promedio_general' => $promedio_general,
                'cursos_aprobados' => $cursos_aprobados,
                'cursos_reprobados' => $cursos_reprobados,
                'actividad_reciente' => $actividad_reciente
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/estudiante/dashboard.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en dashboard estudiante: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar el dashboard');
            redireccionar(APP_URL . '/index.php?action=login');
        }
    }
    
    /**
     * Ver notas del estudiante
     */
    public function notas() {
        try {
            $usuario_actual = SessionManager::obtener_usuario();
            $estudiante_id = $usuario_actual['id'];
            
            // Obtener parámetros
            $semestre_id = intval($_GET['semestre_id'] ?? 0);
            
            // Obtener semestre activo si no se especifica uno
            if ($semestre_id <= 0) {
                $semestre_activo = $this->semestre_model->obtener_activo();
                $semestre_id = $semestre_activo ? $semestre_activo['id'] : 0;
            }
            
            // Obtener todos los semestres para el selector
            $semestres = $this->semestre_model->obtener_todos();
            
            // Obtener semestre seleccionado
            $semestre_seleccionado = null;
            if ($semestre_id > 0) {
                $semestre_seleccionado = $this->semestre_model->obtener_por_id($semestre_id);
            }
            
            // Obtener inscripciones y calificaciones del estudiante
            $inscripciones_con_notas = [];
            $promedio_general = 0;
            $total_calificaciones = 0;
            
            if ($semestre_seleccionado) {
                $inscripciones = $this->inscripcion_model->obtener_por_estudiante($estudiante_id, $semestre_id);
                $promedios_cursos = [];
                
                foreach ($inscripciones as $inscripcion) {
                    // Obtener calificaciones detalladas
                    $calificaciones = $this->calificacion_model->obtener_por_inscripcion($inscripcion['id']);
                    
                    $inscripcion_data = [
                        'inscripcion' => $inscripcion,
                        'calificaciones' => $calificaciones,
                        'total_calificaciones' => count($calificaciones)
                    ];
                    
                    if (!empty($calificaciones)) {
                        $notas = array_column($calificaciones, 'nota');
                        $promedio_curso = calcular_promedio($notas);
                        $inscripcion_data['promedio'] = $promedio_curso;
                        $inscripcion_data['estado'] = estado_aprobacion($promedio_curso);
                        $inscripcion_data['porcentaje_progreso'] = calcular_porcentaje_progreso($promedio_curso);
                        $promedios_cursos[] = $promedio_curso;
                        $total_calificaciones += count($calificaciones);
                    } else {
                        $inscripcion_data['promedio'] = 0;
                        $inscripcion_data['estado'] = 'Sin calificar';
                        $inscripcion_data['porcentaje_progreso'] = 0;
                    }
                    
                    $inscripciones_con_notas[] = $inscripcion_data;
                }
                
                // Calcular promedio general
                if (!empty($promedios_cursos)) {
                    $promedio_general = calcular_promedio($promedios_cursos);
                }
            }
            
            $datos = [
                'semestres' => $semestres,
                'semestre_seleccionado' => $semestre_seleccionado,
                'inscripciones_con_notas' => $inscripciones_con_notas,
                'promedio_general' => $promedio_general,
                'total_calificaciones' => $total_calificaciones,
                'total_cursos' => count($inscripciones_con_notas)
            ];
            
            include VIEWS_PATH . '/layouts/header.php';
            include VIEWS_PATH . '/estudiante/notas.php';
            include VIEWS_PATH . '/layouts/footer.php';
            
        } catch (Exception $e) {
            error_log("Error en notas estudiante: " . $e->getMessage());
            SessionManager::establecer_mensaje('error', 'Error al cargar las notas');
            redireccionar(APP_URL . '/index.php?action=estudiante_dashboard');
        }
    }
    
    /**
     * Manejar peticiones AJAX
     */
    public function manejar_ajax($action) {
        try {
            switch ($action) {
                case 'ajax_obtener_notas_curso':
                    $this->ajax_obtener_notas_curso();
                    break;
                case 'ajax_obtener_estadisticas_estudiante':
                    $this->ajax_obtener_estadisticas_estudiante();
                    break;
                default:
                    respuesta_json(false, 'Acción no válida');
            }
        } catch (Exception $e) {
            error_log("Error en AJAX estudiante: " . $e->getMessage());
            respuesta_json(false, 'Error interno del servidor');
        }
    }
    
    /**
     * AJAX: Obtener notas de un curso específico
     */
    private function ajax_obtener_notas_curso() {
        $inscripcion_id = intval($_GET['inscripcion_id'] ?? 0);
        $usuario_actual = SessionManager::obtener_usuario();
        $estudiante_id = $usuario_actual['id'];
        
        if ($inscripcion_id <= 0) {
            respuesta_json(false, 'ID de inscripción inválido');
        }
        
        // Verificar que la inscripción pertenece al estudiante
        $inscripcion = $this->inscripcion_model->obtener_por_id($inscripcion_id);
        
        if (!$inscripcion || $inscripcion['estudiante_id'] != $estudiante_id) {
            respuesta_json(false, 'No tienes permisos para ver estas notas');
        }
        
        // Obtener calificaciones
        $calificaciones = $this->calificacion_model->obtener_por_inscripcion($inscripcion_id);
        
        // Calcular estadísticas
        $promedio = 0;
        $nota_maxima = 0;
        $nota_minima = 0;
        
        if (!empty($calificaciones)) {
            $notas = array_column($calificaciones, 'nota');
            $promedio = calcular_promedio($notas);
            $nota_maxima = max($notas);
            $nota_minima = min($notas);
        }
        
        $datos = [
            'calificaciones' => $calificaciones,
            'promedio' => $promedio,
            'estado' => estado_aprobacion($promedio),
            'nota_maxima' => $nota_maxima,
            'nota_minima' => $nota_minima,
            'total_calificaciones' => count($calificaciones),
            'porcentaje_progreso' => calcular_porcentaje_progreso($promedio)
        ];
        
        respuesta_json(true, 'Notas obtenidas exitosamente', $datos);
    }
    
    /**
     * AJAX: Obtener estadísticas generales del estudiante
     */
    private function ajax_obtener_estadisticas_estudiante() {
        $semestre_id = intval($_GET['semestre_id'] ?? 0);
        $usuario_actual = SessionManager::obtener_usuario();
        $estudiante_id = $usuario_actual['id'];
        
        // Si no se especifica semestre, usar el activo
        if ($semestre_id <= 0) {
            $semestre_activo = $this->semestre_model->obtener_activo();
            $semestre_id = $semestre_activo ? $semestre_activo['id'] : 0;
        }
        
        if ($semestre_id <= 0) {
            respuesta_json(false, 'No hay semestre válido');
        }
        
        // Obtener inscripciones del estudiante
        $inscripciones = $this->inscripcion_model->obtener_por_estudiante($estudiante_id, $semestre_id);
        
        $estadisticas = [
            'total_cursos' => count($inscripciones),
            'cursos_con_notas' => 0,
            'cursos_aprobados' => 0,
            'cursos_reprobados' => 0,
            'total_calificaciones' => 0,
            'promedio_general' => 0,
            'mejor_nota' => 0,
            'peor_nota' => 0,
            'distribucion_notas' => [
                'excelente' => 0, // >= 4.5
                'bueno' => 0,     // >= 4.0 y < 4.5
                'aceptable' => 0, // >= 3.0 y < 4.0
                'deficiente' => 0 // < 3.0
            ]
        ];
        
        $promedios_cursos = [];
        $todas_las_notas = [];
        
        foreach ($inscripciones as $inscripcion) {
            $calificaciones = $this->calificacion_model->obtener_por_inscripcion($inscripcion['id']);
            
            if (!empty($calificaciones)) {
                $estadisticas['cursos_con_notas']++;
                $estadisticas['total_calificaciones'] += count($calificaciones);
                
                $notas = array_column($calificaciones, 'nota');
                $promedio_curso = calcular_promedio($notas);
                $promedios_cursos[] = $promedio_curso;
                $todas_las_notas = array_merge($todas_las_notas, $notas);
                
                if ($promedio_curso >= NOTA_APROBACION) {
                    $estadisticas['cursos_aprobados']++;
                } else {
                    $estadisticas['cursos_reprobados']++;
                }
                
                // Clasificar notas para distribución
                foreach ($notas as $nota) {
                    if ($nota >= 4.5) {
                        $estadisticas['distribucion_notas']['excelente']++;
                    } elseif ($nota >= 4.0) {
                        $estadisticas['distribucion_notas']['bueno']++;
                    } elseif ($nota >= 3.0) {
                        $estadisticas['distribucion_notas']['aceptable']++;
                    } else {
                        $estadisticas['distribucion_notas']['deficiente']++;
                    }
                }
            }
        }
        
        // Calcular estadísticas generales
        if (!empty($promedios_cursos)) {
            $estadisticas['promedio_general'] = calcular_promedio($promedios_cursos);
        }
        
        if (!empty($todas_las_notas)) {
            $estadisticas['mejor_nota'] = max($todas_las_notas);
            $estadisticas['peor_nota'] = min($todas_las_notas);
        }
        
        respuesta_json(true, 'Estadísticas obtenidas exitosamente', $estadisticas);
    }
}
?>
