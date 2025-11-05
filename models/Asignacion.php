<?php
/**
 * Modelo de Asignación
 */

class Asignacion {
    private $conn;
    private $tabla = 'asignaciones';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener asignación por ID
     * @param int $id
     * @return array|false
     */
    public function obtener_por_id($id) {
        $query = "SELECT id, docente_id, curso_id, semestre_id, fecha_asignacion 
                  FROM " . $this->tabla . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener todas las asignaciones
     * @return array
     */
    public function obtener_todas() {
        $query = "SELECT id, docente_id, curso_id, semestre_id, fecha_asignacion 
                  FROM " . $this->tabla . " 
                  ORDER BY fecha_asignacion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener todas las asignaciones con detalles
     * @return array
     */
    public function obtener_todas_con_detalles() {
        $query = "SELECT a.id, a.docente_id, a.curso_id, a.semestre_id, a.fecha_asignacion,
                         u.nombre as docente_nombre,
                         u.apellido as docente_apellido,
                         u.email as docente_email,
                         c.codigo as curso_codigo,
                         c.nombre as curso_nombre,
                         t.nombre as tecnica_nombre,
                         s.nombre as semestre_nombre,
                         s.estado as semestre_estado
                  FROM " . $this->tabla . " a
                  INNER JOIN usuarios u ON a.docente_id = u.id
                  INNER JOIN cursos c ON a.curso_id = c.id
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  INNER JOIN semestres s ON a.semestre_id = s.id
                  ORDER BY s.fecha_inicio DESC, t.nombre, c.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener asignaciones por docente
     * @param int $docente_id
     * @param int $semestre_id (opcional)
     * @return array
     */
    public function obtener_por_docente($docente_id, $semestre_id = null) {
        $query = "SELECT a.id, a.curso_id, a.semestre_id, a.fecha_asignacion,
                         c.codigo as curso_codigo,
                         c.nombre as curso_nombre,
                         t.nombre as tecnica_nombre,
                         s.nombre as semestre_nombre,
                         s.estado as semestre_estado
                  FROM " . $this->tabla . " a
                  INNER JOIN cursos c ON a.curso_id = c.id
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  INNER JOIN semestres s ON a.semestre_id = s.id
                  WHERE a.docente_id = :docente_id";
        
        if ($semestre_id) {
            $query .= " AND a.semestre_id = :semestre_id";
        }
        
        $query .= " ORDER BY s.fecha_inicio DESC, t.nombre, c.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':docente_id', $docente_id);
        
        if ($semestre_id) {
            $stmt->bindParam(':semestre_id', $semestre_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener asignaciones por curso
     * @param int $curso_id
     * @param int $semestre_id (opcional)
     * @return array
     */
    public function obtener_por_curso($curso_id, $semestre_id = null) {
        $query = "SELECT a.id, a.docente_id, a.semestre_id, a.fecha_asignacion,
                         CONCAT(u.nombre, ' ', u.apellido) as docente_nombre,
                         u.email as docente_email,
                         s.nombre as semestre_nombre,
                         s.estado as semestre_estado
                  FROM " . $this->tabla . " a
                  INNER JOIN usuarios u ON a.docente_id = u.id
                  INNER JOIN semestres s ON a.semestre_id = s.id
                  WHERE a.curso_id = :curso_id";
        
        if ($semestre_id) {
            $query .= " AND a.semestre_id = :semestre_id";
        }
        
        $query .= " ORDER BY s.fecha_inicio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':curso_id', $curso_id);
        
        if ($semestre_id) {
            $stmt->bindParam(':semestre_id', $semestre_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener asignaciones por semestre
     * @param int $semestre_id
     * @return array
     */
    public function obtener_por_semestre($semestre_id) {
        $query = "SELECT a.id, a.docente_id, a.curso_id, a.fecha_asignacion,
                         CONCAT(u.nombre, ' ', u.apellido) as docente_nombre,
                         u.email as docente_email,
                         c.codigo as curso_codigo,
                         c.nombre as curso_nombre,
                         t.nombre as tecnica_nombre
                  FROM " . $this->tabla . " a
                  INNER JOIN usuarios u ON a.docente_id = u.id
                  INNER JOIN cursos c ON a.curso_id = c.id
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  WHERE a.semestre_id = :semestre_id
                  ORDER BY t.nombre, c.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':semestre_id', $semestre_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Crear nueva asignación
     * @param array $datos
     * @return bool
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->tabla . " 
                  (docente_id, curso_id, semestre_id) 
                  VALUES (:docente_id, :curso_id, :semestre_id)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $datos['docente_id'] = intval($datos['docente_id']);
        $datos['curso_id'] = intval($datos['curso_id']);
        $datos['semestre_id'] = intval($datos['semestre_id']);
        
        // Bind parameters
        $stmt->bindParam(':docente_id', $datos['docente_id']);
        $stmt->bindParam(':curso_id', $datos['curso_id']);
        $stmt->bindParam(':semestre_id', $datos['semestre_id']);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar asignación
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
     * Verificar si existe asignación duplicada
     * @param int $docente_id
     * @param int $curso_id
     * @param int $semestre_id
     * @return bool
     */
    public function existe_asignacion($docente_id, $curso_id, $semestre_id) {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " 
                  WHERE docente_id = :docente_id AND curso_id = :curso_id AND semestre_id = :semestre_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':docente_id', $docente_id);
        $stmt->bindParam(':curso_id', $curso_id);
        $stmt->bindParam(':semestre_id', $semestre_id);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Verificar si el curso ya tiene docente asignado en el semestre
     * @param int $curso_id
     * @param int $semestre_id
     * @param int $excluir_docente_id (opcional)
     * @return bool
     */
    public function curso_tiene_docente($curso_id, $semestre_id, $excluir_docente_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " 
                  WHERE curso_id = :curso_id AND semestre_id = :semestre_id";
        
        if ($excluir_docente_id) {
            $query .= " AND docente_id != :excluir_docente_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':curso_id', $curso_id);
        $stmt->bindParam(':semestre_id', $semestre_id);
        
        if ($excluir_docente_id) {
            $stmt->bindParam(':excluir_docente_id', $excluir_docente_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar asignaciones por docente
     * @param int $docente_id
     * @param int $semestre_id (opcional)
     * @return int
     */
    public function contar_por_docente($docente_id, $semestre_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " WHERE docente_id = :docente_id";
        
        if ($semestre_id) {
            $query .= " AND semestre_id = :semestre_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':docente_id', $docente_id);
        
        if ($semestre_id) {
            $stmt->bindParam(':semestre_id', $semestre_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Eliminar asignaciones por semestre
     * @param int $semestre_id
     * @return bool
     */
    public function eliminar_por_semestre($semestre_id) {
        $query = "DELETE FROM " . $this->tabla . " WHERE semestre_id = :semestre_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':semestre_id', $semestre_id);
        
        return $stmt->execute();
    }
    
    /**
     * Validar datos de asignación
     * @param array $datos
     * @return array Array de errores
     */
    public function validar($datos) {
        $errores = [];
        
        // Validar docente_id
        if (empty($datos['docente_id'])) {
            $errores[] = 'El docente es requerido';
        } elseif (!is_numeric($datos['docente_id']) || intval($datos['docente_id']) <= 0) {
            $errores[] = 'El docente seleccionado no es válido';
        }
        
        // Validar curso_id
        if (empty($datos['curso_id'])) {
            $errores[] = 'El curso es requerido';
        } elseif (!is_numeric($datos['curso_id']) || intval($datos['curso_id']) <= 0) {
            $errores[] = 'El curso seleccionado no es válido';
        }
        
        // Validar semestre_id
        if (empty($datos['semestre_id'])) {
            $errores[] = 'El semestre es requerido';
        } elseif (!is_numeric($datos['semestre_id']) || intval($datos['semestre_id']) <= 0) {
            $errores[] = 'El semestre seleccionado no es válido';
        }
        
        // Validar que no exista asignación duplicada
        if (!empty($datos['docente_id']) && !empty($datos['curso_id']) && !empty($datos['semestre_id'])) {
            if ($this->existe_asignacion($datos['docente_id'], $datos['curso_id'], $datos['semestre_id'])) {
                $errores[] = 'Esta asignación ya existe';
            }
        }
        
        // Validar que el curso no tenga ya un docente asignado en el semestre
        if (!empty($datos['curso_id']) && !empty($datos['semestre_id'])) {
            if ($this->curso_tiene_docente($datos['curso_id'], $datos['semestre_id'])) {
                $errores[] = 'Este curso ya tiene un docente asignado en el semestre seleccionado';
            }
        }
        
        return $errores;
    }
}
?>
