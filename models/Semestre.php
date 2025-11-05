<?php
/**
 * Modelo de Semestre
 */

class Semestre {
    private $conn;
    private $tabla = 'semestres';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener semestre por ID
     * @param int $id
     * @return array|false
     */
    public function obtener_por_id($id) {
        $query = "SELECT id, nombre, fecha_inicio, fecha_fin, estado, fecha_creacion 
                  FROM " . $this->tabla . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener todos los semestres
     * @return array
     */
    public function obtener_todos() {
        $query = "SELECT id, nombre, fecha_inicio, fecha_fin, estado, fecha_creacion 
                  FROM " . $this->tabla . " 
                  ORDER BY fecha_inicio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener semestre activo
     * @return array|false
     */
    public function obtener_activo() {
        $query = "SELECT id, nombre, fecha_inicio, fecha_fin, estado, fecha_creacion 
                  FROM " . $this->tabla . " 
                  WHERE estado = 'activo' 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Crear nuevo semestre
     * @param array $datos
     * @return bool
     */
    public function crear($datos) {
        try {
            // Si se está creando un semestre activo, desactivar los demás
            if (isset($datos['estado']) && $datos['estado'] === 'activo') {
                $this->desactivar_todos();
            }
            
            $query = "INSERT INTO " . $this->tabla . " 
                      (nombre, fecha_inicio, fecha_fin, estado) 
                      VALUES (:nombre, :fecha_inicio, :fecha_fin, :estado)";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $datos['nombre'] = limpiar_entrada($datos['nombre']);
            $datos['fecha_inicio'] = $datos['fecha_inicio'];
            $datos['fecha_fin'] = $datos['fecha_fin'];
            $datos['estado'] = $datos['estado'] ?? 'cerrado';
            
            // Bind parameters
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $datos['fecha_fin']);
            $stmt->bindParam(':estado', $datos['estado']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al crear semestre en BD: " . $e->getMessage());
            throw new Exception("Error de base de datos: " . $e->getMessage());
        }
    }
    
    /**
     * Actualizar semestre
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar($id, $datos) {
        // Si se está activando este semestre, desactivar los demás
        if (isset($datos['estado']) && $datos['estado'] === 'activo') {
            $this->desactivar_todos();
        }
        
        // Construir query dinámicamente
        $campos = [];
        $parametros = [':id' => $id];
        
        if (isset($datos['nombre'])) {
            $campos[] = "nombre = :nombre";
            $parametros[':nombre'] = limpiar_entrada($datos['nombre']);
        }
        
        if (isset($datos['fecha_inicio'])) {
            $campos[] = "fecha_inicio = :fecha_inicio";
            $parametros[':fecha_inicio'] = $datos['fecha_inicio'];
        }
        
        if (isset($datos['fecha_fin'])) {
            $campos[] = "fecha_fin = :fecha_fin";
            $parametros[':fecha_fin'] = $datos['fecha_fin'];
        }
        
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
     * Eliminar semestre
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
     * Desactivar todos los semestres
     * @return bool
     */
    private function desactivar_todos() {
        $query = "UPDATE " . $this->tabla . " SET estado = 'cerrado'";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
    
    /**
     * Activar semestre
     * @param int $id
     * @return bool
     */
    public function activar($id) {
        // Primero desactivar todos
        $this->desactivar_todos();
        
        // Luego activar el seleccionado
        $query = "UPDATE " . $this->tabla . " SET estado = 'activo' WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Cerrar semestre
     * @param int $id
     * @return bool
     */
    public function cerrar($id) {
        $query = "UPDATE " . $this->tabla . " SET estado = 'cerrado' WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Verificar si hay fechas solapadas
     * @param string $fecha_inicio
     * @param string $fecha_fin
     * @param int $excluir_id ID a excluir de la búsqueda (para actualizaciones)
     * @return bool
     */
    public function fechas_solapadas($fecha_inicio, $fecha_fin, $excluir_id = null) {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->tabla . " 
                      WHERE (
                          (fecha_inicio <= :fecha_inicio AND fecha_fin >= :fecha_inicio) OR
                          (fecha_inicio <= :fecha_fin AND fecha_fin >= :fecha_fin) OR
                          (fecha_inicio >= :fecha_inicio AND fecha_fin <= :fecha_fin)
                      )";
            
            if ($excluir_id) {
                $query .= " AND id != :excluir_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            
            if ($excluir_id) {
                $stmt->bindParam(':excluir_id', $excluir_id);
            }
            
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar fechas solapadas: " . $e->getMessage());
            // En caso de error, no bloquear la creación
            return false;
        }
    }
    
    /**
     * Obtener semestres con estadísticas
     * @return array
     */
    public function obtener_con_estadisticas() {
        $query = "SELECT s.id, s.nombre, s.fecha_inicio, s.fecha_fin, s.estado, s.fecha_creacion,
                         COUNT(DISTINCT a.id) as total_asignaciones,
                         COUNT(DISTINCT i.id) as total_inscripciones
                  FROM " . $this->tabla . " s
                  LEFT JOIN asignaciones a ON s.id = a.semestre_id
                  LEFT JOIN inscripciones i ON s.id = i.semestre_id
                  GROUP BY s.id, s.nombre, s.fecha_inicio, s.fecha_fin, s.estado, s.fecha_creacion
                  ORDER BY s.fecha_inicio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Verificar si el semestre está activo
     * @param int $id
     * @return bool
     */
    public function esta_activo($id) {
        $query = "SELECT estado FROM " . $this->tabla . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $resultado = $stmt->fetch();
        return $resultado && $resultado['estado'] === 'activo';
    }
    
    /**
     * Validar datos de semestre
     * @param array $datos
     * @param bool $es_actualizacion
     * @return array Array de errores
     */
    public function validar($datos, $es_actualizacion = false) {
        $errores = [];
        
        // Validar nombre
        if (!$es_actualizacion || isset($datos['nombre'])) {
            if (empty($datos['nombre'])) {
                $errores[] = 'El nombre es requerido';
            } elseif (strlen($datos['nombre']) > 50) {
                $errores[] = 'El nombre no puede tener más de 50 caracteres';
            }
        }
        
        // Validar fecha_inicio
        if (!$es_actualizacion || isset($datos['fecha_inicio'])) {
            if (empty($datos['fecha_inicio'])) {
                $errores[] = 'La fecha de inicio es requerida';
            } elseif (!$this->validar_fecha($datos['fecha_inicio'])) {
                $errores[] = 'La fecha de inicio no es válida';
            }
        }
        
        // Validar fecha_fin
        if (!$es_actualizacion || isset($datos['fecha_fin'])) {
            if (empty($datos['fecha_fin'])) {
                $errores[] = 'La fecha de fin es requerida';
            } elseif (!$this->validar_fecha($datos['fecha_fin'])) {
                $errores[] = 'La fecha de fin no es válida';
            }
        }
        
        // Validar que fecha_fin sea posterior a fecha_inicio
        if (isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
            if (strtotime($datos['fecha_fin']) <= strtotime($datos['fecha_inicio'])) {
                $errores[] = 'La fecha de fin debe ser posterior a la fecha de inicio';
            }
        }
        
        // Validar solapamiento de fechas
        if (isset($datos['fecha_inicio']) && isset($datos['fecha_fin'])) {
            $excluir_id = $es_actualizacion && isset($datos['id']) ? $datos['id'] : null;
            if ($this->fechas_solapadas($datos['fecha_inicio'], $datos['fecha_fin'], $excluir_id)) {
                $errores[] = 'Las fechas se solapan con otro semestre existente';
            }
        }
        
        // Validar estado
        if (isset($datos['estado'])) {
            $estados_validos = ['activo', 'cerrado'];
            if (!in_array($datos['estado'], $estados_validos)) {
                $errores[] = 'El estado seleccionado no es válido';
            }
        }
        
        return $errores;
    }
    
    /**
     * Validar formato de fecha
     * @param string $fecha
     * @return bool
     */
    private function validar_fecha($fecha) {
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }
}
?>
