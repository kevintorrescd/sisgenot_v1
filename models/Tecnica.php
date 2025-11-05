<?php
/**
 * Modelo de Técnica
 */

class Tecnica {
    private $conn;
    private $tabla = 'tecnicas';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener técnica por ID
     * @param int $id
     * @return array|false
     */
    public function obtener_por_id($id) {
        $query = "SELECT id, codigo, nombre, descripcion, estado, fecha_creacion 
                  FROM " . $this->tabla . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener todas las técnicas
     * @return array
     */
    public function obtener_todas() {
        $query = "SELECT id, codigo, nombre, descripcion, estado, fecha_creacion 
                  FROM " . $this->tabla . " 
                  ORDER BY nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener técnicas activas
     * @return array
     */
    public function obtener_activas() {
        $query = "SELECT id, codigo, nombre, descripcion, fecha_creacion 
                  FROM " . $this->tabla . " 
                  WHERE estado = 'activo' 
                  ORDER BY nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Crear nueva técnica
     * @param array $datos
     * @return bool
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->tabla . " 
                  (codigo, nombre, descripcion, estado) 
                  VALUES (:codigo, :nombre, :descripcion, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $datos['codigo'] = limpiar_entrada($datos['codigo']);
        $datos['nombre'] = limpiar_entrada($datos['nombre']);
        $datos['descripcion'] = limpiar_entrada($datos['descripcion'] ?? '');
        $datos['estado'] = $datos['estado'] ?? 'activo';
        
        // Bind parameters
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':estado', $datos['estado']);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar técnica
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar($id, $datos) {
        // Construir query dinámicamente
        $campos = [];
        $parametros = [':id' => $id];
        
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
     * Eliminar técnica
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
     * Contar técnicas activas
     * @return int
     */
    public function contar_activas() {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " WHERE estado = 'activo'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Cambiar estado de técnica
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
     * Obtener técnicas con conteo de cursos
     * @return array
     */
    public function obtener_con_conteo_cursos() {
        $query = "SELECT t.id, t.codigo, t.nombre, t.descripcion, t.estado, t.fecha_creacion,
                         COUNT(c.id) as total_cursos
                  FROM " . $this->tabla . " t
                  LEFT JOIN cursos c ON t.id = c.tecnica_id AND c.estado = 'activo'
                  GROUP BY t.id, t.codigo, t.nombre, t.descripcion, t.estado, t.fecha_creacion
                  ORDER BY t.nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Validar datos de técnica
     * @param array $datos
     * @param bool $es_actualizacion
     * @return array Array de errores
     */
    public function validar($datos, $es_actualizacion = false) {
        $errores = [];
        
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
