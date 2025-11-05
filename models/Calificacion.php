<?php
/**
 * Modelo de Calificación
 */

class Calificacion {
    private $conn;
    private $tabla = 'calificaciones';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener calificación por ID
     * @param int $id
     * @return array|false
     */
    public function obtener_por_id($id) {
        $query = "SELECT id, inscripcion_id, nota, observaciones, fecha_registro, fecha_actualizacion 
                  FROM " . $this->tabla . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener calificaciones por inscripción
     * @param int $inscripcion_id
     * @return array
     */
    public function obtener_por_inscripcion($inscripcion_id) {
        $query = "SELECT id, inscripcion_id, nota, observaciones, fecha_registro, fecha_actualizacion 
                  FROM " . $this->tabla . " 
                  WHERE inscripcion_id = :inscripcion_id 
                  ORDER BY fecha_registro DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':inscripcion_id', $inscripcion_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener todas las calificaciones de un estudiante
     * @param int $estudiante_id
     * @param int $semestre_id (opcional)
     * @return array
     */
    public function obtener_por_estudiante($estudiante_id, $semestre_id = null) {
        $query = "SELECT c.id, c.inscripcion_id, c.nota, c.observaciones, c.fecha_registro,
                         cur.codigo as curso_codigo, cur.nombre as curso_nombre,
                         t.nombre as tecnica_nombre,
                         s.nombre as semestre_nombre
                  FROM " . $this->tabla . " c
                  INNER JOIN inscripciones i ON c.inscripcion_id = i.id
                  INNER JOIN cursos cur ON i.curso_id = cur.id
                  INNER JOIN tecnicas t ON cur.tecnica_id = t.id
                  INNER JOIN semestres s ON i.semestre_id = s.id
                  WHERE i.estudiante_id = :estudiante_id";
        
        if ($semestre_id) {
            $query .= " AND i.semestre_id = :semestre_id";
        }
        
        $query .= " ORDER BY s.fecha_inicio DESC, t.nombre, cur.nombre, c.fecha_registro DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudiante_id', $estudiante_id);
        
        if ($semestre_id) {
            $stmt->bindParam(':semestre_id', $semestre_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener calificaciones recientes por docente
     * @param int $docente_id
     * @param int $limite
     * @return array
     */
    public function obtener_recientes_por_docente($docente_id, $limite = 10) {
        $query = "SELECT c.id, c.nota, c.fecha_registro,
                         CONCAT(u.nombre, ' ', u.apellido) as estudiante_nombre,
                         cur.codigo as curso_codigo, cur.nombre as curso_nombre
                  FROM " . $this->tabla . " c
                  INNER JOIN inscripciones i ON c.inscripcion_id = i.id
                  INNER JOIN usuarios u ON i.estudiante_id = u.id
                  INNER JOIN cursos cur ON i.curso_id = cur.id
                  INNER JOIN asignaciones a ON (cur.id = a.curso_id AND i.semestre_id = a.semestre_id)
                  WHERE a.docente_id = :docente_id
                  ORDER BY c.fecha_registro DESC
                  LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':docente_id', $docente_id);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Crear nueva calificación
     * @param array $datos
     * @return bool
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->tabla . " 
                  (inscripcion_id, nota, observaciones) 
                  VALUES (:inscripcion_id, :nota, :observaciones)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $datos['inscripcion_id'] = intval($datos['inscripcion_id']);
        $datos['nota'] = floatval($datos['nota']);
        $datos['observaciones'] = limpiar_entrada($datos['observaciones'] ?? '');
        
        // Bind parameters
        $stmt->bindParam(':inscripcion_id', $datos['inscripcion_id']);
        $stmt->bindParam(':nota', $datos['nota']);
        $stmt->bindParam(':observaciones', $datos['observaciones']);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar calificación
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar($id, $datos) {
        // Construir query dinámicamente
        $campos = [];
        $parametros = [':id' => $id];
        
        if (isset($datos['nota'])) {
            $campos[] = "nota = :nota";
            $parametros[':nota'] = floatval($datos['nota']);
        }
        
        if (isset($datos['observaciones'])) {
            $campos[] = "observaciones = :observaciones";
            $parametros[':observaciones'] = limpiar_entrada($datos['observaciones']);
        }
        
        if (empty($campos)) {
            return false;
        }
        
        $query = "UPDATE " . $this->tabla . " SET " . implode(', ', $campos) . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($parametros as $param => $valor) {
            $stmt->bindValue($param, $valor);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar calificación
     * @param int $id
     * @return bool
     */
    public function eliminar($id) {
        $query = "DELETE FROM " . $this->tabla . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Calcular promedio por inscripción
     * @param int $inscripcion_id
     * @return float
     */
    public function calcular_promedio_inscripcion($inscripcion_id) {
        $query = "SELECT AVG(nota) as promedio FROM " . $this->tabla . " WHERE inscripcion_id = :inscripcion_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':inscripcion_id', $inscripcion_id);
        $stmt->execute();
        
        $resultado = $stmt->fetch();
        return $resultado ? floatval($resultado['promedio']) : 0.0;
    }
    
    /**
     * Calcular promedio general de un estudiante
     * @param int $estudiante_id
     * @param int $semestre_id (opcional)
     * @return float
     */
    public function calcular_promedio_estudiante($estudiante_id, $semestre_id = null) {
        // Obtener promedio por curso
        $query = "SELECT AVG(c.nota) as promedio_curso
                  FROM " . $this->tabla . " c
                  INNER JOIN inscripciones i ON c.inscripcion_id = i.id
                  WHERE i.estudiante_id = :estudiante_id";
        
        if ($semestre_id) {
            $query .= " AND i.semestre_id = :semestre_id";
        }
        
        $query .= " GROUP BY i.curso_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudiante_id', $estudiante_id);
        
        if ($semestre_id) {
            $stmt->bindParam(':semestre_id', $semestre_id);
        }
        
        $stmt->execute();
        $promedios_cursos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($promedios_cursos)) {
            return 0.0;
        }
        
        return array_sum($promedios_cursos) / count($promedios_cursos);
    }
    
    /**
     * Obtener estadísticas de calificaciones por curso
     * @param int $curso_id
     * @param int $semestre_id
     * @return array
     */
    public function obtener_estadisticas_curso($curso_id, $semestre_id) {
        $query = "SELECT 
                    COUNT(c.id) as total_calificaciones,
                    AVG(c.nota) as promedio_general,
                    MIN(c.nota) as nota_minima,
                    MAX(c.nota) as nota_maxima,
                    COUNT(DISTINCT i.estudiante_id) as estudiantes_calificados
                  FROM " . $this->tabla . " c
                  INNER JOIN inscripciones i ON c.inscripcion_id = i.id
                  WHERE i.curso_id = :curso_id AND i.semestre_id = :semestre_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':curso_id', $curso_id);
        $stmt->bindParam(':semestre_id', $semestre_id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Contar calificaciones por rango
     * @param int $curso_id
     * @param int $semestre_id
     * @return array
     */
    public function contar_por_rangos($curso_id, $semestre_id) {
        $query = "SELECT 
                    SUM(CASE WHEN c.nota >= 4.5 THEN 1 ELSE 0 END) as excelente,
                    SUM(CASE WHEN c.nota >= 4.0 AND c.nota < 4.5 THEN 1 ELSE 0 END) as bueno,
                    SUM(CASE WHEN c.nota >= 3.0 AND c.nota < 4.0 THEN 1 ELSE 0 END) as aceptable,
                    SUM(CASE WHEN c.nota < 3.0 THEN 1 ELSE 0 END) as deficiente
                  FROM " . $this->tabla . " c
                  INNER JOIN inscripciones i ON c.inscripcion_id = i.id
                  WHERE i.curso_id = :curso_id AND i.semestre_id = :semestre_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':curso_id', $curso_id);
        $stmt->bindParam(':semestre_id', $semestre_id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener historial de calificaciones con detalles
     * @param int $inscripcion_id
     * @return array
     */
    public function obtener_historial_detallado($inscripcion_id) {
        $query = "SELECT c.id, c.nota, c.observaciones, c.fecha_registro, c.fecha_actualizacion,
                         CONCAT(u.nombre, ' ', u.apellido) as docente_nombre
                  FROM " . $this->tabla . " c
                  INNER JOIN inscripciones i ON c.inscripcion_id = i.id
                  INNER JOIN asignaciones a ON (i.curso_id = a.curso_id AND i.semestre_id = a.semestre_id)
                  INNER JOIN usuarios u ON a.docente_id = u.id
                  WHERE c.inscripcion_id = :inscripcion_id
                  ORDER BY c.fecha_registro DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':inscripcion_id', $inscripcion_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Eliminar calificaciones por inscripción
     * @param int $inscripcion_id
     * @return bool
     */
    public function eliminar_por_inscripcion($inscripcion_id) {
        $query = "DELETE FROM " . $this->tabla . " WHERE inscripcion_id = :inscripcion_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':inscripcion_id', $inscripcion_id);
        
        return $stmt->execute();
    }
    
    /**
     * Validar datos de calificación
     * @param array $datos
     * @return array Array de errores
     */
    public function validar($datos) {
        $errores = [];
        
        // Validar inscripcion_id
        if (empty($datos['inscripcion_id'])) {
            $errores[] = 'La inscripción es requerida';
        } elseif (!is_numeric($datos['inscripcion_id']) || intval($datos['inscripcion_id']) <= 0) {
            $errores[] = 'La inscripción seleccionada no es válida';
        }
        
        // Validar nota
        if (!isset($datos['nota']) || $datos['nota'] === '') {
            $errores[] = 'La nota es requerida';
        } elseif (!is_numeric($datos['nota'])) {
            $errores[] = 'La nota debe ser un número válido';
        } elseif (!es_nota_valida($datos['nota'])) {
            $errores[] = 'La nota debe estar entre ' . NOTA_MINIMA . ' y ' . NOTA_MAXIMA;
        }
        
        // Validar observaciones (opcional)
        if (isset($datos['observaciones']) && strlen($datos['observaciones']) > 500) {
            $errores[] = 'Las observaciones no pueden tener más de 500 caracteres';
        }
        
        return $errores;
    }
}
?>
