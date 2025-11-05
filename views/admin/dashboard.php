<?php
// Obtener datos pasados desde el controlador
$total_estudiantes = $datos['total_estudiantes'] ?? 0;
$total_docentes = $datos['total_docentes'] ?? 0;
$total_cursos = $datos['total_cursos'] ?? 0;
$semestre_activo = $datos['semestre_activo'] ?? null;
$usuarios_recientes = $datos['usuarios_recientes'] ?? [];
$cursos_recientes = $datos['cursos_recientes'] ?? [];
?>

<!-- Main content -->
<div class="flex-1 lg:ml-64">
    <div class="p-6">
        
        <!-- Header del dashboard -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Panel de Administración</h1>
            <p class="text-gray-600">Bienvenido al sistema de gestión de notas académicas</p>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Total Estudiantes -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Total Estudiantes</p>
                        <p class="text-3xl font-bold"><?php echo number_format($total_estudiantes); ?></p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Docentes -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Total Docentes</p>
                        <p class="text-3xl font-bold"><?php echo number_format($total_docentes); ?></p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Cursos -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Total Cursos</p>
                        <p class="text-3xl font-bold"><?php echo number_format($total_cursos); ?></p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Semestre Activo -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Semestre Activo</p>
                        <p class="text-lg font-bold">
                            <?php echo $semestre_activo ? escapar_html($semestre_activo['nombre']) : 'Ninguno'; ?>
                        </p>
                        <?php if ($semestre_activo): ?>
                        <p class="text-orange-100 text-xs mt-1">
                            <?php echo formatear_fecha($semestre_activo['fecha_inicio']); ?> - 
                            <?php echo formatear_fecha($semestre_activo['fecha_fin']); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Usuarios Recientes -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Usuarios Recientes</h2>
                        <a href="<?php echo APP_URL; ?>/index.php?action=admin_usuarios" 
                           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Ver todos
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($usuarios_recientes)): ?>
                    <div class="space-y-4">
                        <?php foreach ($usuarios_recientes as $usuario): ?>
                        <div class="flex items-center space-x-4">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-indigo-600">
                                    <?php echo strtoupper(substr($usuario['nombre'], 0, 1) . substr($usuario['apellido'], 0, 1)); ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?php echo escapar_html($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    <?php echo escapar_html($usuario['email']); ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                    <?php 
                                    switch($usuario['rol']) {
                                        case 'admin': echo 'bg-red-100 text-red-800'; break;
                                        case 'docente': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'estudiante': echo 'bg-green-100 text-green-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo ucfirst($usuario['rol']); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No hay usuarios recientes</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cursos Recientes -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Cursos Recientes</h2>
                        <a href="<?php echo APP_URL; ?>/index.php?action=admin_cursos" 
                           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Ver todos
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($cursos_recientes)): ?>
                    <div class="space-y-4">
                        <?php foreach ($cursos_recientes as $curso): ?>
                        <div class="flex items-center space-x-4">
                            <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?php echo escapar_html($curso['nombre']); ?>
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    <?php echo escapar_html($curso['codigo']); ?> - <?php echo escapar_html($curso['tecnica_nombre']); ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <p class="text-xs text-gray-400">
                                    <?php echo formatear_fecha($curso['fecha_creacion']); ?>
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No hay cursos recientes</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <a href="<?php echo APP_URL; ?>/index.php?action=admin_usuarios" 
                   class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-indigo-300">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Gestionar Usuarios</p>
                            <p class="text-sm text-gray-500">Crear, editar y eliminar usuarios</p>
                        </div>
                    </div>
                </a>

                <a href="<?php echo APP_URL; ?>/index.php?action=admin_cursos" 
                   class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-purple-300">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Gestionar Cursos</p>
                            <p class="text-sm text-gray-500">Administrar cursos y técnicas</p>
                        </div>
                    </div>
                </a>

                <a href="<?php echo APP_URL; ?>/index.php?action=admin_asignaciones" 
                   class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-blue-300">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Asignar Docentes</p>
                            <p class="text-sm text-gray-500">Asignar docentes a cursos</p>
                        </div>
                    </div>
                </a>

                <a href="<?php echo APP_URL; ?>/index.php?action=admin_inscripciones" 
                   class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-green-300">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Inscribir Estudiantes</p>
                            <p class="text-sm text-gray-500">Inscribir estudiantes a cursos</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
