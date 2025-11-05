<?php
/**
 * Modelo de Curso
 */

class Curso {
    private $conn;
    private $tabla = 'cursos';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener curso por ID
     * @param int $id
     * @return array|false
     */
    public function obtener_por_id($id) {
        $query = "SELECT id, tecnica_id, codigo, nombre, descripcion, estado, fecha_creacion 
                  FROM " . $this->tabla . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener todos los cursos
     * @return array
     */
    public function obtener_todos() {
        $query = "SELECT id, tecnica_id, codigo, nombre, descripcion, estado, fecha_creacion 
                  FROM " . $this->tabla . " 
                  ORDER BY nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener todos los cursos con información de técnica
     * @return array
     */
    public function obtener_todos_con_tecnica() {
        $query = "SELECT c.id, c.tecnica_id, c.codigo, c.nombre, c.descripcion, 
                         c.estado, c.fecha_creacion, t.nombre as tecnica_nombre, 
                         t.codigo as tecnica_codigo
                  FROM " . $this->tabla . " c
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  ORDER BY t.nombre, c.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener cursos activos
     * @return array
     */
    public function obtener_activos() {
        $query = "SELECT c.id, c.tecnica_id, c.codigo, c.nombre, c.descripcion, 
                         c.fecha_creacion, t.nombre as tecnica_nombre
                  FROM " . $this->tabla . " c
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  WHERE c.estado = 'activo' AND t.estado = 'activo'
                  ORDER BY t.nombre, c.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener cursos por técnica
     * @param int $tecnica_id
     * @return array
     */
    public function obtener_por_tecnica($tecnica_id) {
        $query = "SELECT id, codigo, nombre, descripcion, estado, fecha_creacion 
                  FROM " . $this->tabla . " 
                  WHERE tecnica_id = :tecnica_id AND estado = 'activo'
                  ORDER BY nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tecnica_id', $tecnica_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener cursos recientes
     * @param int $limite
     * @return array
     */
    public function obtener_recientes($limite = 5) {
        $query = "SELECT c.id, c.codigo, c.nombre, c.fecha_creacion, t.nombre as tecnica_nombre
                  FROM " . $this->tabla . " c
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  WHERE c.estado = 'activo'
                  ORDER BY c.fecha_creacion DESC
                  LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Crear nuevo curso
     * @param array $datos
     * @return bool
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->tabla . " 
                  (tecnica_id, codigo, nombre, descripcion, estado) 
                  VALUES (:tecnica_id, :codigo, :nombre, :descripcion, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $datos['tecnica_id'] = intval($datos['tecnica_id']);
        $datos['codigo'] = limpiar_entrada($datos['codigo']);
        $datos['nombre'] = limpiar_entrada($datos['nombre']);
        $datos['descripcion'] = limpiar_entrada($datos['descripcion'] ?? '');
        $datos['estado'] = $datos['estado'] ?? 'activo';
        
        // Bind parameters
        $stmt->bindParam(':tecnica_id', $datos['tecnica_id']);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':estado', $datos['estado']);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar curso
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar($id, $datos) {
        // Construir query dinámicamente
        $campos = [];
        $parametros = [':id' => $id];
        
        if (isset($datos['tecnica_id'])) {
            $campos[] = "tecnica_id = :tecnica_id";
            $parametros[':tecnica_id'] = intval($datos['tecnica_id']);
        }
        
        if (isset($datos['codigo'])) {
            $campos[] = "codigo = :codigo";
            $parametros[':codigo'] = limpiar_entrada($datos['codigo']);
        }
        
        if (isset($datos['nombre'])) {
            $campos[] = "nombre = :nombre";
            $parametros[':nombre'] = limpiar_entrada($datos['nombre']);
        }
        
        if (isset($datos['descripcion'])) {
            $campos[] = "descripcion = :descripcion";
            $parametros[':descripcion'] = limpiar_entrada($datos['descripcion']);
        }
        
        if (isset($datos['estado'])) {
            $campos[] = "estado = :estado";
            $parametros[':estado'] = limpiar_entrada($datos['estado']);
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
     * Eliminar curso
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
     * Verificar si existe código
     * @param string $codigo
     * @param int $excluir_id ID a excluir de la búsqueda (para actualizaciones)
     * @return bool
     */
    public function existe_codigo($codigo, $excluir_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " WHERE codigo = :codigo";
        
        if ($excluir_id) {
            $query .= " AND id != :excluir_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':codigo', $codigo);
        
        if ($excluir_id) {
            $stmt->bindParam(':excluir_id', $excluir_id);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar cursos activos
     * @return int
     */
    public function contar_activos() {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " WHERE estado = 'activo'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Cambiar estado de curso
     * @param int $id
     * @param string $estado
     * @return bool
     */
    public function cambiar_estado($id, $estado) {
        $query = "UPDATE " . $this->tabla . " SET estado = :estado WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener cursos con estadísticas
     * @return array
     */
    public function obtener_con_estadisticas() {
        $query = "SELECT c.id, c.codigo, c.nombre, c.descripcion, c.estado, c.fecha_creacion,
                         t.nombre as tecnica_nombre,
                         COUNT(DISTINCT a.id) as total_asignaciones,
                         COUNT(DISTINCT i.id) as total_inscripciones
                  FROM " . $this->tabla . " c
                  INNER JOIN tecnicas t ON c.tecnica_id = t.id
                  LEFT JOIN asignaciones a ON c.id = a.curso_id
                  LEFT JOIN inscripciones i ON c.id = i.curso_id
                  GROUP BY c.id, c.codigo, c.nombre, c.descripcion, c.estado, c.fecha_creacion, t.nombre
                  ORDER BY t.nombre, c.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Validar datos de curso
     * @param array $datos
     * @param bool $es_actualizacion
     * @return array Array de errores
     */
    public function validar($datos, $es_actualizacion = false) {
        $errores = [];
        
        // Validar técnica_id
        if (!$es_actualizacion || isset($datos['tecnica_id'])) {
            if (empty($datos['tecnica_id'])) {
                $errores[] = 'La técnica es requerida';
            } elseif (!is_numeric($datos['tecnica_id']) || intval($datos['tecnica_id']) <= 0) {
                $errores[] = 'La técnica seleccionada no es válida';
            }
        }
        
        // Validar código
        if (!$es_actualizacion || isset($datos['codigo'])) {
            if (empty($datos['codigo'])) {
                $errores[] = 'El código es requerido';
            } elseif (!codigo_valido($datos['codigo'])) {
                $errores[] = 'El código solo puede contener letras, números y guiones';
            } elseif (strlen($datos['codigo']) > 20) {
                $errores[] = 'El código no puede tener más de 20 caracteres';
            } else {
                // Verificar si el código ya existe
                $excluir_id = $es_actualizacion && isset($datos['id']) ? $datos['id'] : null;
                if ($this->existe_codigo($datos['codigo'], $excluir_id)) {
                    $errores[] = 'El código ya está registrado';
                }
            }
        }
        
        // Validar nombre
        if (!$es_actualizacion || isset($datos['nombre'])) {
            if (empty($datos['nombre'])) {
                $errores[] = 'El nombre es requerido';
            } elseif (strlen($datos['nombre']) > 200) {
                $errores[] = 'El nombre no puede tener más de 200 caracteres';
            }
        }
        
        // Validar descripción (opcional)
        if (isset($datos['descripcion']) && strlen($datos['descripcion']) > 1000) {
            $errores[] = 'La descripción no puede tener más de 1000 caracteres';
        }
        
        // Validar estado
        if (isset($datos['estado'])) {
            $estados_validos = ['activo', 'inactivo'];
            if (!in_array($datos['estado'], $estados_validos)) {
                $errores[] = 'El estado seleccionado no es válido';
            }
        }
        
        return $errores;
    }
}
?>
