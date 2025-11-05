<?php
/**
 * Funciones auxiliares del sistema
 */

/**
 * Generar respuesta JSON para AJAX
 * @param bool $exito
 * @param string $mensaje
 * @param array $datos
 */
function respuesta_json($exito, $mensaje, $datos = []) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'exito' => $exito,
        'mensaje' => $mensaje,
        'datos' => $datos
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Validar datos requeridos en un array
 * @param array $datos
 * @param array $campos_requeridos
 * @return array Array con errores encontrados
 */
function validar_campos_requeridos($datos, $campos_requeridos) {
    $errores = [];
    
    foreach ($campos_requeridos as $campo) {
        if (!isset($datos[$campo]) || empty(trim($datos[$campo]))) {
            $errores[] = "El campo {$campo} es requerido";
        }
    }
    
    return $errores;
}

/**
 * Validar formato de email
 * @param string $email
 * @return bool
 */
function es_email_valido($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar rango de nota
 * @param float $nota
 * @return bool
 */
function es_nota_valida($nota) {
    $nota = floatval($nota);
    return $nota >= NOTA_MINIMA && $nota <= NOTA_MAXIMA;
}

/**
 * Formatear fecha para mostrar
 * @param string $fecha
 * @param string $formato
 * @return string
 */
function formatear_fecha($fecha, $formato = 'd/m/Y') {
    if (empty($fecha)) return '';
    
    try {
        $date = new DateTime($fecha);
        return $date->format($formato);
    } catch (Exception $e) {
        return $fecha;
    }
}

/**
 * Formatear fecha y hora para mostrar
 * @param string $fecha_hora
 * @return string
 */
function formatear_fecha_hora($fecha_hora) {
    return formatear_fecha($fecha_hora, 'd/m/Y H:i');
}

/**
 * Generar hash de contraseña
 * @param string $password
 * @return string
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verificar contraseña
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verificar_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Calcular promedio de notas
 * @param array $notas
 * @return float
 */
function calcular_promedio($notas) {
    if (empty($notas)) return 0.0;
    
    $suma = array_sum($notas);
    $cantidad = count($notas);
    
    return round($suma / $cantidad, 1);
}

/**
 * Obtener color CSS según la nota
 * @param float $nota
 * @return string
 */
function obtener_color_nota($nota) {
    if ($nota >= 4.5) return 'text-green-600';
    if ($nota >= 4.0) return 'text-blue-600';
    if ($nota >= 3.0) return 'text-yellow-600';
    return 'text-red-600';
}

/**
 * Obtener color de fondo para barra de progreso según la nota
 * @param float $nota
 * @return string
 */
function obtener_color_barra_progreso($nota) {
    if ($nota >= 4.5) return 'bg-green-500';
    if ($nota >= 4.0) return 'bg-blue-500';
    if ($nota >= 3.0) return 'bg-yellow-500';
    return 'bg-red-500';
}

/**
 * Generar código único
 * @param string $prefijo
 * @return string
 */
function generar_codigo($prefijo = '') {
    return $prefijo . strtoupper(uniqid());
}

/**
 * Limpiar string para usar en URLs
 * @param string $string
 * @return string
 */
function limpiar_url($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Truncar texto
 * @param string $texto
 * @param int $longitud
 * @param string $sufijo
 * @return string
 */
function truncar_texto($texto, $longitud = 100, $sufijo = '...') {
    if (strlen($texto) <= $longitud) {
        return $texto;
    }
    
    return substr($texto, 0, $longitud) . $sufijo;
}

/**
 * Escapar HTML para prevenir XSS
 * @param string|null $string
 * @return string
 */
function escapar_html($string) {
    if ($string === null || $string === '') {
        return '';
    }
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Validar que solo contenga letras y espacios
 * @param string $string
 * @return bool
 */
function solo_letras_espacios($string) {
    return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $string);
}

/**
 * Validar que solo contenga letras, números y guiones
 * @param string $string
 * @return bool
 */
function codigo_valido($string) {
    return preg_match('/^[a-zA-Z0-9\-]+$/', $string);
}

/**
 * Obtener nombre del mes en español
 * @param int $mes
 * @return string
 */
function nombre_mes($mes) {
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    return $meses[$mes] ?? '';
}

/**
 * Registrar log de actividad
 * @param string $mensaje
 * @param string $nivel
 */
function log_actividad($mensaje, $nivel = 'INFO') {
    $fecha = date('Y-m-d H:i:s');
    $usuario = SessionManager::obtener_usuario();
    $usuario_info = $usuario ? "{$usuario['nombre']} {$usuario['apellido']} ({$usuario['email']})" : 'Sistema';
    
    $log_mensaje = "[{$fecha}] [{$nivel}] Usuario: {$usuario_info} - {$mensaje}" . PHP_EOL;
    
    // En un entorno de producción, esto se escribiría a un archivo de log
    error_log($log_mensaje);
}

/**
 * Validar token CSRF (implementación básica)
 * @param string $token
 * @return bool
 */
function validar_csrf($token) {
    SessionManager::iniciar_sesion();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generar token CSRF
 * @return string
 */
function generar_csrf() {
    SessionManager::iniciar_sesion();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
?>
