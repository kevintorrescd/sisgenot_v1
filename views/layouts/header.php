<?php
// Obtener datos del usuario actual
$usuario_actual = SessionManager::obtener_usuario();
$nombre_completo = $usuario_actual['nombre'] . ' ' . $usuario_actual['apellido'];
$rol_usuario = $usuario_actual['rol'];

// Definir título de página según la acción
$titulos_pagina = [
    'admin_dashboard' => 'Panel de Administración',
    'admin_usuarios' => 'Gestión de Usuarios',
    'admin_tecnicas' => 'Gestión de Técnicas',
    'admin_cursos' => 'Gestión de Cursos',
    'admin_semestres' => 'Gestión de Semestres',
    'admin_asignaciones' => 'Asignaciones Docente-Curso',
    'admin_inscripciones' => 'Inscripciones Estudiante-Curso',
    'docente_dashboard' => 'Panel del Docente',
    'docente_cursos' => 'Mis Cursos',
    'docente_calificar' => 'Calificar Estudiantes',
    'estudiante_dashboard' => 'Panel del Estudiante',
    'estudiante_notas' => 'Mis Notas'
];

$accion_actual = $_GET['action'] ?? '';
$titulo_pagina = $titulos_pagina[$accion_actual] ?? 'SisGeNot';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/styles.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo APP_URL; ?>/public/assets/images/logo.png">
    
    <!-- Meta tags adicionales -->
    <meta name="description" content="Sistema de Gestión de Notas Académicas">
    <meta name="author" content="SiSGENOT">
    <meta name="robots" content="noindex, nofollow">
</head>
<body class="bg-gray-50">
    
    <!-- Navigation -->
    <nav class="bg-gradient-to-br from-indigo-700 via-indigo-600 to-blue-700 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <!-- Logo y título (Izquierda) -->
                <div class="flex items-center">
                    <button id="sidebarToggle" class="lg:hidden p-2 rounded-md text-blue-100 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white/50">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <div class="flex items-center ml-4 lg:ml-0">
                        <div class="h-9 w-9 bg-white rounded-lg p-1.5 shadow-md">
                            <img src="<?php echo APP_URL; ?>/public/assets/images/logo.png" alt="SISGENOT Logo" class="h-full w-full object-contain">
                        </div>
                        <div class="ml-3">
                            <h1 class="text-xl font-bold text-white">SISGENOT</h1>
                        </div>
                    </div>
                </div>

                <!-- Título de página (Centro) -->
                <div class="hidden md:flex items-center absolute left-1/2 transform -translate-x-1/2">
                    <h2 class="text-lg font-semibold text-white"><?php echo $titulo_pagina; ?></h2>
                </div>

                <!-- User menu (Derecha) -->
                <div class="flex items-center space-x-4">
                    
                    <!-- Notificaciones -->
                    <button class="p-2 rounded-full text-blue-100 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/50 relative transition">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <!-- Badge de notificación -->
                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-indigo-700"></span>
                    </button>

                    <!-- Dropdown del usuario -->
                    <div class="relative">
                        <button id="userMenuButton" class="flex items-center space-x-3 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-white/50 p-2 hover:bg-white/10 transition">
                            
                            <!-- Avatar -->
                            <div class="h-8 w-8 rounded-full bg-white flex items-center justify-center">
                                <span class="text-sm font-semibold text-indigo-600">
                                    <?php echo strtoupper(substr($usuario_actual['nombre'], 0, 1) . substr($usuario_actual['apellido'], 0, 1)); ?>
                                </span>
                            </div>
                            
                            <!-- Información del usuario -->
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold text-white"><?php echo escapar_html($nombre_completo); ?></p>
                                <p class="text-xs text-blue-100 capitalize"><?php echo ucfirst($rol_usuario); ?></p>
                            </div>
                            
                            <!-- Chevron -->
                            <svg id="userMenuChevron" class="h-4 w-4 text-blue-100 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                
                                <!-- Información del usuario -->
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900"><?php echo escapar_html($nombre_completo); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo escapar_html($usuario_actual['email']); ?></p>
                                </div>
                                
                                <!-- Opciones del menú -->
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Mi Perfil
                                </a>
                                
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Configuración
                                </a>
                                
                                <div class="border-t border-gray-100"></div>
                                
                                <a href="<?php echo APP_URL; ?>/index.php?action=logout" class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50 hover:text-red-900">
                                    <svg class="inline h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Cerrar Sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Título de página (mobile) -->
    <div class="md:hidden bg-gradient-to-r from-indigo-600 to-blue-600 px-4 py-3 shadow-md">
        <h2 class="text-lg font-semibold text-white"><?php echo $titulo_pagina; ?></h2>
    </div>

    <!-- JavaScript para dropdown del usuario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('userMenuButton');
            const userMenuDropdown = document.getElementById('userMenuDropdown');
            const userMenuChevron = document.getElementById('userMenuChevron');
            
            if (userMenuButton && userMenuDropdown) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = userMenuDropdown.classList.contains('hidden');
                    
                    if (isHidden) {
                        userMenuDropdown.classList.remove('hidden');
                        userMenuChevron.style.transform = 'rotate(180deg)';
                    } else {
                        userMenuDropdown.classList.add('hidden');
                        userMenuChevron.style.transform = 'rotate(0deg)';
                    }
                });
                
                // Cerrar dropdown al hacer clic fuera
                document.addEventListener('click', function() {
                    userMenuDropdown.classList.add('hidden');
                    userMenuChevron.style.transform = 'rotate(0deg)';
                });
                
                // Prevenir que el dropdown se cierre al hacer clic dentro
                userMenuDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
    </script>

    <!-- Flash Messages -->
    <?php 
    $mensaje = SessionManager::obtener_mensaje();
    if ($mensaje): 
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?php echo $mensaje['tipo'] === 'success' ? 'success' : ($mensaje['tipo'] === 'warning' ? 'warning' : 'error'); ?>',
                title: '<?php 
                    switch($mensaje['tipo']) {
                        case 'success': echo '¡Éxito!'; break;
                        case 'warning': echo '¡Atención!'; break;
                        case 'info': echo 'Información'; break;
                        default: echo '¡Error!';
                    }
                ?>',
                text: '<?php echo addslashes($mensaje['mensaje']); ?>',
                confirmButtonColor: '#4F46E5',
                confirmButtonText: 'Entendido',
                timer: <?php echo $mensaje['tipo'] === 'success' ? '3000' : '0'; ?>,
                timerProgressBar: <?php echo $mensaje['tipo'] === 'success' ? 'true' : 'false'; ?>
            });
        });
    </script>
    <?php endif; ?>

    <!-- Main Content Container -->
    <div class="flex min-h-screen bg-gray-50 pt-16"><?php // pt-16 para compensar el header fijo ?>
        
        <!-- Sidebar -->
        <?php include __DIR__ . '/sidebar.php'; ?>
