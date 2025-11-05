<?php
// Configuración de la aplicación
define('APP_NAME', 'SISGENOT - Sistema de Gestión de Notas');
define('APP_VERSION', '1.0.0');

// Detectar automáticamente la URL base de la aplicación
function detectar_url_base() {
    // Detectar protocolo (HTTP o HTTPS)
    // Prioridad: X-Forwarded-Proto > HTTPS header > puerto 443
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $protocolo = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://';
    } elseif ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
              (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) {
        $protocolo = 'https://';
    } else {
        $protocolo = 'http://';
    }
    
    // Detectar host
    // Prioridad: X-Forwarded-Host > HTTP_HOST
    if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    } elseif (!empty($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } else {
        $host = $_SERVER['SERVER_NAME'];
    }
    
    // Obtener la ruta del directorio (por ejemplo: /sisgenot)
    $script_name = $_SERVER['SCRIPT_NAME'];
    $directorio = str_replace('\\', '/', dirname($script_name));
    
    // Si está en la raíz, el directorio será '/'
    if ($directorio === '/' || $directorio === '\\') {
        $directorio = '';
    }
    
    return $protocolo . $host . $directorio;
}

define('APP_URL', detectar_url_base());


// Configuración de rutas
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('MODELS_PATH', ROOT_PATH . '/models');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('INCLUDES_PATH', ROOT_PATH . '/includes');

// Configuración de sesiones
define('SESSION_LIFETIME', 3600); // 1 hora en segundos
define('SESSION_NAME', 'sisgenot_session');

// Configuración de notas
define('NOTA_MINIMA', 1.0);
define('NOTA_MAXIMA', 5.0);
define('NOTA_APROBACION', 3.0);

// Configuración de timezone
date_default_timezone_set('America/Bogota');

// Configuración de errores (solo para desarrollo)
// Detectar si es entorno local (localhost, 127.0.0.1, o IP local 192.168.x.x, 10.x.x.x)
function es_entorno_local() {
    // Usar el host real (prioridad a X-Forwarded-Host si existe)
    if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    } elseif (!empty($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } else {
        $host = $_SERVER['SERVER_NAME'];
    }
    
    // Quitar el puerto si existe (ej: 192.168.1.100:8080 -> 192.168.1.100)
    $host = explode(':', $host)[0];
    
    // Lista de hosts locales
    $hosts_locales = ['localhost', '127.0.0.1', '::1'];
    
    // Verificar si es un host local conocido
    if (in_array($host, $hosts_locales)) {
        return true;
    }
    
    // Verificar si es una IP local (192.168.x.x, 10.x.x.x, 172.16-31.x.x)
    if (preg_match('/^192\.168\.\d{1,3}\.\d{1,3}$/', $host) ||
        preg_match('/^10\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $host) ||
        preg_match('/^172\.(1[6-9]|2\d|3[01])\.\d{1,3}\.\d{1,3}$/', $host)) {
        return true;
    }
    
    return false;
}

if (es_entorno_local()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Función para incluir archivos de forma segura
function incluir_archivo($ruta) {
    if (file_exists($ruta)) {
        include_once $ruta;
        return true;
    }
    return false;
}

// Función para redireccionar
function redireccionar($url) {
    header("Location: " . $url);
    exit();
}

// Función para limpiar datos de entrada
function limpiar_entrada($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Función para validar email
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Función para validar nota
function validar_nota($nota) {
    $nota = floatval($nota);
    return ($nota >= NOTA_MINIMA && $nota <= NOTA_MAXIMA);
}

// Función para formatear nota
function formatear_nota($nota) {
    return number_format($nota, 1);
}

// Función para determinar estado de aprobación
function estado_aprobacion($promedio) {
    return $promedio >= NOTA_APROBACION ? 'Aprobado' : 'Reprobado';
}

// Función para calcular porcentaje de progreso (nota de 1-5 a porcentaje 0-100)
function calcular_porcentaje_progreso($nota) {
    return (($nota - NOTA_MINIMA) / (NOTA_MAXIMA - NOTA_MINIMA)) * 100;
}
?>
