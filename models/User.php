<?php
/**
 * Modelo de Usuario
 */

class User {
    private $conn;
    private $tabla = 'usuarios';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Obtener usuario por email
     * @param string $email
     * @return array|false
     */
    public function obtener_por_email($email) {
        $query = "SELECT id, nombre, apellido, email, password, rol, estado, 
                         fecha_creacion, fecha_actualizacion 
                  FROM " . $this->tabla . " 
                  WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener usuario por ID
     * @param int $id
     * @return array|false
     */
    public function obtener_por_id($id) {
        $query = "SELECT id, nombre, apellido, email, rol, estado, 
                         fecha_creacion, fecha_actualizacion 
                  FROM " . $this->tabla . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener todos los usuarios
     * @param string $rol Filtrar por rol (opcional)
     * @return array
     */
    public function obtener_todos($rol = null) {
        $query = "SELECT id, nombre, apellido, email, rol, estado, 
                         fecha_creacion, fecha_actualizacion 
                  FROM " . $this->tabla;
        
        if ($rol) {
            $query .= " WHERE rol = :rol";
        }
        
        $query .= " ORDER BY nombre, apellido";
        
        $stmt = $this->conn->prepare($query);
        
        if ($rol) {
            $stmt->bindParam(':rol', $rol);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Crear nuevo usuario
     * @param array $datos
     * @return bool
     */
    public function crear($datos) {
        $query = "INSERT INTO " . $this->tabla . " 
                  (nombre, apellido, email, password, rol, estado) 
                  VALUES (:nombre, :apellido, :email, :password, :rol, :estado)";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $datos['nombre'] = limpiar_entrada($datos['nombre']);
        $datos['apellido'] = limpiar_entrada($datos['apellido']);
        $datos['email'] = limpiar_entrada($datos['email']);
        $datos['password'] = hash_password($datos['password']);
        $datos['rol'] = limpiar_entrada($datos['rol']);
        $datos['estado'] = $datos['estado'] ?? 'activo';
        
        // Bind parameters
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':apellido', $datos['apellido']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':password', $datos['password']);
        $stmt->bindParam(':rol', $datos['rol']);
        $stmt->bindParam(':estado', $datos['estado']);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar usuario
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar($id, $datos) {
        // Construir query dinámicamente
        $campos = [];
        $parametros = [':id' => $id];
        
        if (isset($datos['nombre'])) {
            $campos[] = "nombre = :nombre";
            $parametros[':nombre'] = limpiar_entrada($datos['nombre']);
        }
        
        if (isset($datos['apellido'])) {
            $campos[] = "apellido = :apellido";
            $parametros[':apellido'] = limpiar_entrada($datos['apellido']);
        }
        
        if (isset($datos['email'])) {
            $campos[] = "email = :email";
            $parametros[':email'] = limpiar_entrada($datos['email']);
        }
        
        if (isset($datos['password']) && !empty($datos['password'])) {
            $campos[] = "password = :password";
            $parametros[':password'] = hash_password($datos['password']);
        }
        
        if (isset($datos['rol'])) {
            $campos[] = "rol = :rol";
            $parametros[':rol'] = limpiar_entrada($datos['rol']);
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
     * Eliminar usuario
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
     * Verificar si existe email
     * @param string $email
     * @param int $excluir_id ID a excluir de la búsqueda (para actualizaciones)
     * @return bool
     */
    public function existe_email($email, $excluir_id = null) {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " WHERE email = :email";
        
        if ($excluir_id) {
            $query .= " AND id != :excluir_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if ($excluir_id) {
            $stmt->bindParam(':excluir_id', $excluir_id);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar usuarios por rol
     * @param string $rol
     * @return int
     */
    public function contar_por_rol($rol) {
        $query = "SELECT COUNT(*) FROM " . $this->tabla . " WHERE rol = :rol AND estado = 'activo'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':rol', $rol);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Obtener docentes activos
     * @return array
     */
    public function obtener_docentes() {
        return $this->obtener_todos('docente');
    }
    
    /**
     * Obtener estudiantes activos
     * @return array
     */
    public function obtener_estudiantes() {
        return $this->obtener_todos('estudiante');
    }
    
    /**
     * Cambiar estado de usuario
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
     * Obtener usuarios recientes
     * @param int $limite
     * @return array
     */
    public function obtener_recientes($limite = 5) {
        $query = "SELECT id, nombre, apellido, email, rol, fecha_creacion
                  FROM " . $this->tabla . " 
                  WHERE estado = 'activo'
                  ORDER BY fecha_creacion DESC
                  LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Validar datos de usuario
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
            } elseif (!solo_letras_espacios($datos['nombre'])) {
                $errores[] = 'El nombre solo puede contener letras y espacios';
            } elseif (strlen($datos['nombre']) > 100) {
                $errores[] = 'El nombre no puede tener más de 100 caracteres';
            }
        }
        
        // Validar apellido
        if (!$es_actualizacion || isset($datos['apellido'])) {
            if (empty($datos['apellido'])) {
                $errores[] = 'El apellido es requerido';
            } elseif (!solo_letras_espacios($datos['apellido'])) {
                $errores[] = 'El apellido solo puede contener letras y espacios';
            } elseif (strlen($datos['apellido']) > 100) {
                $errores[] = 'El apellido no puede tener más de 100 caracteres';
            }
        }
        
        // Validar email
        if (!$es_actualizacion || isset($datos['email'])) {
            if (empty($datos['email'])) {
                $errores[] = 'El email es requerido';
            } elseif (!es_email_valido($datos['email'])) {
                $errores[] = 'El formato del email es inválido';
            } elseif (strlen($datos['email']) > 150) {
                $errores[] = 'El email no puede tener más de 150 caracteres';
            } else {
                // Verificar si el email ya existe
                $excluir_id = $es_actualizacion && isset($datos['id']) ? $datos['id'] : null;
                if ($this->existe_email($datos['email'], $excluir_id)) {
                    $errores[] = 'El email ya está registrado';
                }
            }
        }
        
        // Validar contraseña (solo para creación o si se proporciona)
        if (!$es_actualizacion || (isset($datos['password']) && !empty($datos['password']))) {
            if (empty($datos['password'])) {
                $errores[] = 'La contraseña es requerida';
            } elseif (strlen($datos['password']) < 6) {
                $errores[] = 'La contraseña debe tener al menos 6 caracteres';
            }
        }
        
        // Validar rol
        if (!$es_actualizacion || isset($datos['rol'])) {
            $roles_validos = ['admin', 'docente', 'estudiante'];
            if (empty($datos['rol'])) {
                $errores[] = 'El rol es requerido';
            } elseif (!in_array($datos['rol'], $roles_validos)) {
                $errores[] = 'El rol seleccionado no es válido';
            }
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
