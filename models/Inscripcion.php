<?php
/**
 * Modelo de Inscripción
 */

class Inscripcion {
    private $conn;
    private $tabla = 'inscripciones';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener inscripción por ID
     * @param int $id
     * @return array|false
     */
    public function obtener_por_id($id) {
        $query = "SELECT id, estudiante_id, curso_id, semestre_id, estado, fecha_inscripcion 
                  FROM " . $this->tabla . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener todas las inscripciones
     * @return array
     */
    public function obtener_todas() {
        $query = "SELECT id, estudiante_id, curso_id, semestre_id, estado, fecha_inscripcion 
                  FROM " . $this->tabla . " 
                  ORDER BY fecha_inscripcion DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener todas las inscripciones con detalles
     * @return array
     */
    public function obtener_todas_con_detalles() {
        $query = "SELECT i.id, i.estudiante_id, i.curso_id, i.semestre_id, i.estado, i.fecha_inscripcion,
                         u.nombre as estudiante_nombre,
                         u.apellido as estudiante_apellido,
                         u.email as estudiante_email,
                         c.codigo as curso_codigo,
                         c.nombre as curso_nombre,
                         t.nombre as tecnica_nombre,
                         s.nombre as semestre_nombre,
                         s.estado as semestre_estado
                  FROM " . $this->tabla . " i
                  INNER JOIN usuarios u ON i.estudiante_id = u.id
                  INNER JOIN cursos c ON i.curso_id = c.id
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  INNER JOIN semestres s ON i.semestre_id = s.id
                  ORDER BY s.fecha_inicio DESC, t.nombre, c.nombre, u.apellido, u.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener inscripciones por estudiante
     * @param int $estudiante_id
     * @param int $semestre_id (opcional)
     * @return array
     */
    public function obtener_por_estudiante($estudiante_id, $semestre_id = null) {
        $query = "SELECT i.id, i.curso_id, i.semestre_id, i.estado, i.fecha_inscripcion,
                         c.codigo as curso_codigo,
                         c.nombre as curso_nombre,
                         t.nombre as tecnica_nombre,
                         s.nombre as semestre_nombre,
                         s.estado as semestre_estado
                  FROM " . $this->tabla . " i
                  INNER JOIN cursos c ON i.curso_id = c.id
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  INNER JOIN semestres s ON i.semestre_id = s.id
                  WHERE i.estudiante_id = :estudiante_id AND i.estado = 'inscrito'";
        
        if ($semestre_id) {
            $query .= " AND i.semestre_id = :semestre_id";
        }
        
        $query .= " ORDER BY s.fecha_inicio DESC, t.nombre, c.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudiante_id', $estudiante_id);
        
        if ($semestre_id) {
            $stmt->bindParam(':semestre_id', $semestre_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener inscripciones por curso
     * @param int $curso_id
     * @param int $semestre_id (opcional)
     * @return array
     */
    public function obtener_por_curso($curso_id, $semestre_id = null) {
        $query = "SELECT i.id, i.estudiante_id, i.semestre_id, i.estado, i.fecha_inscripcion,
                         CONCAT(u.nombre, ' ', u.apellido) as estudiante_nombre,
                         u.email as estudiante_email,
                         s.nombre as semestre_nombre,
                         s.estado as semestre_estado
                  FROM " . $this->tabla . " i
                  INNER JOIN usuarios u ON i.estudiante_id = u.id
                  INNER JOIN semestres s ON i.semestre_id = s.id
                  WHERE i.curso_id = :curso_id AND i.estado = 'inscrito'";
        
        if ($semestre_id) {
            $query .= " AND i.semestre_id = :semestre_id";
        }
        
        $query .= " ORDER BY s.fecha_inicio DESC, u.apellido, u.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':curso_id', $curso_id);
        
        if ($semestre_id) {
            $stmt->bindParam(':semestre_id', $semestre_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener inscripciones por semestre
     * @param int $semestre_id
     * @return array
     */
    public function obtener_por_semestre($semestre_id) {
        $query = "SELECT i.id, i.estudiante_id, i.curso_id, i.estado, i.fecha_inscripcion,
                         CONCAT(u.nombre, ' ', u.apellido) as estudiante_nombre,
                         u.email as estudiante_email,
                         c.codigo as curso_codigo,
                         c.nombre as curso_nombre,
                         t.nombre as tecnica_nombre
                  FROM " . $this->tabla . " i
                  INNER JOIN usuarios u ON i.estudiante_id = u.id
                  INNER JOIN cursos c ON i.curso_id = c.id
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  WHERE i.semestre_id = :semestre_id AND i.estado = 'inscrito'
                  ORDER BY t.nombre, c.nombre, u.apellido, u.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':semestre_id', $semestre_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Crear nueva inscripción
     * @param array $datos
     * @return bool
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->tabla . " 
                  (estudiante_id, curso_id, semestre_id, estado) 
                  VALUES (:estudiante_id, :curso_id, :semestre_id, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $datos['estudiante_id'] = intval($datos['estudiante_id']);
        $datos['curso_id'] = intval($datos['curso_id']);
        $datos['semestre_id'] = intval($datos['semestre_id']);
        $datos['estado'] = $datos['estado'] ?? 'inscrito';
        
        // Bind parameters
        $stmt->bindParam(':estudiante_id', $datos['estudiante_id']);
        $stmt->bindParam(':curso_id', $datos['curso_id']);
        $stmt->bindParam(':semestre_id', $datos['semestre_id']);
        $stmt->bindParam(':estado', $datos['estado']);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar inscripción
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar($id, $datos) {
        // Construir query dinámicamente
        $campos = [];
        $parametros = [':id' => $id];
        
        if (isset($datos['estado'])) {
            $campos[] = "estado = :estado";
            $parametros[':estado'] = $datos['estado'];
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
     * Eliminar inscripción
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
     * Retirar estudiante (cambiar estado a retirado)
     * @param int $id
     * @return bool
     */
    public function retirar($id) {
        return $this->actualizar($id, ['estado' => 'retirado']);
    }
    
    /**
     * Verificar si existe inscripción duplicada
     * @param int $estudiante_id
     * @param int $curso_id
     * @param int $semestre_id
     * @return bool
     */
    public function existe_inscripcion($estudiante_id, $curso_id, $semestre_id) {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " 
                  WHERE estudiante_id = :estudiante_id AND curso_id = :curso_id 
                  AND semestre_id = :semestre_id AND estado = 'inscrito'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudiante_id', $estudiante_id);
        $stmt->bindParam(':curso_id', $curso_id);
        $stmt->bindParam(':semestre_id', $semestre_id);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar inscripciones por estudiante
     * @param int $estudiante_id
     * @param int $semestre_id (opcional)
     * @return int
     */
    public function contar_por_estudiante($estudiante_id, $semestre_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " 
                  WHERE estudiante_id = :estudiante_id AND estado = 'inscrito'";
        
        if ($semestre_id) {
            $query .= " AND semestre_id = :semestre_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudiante_id', $estudiante_id);
        
        if ($semestre_id) {
            $stmt->bindParam(':semestre_id', $semestre_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Contar inscripciones por curso
     * @param int $curso_id
     * @param int $semestre_id (opcional)
     * @return int
     */
    public function contar_por_curso($curso_id, $semestre_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " 
                  WHERE curso_id = :curso_id AND estado = 'inscrito'";
        
        if ($semestre_id) {
            $query .= " AND semestre_id = :semestre_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':curso_id', $curso_id);
        
        if ($semestre_id) {
            $stmt->bindParam(':semestre_id', $semestre_id);
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Eliminar inscripciones por semestre
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
     * Obtener estudiantes inscritos en un curso específico
     * @param int $curso_id
     * @param int $semestre_id
     * @return array
     */
    public function obtener_estudiantes_curso($curso_id, $semestre_id) {
        $query = "SELECT i.id as inscripcion_id, i.fecha_inscripcion,
                         u.id as estudiante_id, u.nombre, u.apellido, u.email
                  FROM " . $this->tabla . " i
                  INNER JOIN usuarios u ON i.estudiante_id = u.id
                  WHERE i.curso_id = :curso_id AND i.semestre_id = :semestre_id 
                  AND i.estado = 'inscrito'
                  ORDER BY u.apellido, u.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':curso_id', $curso_id);
        $stmt->bindParam(':semestre_id', $semestre_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Validar datos de inscripción
     * @param array $datos
     * @return array Array de errores
     */
    public function validar($datos) {
        $errores = [];
        
        // Validar estudiante_id
        if (empty($datos['estudiante_id'])) {
            $errores[] = 'El estudiante es requerido';
        } elseif (!is_numeric($datos['estudiante_id']) || intval($datos['estudiante_id']) <= 0) {
            $errores[] = 'El estudiante seleccionado no es válido';
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
        
        // Validar estado
        if (isset($datos['estado'])) {
            $estados_validos = ['inscrito', 'retirado'];
            if (!in_array($datos['estado'], $estados_validos)) {
                $errores[] = 'El estado seleccionado no es válido';
            }
        }
        
        // Validar que no exista inscripción duplicada
        if (!empty($datos['estudiante_id']) && !empty($datos['curso_id']) && !empty($datos['semestre_id'])) {
            if ($this->existe_inscripcion($datos['estudiante_id'], $datos['curso_id'], $datos['semestre_id'])) {
                $errores[] = 'Esta inscripción ya existe';
            }
        }
        
        return $errores;
    }
}
?>
