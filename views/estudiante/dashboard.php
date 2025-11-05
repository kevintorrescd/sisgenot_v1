<?php
// Obtener datos pasados desde el controlador
$semestre_activo = $datos['semestre_activo'] ?? null;
$inscripciones = $datos['inscripciones'] ?? [];
$total_cursos = $datos['total_cursos'] ?? 0;
$total_calificaciones = $datos['total_calificaciones'] ?? 0;
$promedio_general = $datos['promedio_general'] ?? 0;
$cursos_aprobados = $datos['cursos_aprobados'] ?? 0;
$cursos_reprobados = $datos['cursos_reprobados'] ?? 0;
$actividad_reciente = $datos['actividad_reciente'] ?? [];

$usuario_actual = SessionManager::obtener_usuario();
?>

<!-- Main content -->
<div class="flex-1 lg:ml-64">
    <div class="p-6">
        
        <!-- Header del dashboard -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Panel del Estudiante</h1>
            <p class="text-gray-600">Bienvenido, <?php echo escapar_html($usuario_actual['nombre'] . ' ' . $usuario_actual['apellido']); ?></p>
        </div>

        <!-- Información del semestre activo -->
        <?php if ($semestre_activo): ?>
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-indigo-800">Semestre Activo</h3>
                    <div class="mt-1 text-sm text-indigo-700">
                        <p><strong><?php echo escapar_html($semestre_activo['nombre']); ?></strong></p>
                        <p><?php echo formatear_fecha($semestre_activo['fecha_inicio']); ?> - <?php echo formatear_fecha($semestre_activo['fecha_fin']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Sin Semestre Activo</h3>
                    <p class="mt-1 text-sm text-yellow-700">No hay un semestre activo configurado. Contacta al administrador.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Total Cursos -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Cursos Inscritos</p>
                        <p class="text-3xl font-bold"><?php echo number_format($total_cursos); ?></p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Promedio General -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Promedio General</p>
                        <p class="text-3xl font-bold"><?php echo formatear_nota($promedio_general); ?></p>
                        <p class="text-purple-100 text-xs mt-1"><?php echo estado_aprobacion($promedio_general); ?></p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Cursos Aprobados -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Cursos Aprobados</p>
                        <p class="text-3xl font-bold"><?php echo number_format($cursos_aprobados); ?></p>
                        <?php if ($total_cursos > 0): ?>
                        <p class="text-green-100 text-xs mt-1"><?php echo round(($cursos_aprobados / $total_cursos) * 100); ?>% del total</p>
                        <?php endif; ?>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Calificaciones -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium">Total Calificaciones</p>
                        <p class="text-3xl font-bold"><?php echo number_format($total_calificaciones); ?></p>
                        <?php if ($cursos_reprobados > 0): ?>
                        <p class="text-orange-100 text-xs mt-1"><?php echo $cursos_reprobados; ?> curso(s) reprobado(s)</p>
                        <?php endif; ?>
                    </div>
                    <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Mis Cursos -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Mis Cursos</h2>
                        <a href="<?php echo APP_URL; ?>/index.php?action=estudiante_notas" 
                           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Ver todas las notas
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($inscripciones)): ?>
                    <div class="space-y-4">
                        <?php foreach (array_slice($inscripciones, 0, 5) as $inscripcion): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        <?php echo escapar_html($inscripcion['curso_nombre']); ?>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        <?php echo escapar_html($inscripcion['curso_codigo']); ?> - <?php echo escapar_html($inscripcion['tecnica_nombre']); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php if ($inscripcion['promedio'] > 0): ?>
                                <p class="text-lg font-bold <?php echo $inscripcion['promedio'] >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo formatear_nota($inscripcion['promedio']); ?>
                                </p>
                                <p class="text-xs <?php echo $inscripcion['promedio'] >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $inscripcion['estado']; ?>
                                </p>
                                <?php else: ?>
                                <p class="text-sm text-gray-500">Sin calificar</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No tienes cursos inscritos</p>
                        <p class="text-xs text-gray-400 mt-1">Contacta al administrador para gestionar inscripciones</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Calificaciones Recientes</h2>
                        <a href="<?php echo APP_URL; ?>/index.php?action=estudiante_notas" 
                           class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            Ver historial
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($actividad_reciente)): ?>
                    <div class="space-y-4">
                        <?php foreach ($actividad_reciente as $actividad): ?>
                        <div class="flex items-center space-x-4">
                            <div class="h-10 w-10 rounded-full <?php echo $actividad['nota'] >= 3.0 ? 'bg-green-100' : 'bg-red-100'; ?> flex items-center justify-center">
                                <span class="text-sm font-bold <?php echo $actividad['nota'] >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo formatear_nota($actividad['nota']); ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    <?php echo escapar_html($actividad['curso_nombre']); ?>
                                </p>
                                <p class="text-sm text-gray-500 truncate">
                                    <?php echo escapar_html($actividad['curso_codigo']); ?> - <?php echo formatear_fecha($actividad['fecha_registro']); ?>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php 
                                    if ($actividad['nota'] >= 4.5) echo 'bg-green-100 text-green-800';
                                    elseif ($actividad['nota'] >= 4.0) echo 'bg-blue-100 text-blue-800';
                                    elseif ($actividad['nota'] >= 3.0) echo 'bg-yellow-100 text-yellow-800';
                                    else echo 'bg-red-100 text-red-800';
                                    ?>">
                                    <?php 
                                    if ($actividad['nota'] >= 4.5) echo 'Excelente';
                                    elseif ($actividad['nota'] >= 4.0) echo 'Bueno';
                                    elseif ($actividad['nota'] >= 3.0) echo 'Aceptable';
                                    else echo 'Deficiente';
                                    ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No hay calificaciones recientes</p>
                        <p class="text-xs text-gray-400 mt-1">Las calificaciones aparecerán aquí cuando sean registradas</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Progreso visual -->
        <?php if ($promedio_general > 0): ?>
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Progreso Académico</h2>
                
                <div class="space-y-4">
                    <!-- Barra de progreso general -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Promedio General</span>
                            <span class="text-sm font-bold <?php echo $promedio_general >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo formatear_nota($promedio_general); ?> / <?php echo NOTA_MAXIMA; ?>
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full <?php echo obtener_color_barra_progreso($promedio_general); ?>" 
                                 style="width: <?php echo calcular_porcentaje_progreso($promedio_general); ?>%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span><?php echo NOTA_MINIMA; ?></span>
                            <span><?php echo NOTA_APROBACION; ?> (Aprobación)</span>
                            <span><?php echo NOTA_MAXIMA; ?></span>
                        </div>
                    </div>

                    <!-- Estadísticas de cursos -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600"><?php echo $cursos_aprobados; ?></p>
                            <p class="text-sm text-green-700">Cursos Aprobados</p>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <p class="text-2xl font-bold text-red-600"><?php echo $cursos_reprobados; ?></p>
                            <p class="text-sm text-red-700">Cursos Reprobados</p>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-2xl font-bold text-blue-600"><?php echo $total_calificaciones; ?></p>
                            <p class="text-sm text-blue-700">Total Calificaciones</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Acciones rápidas -->
        <?php if ($semestre_activo && !empty($inscripciones)): ?>
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <a href="<?php echo APP_URL; ?>/index.php?action=estudiante_notas" 
                   class="bg-white p-6 rounded-lg shadow hover:shadow-md transition-shadow duration-200 border border-gray-200 hover:border-indigo-300">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Ver Mis Notas</p>
                            <p class="text-sm text-gray-500">Consultar calificaciones detalladas</p>
                        </div>
                    </div>
                </a>

                <div class="bg-white p-6 rounded-lg shadow border border-gray-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Ayuda y Soporte</p>
                            <p class="text-sm text-gray-500">Contacta al administrador para dudas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
