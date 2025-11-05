<?php
// Obtener datos pasados desde el controlador
$semestres = $datos['semestres'] ?? [];
$semestre_seleccionado = $datos['semestre_seleccionado'] ?? null;
$inscripciones_con_notas = $datos['inscripciones_con_notas'] ?? [];
$promedio_general = $datos['promedio_general'] ?? 0;
$total_calificaciones = $datos['total_calificaciones'] ?? 0;
$total_cursos = $datos['total_cursos'] ?? 0;
?>

<!-- Main content -->
<div class="flex-1 lg:ml-64">
    <div class="p-6">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Mis Notas</h1>
                    <p class="text-gray-600">Consulta tus calificaciones y progreso académico</p>
                </div>
                <a href="<?php echo APP_URL; ?>/index.php?action=estudiante_dashboard" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Selector de semestre -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Seleccionar Semestre</h2>
                    <p class="text-sm text-gray-500 mt-1">Elige el semestre para ver tus calificaciones</p>
                </div>
                <div class="min-w-0 flex-1 max-w-xs ml-4">
                    <select id="semestreSelector" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            onchange="cambiarSemestre()">
                        <option value="">Seleccionar semestre...</option>
                        <?php foreach ($semestres as $semestre): ?>
                        <option value="<?php echo $semestre['id']; ?>" 
                                <?php echo $semestre_seleccionado && $semestre['id'] == $semestre_seleccionado['id'] ? 'selected' : ''; ?>>
                            <?php echo escapar_html($semestre['nombre']); ?>
                            <?php if ($semestre['estado'] === 'activo'): ?>
                            (Activo)
                            <?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <?php if ($semestre_seleccionado): ?>
        
        <!-- Información del semestre seleccionado -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-indigo-800">
                            <?php echo escapar_html($semestre_seleccionado['nombre']); ?>
                            <?php if ($semestre_seleccionado['estado'] === 'activo'): ?>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Activo
                            </span>
                            <?php endif; ?>
                        </h3>
                        <p class="mt-1 text-sm text-indigo-700">
                            <?php echo formatear_fecha($semestre_seleccionado['fecha_inicio']); ?> - 
                            <?php echo formatear_fecha($semestre_seleccionado['fecha_fin']); ?>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-indigo-800 text-lg font-bold"><?php echo formatear_nota($promedio_general); ?></p>
                    <p class="text-indigo-700 text-sm">Promedio General</p>
                    <p class="text-indigo-600 text-xs"><?php echo estado_aprobacion($promedio_general); ?></p>
                </div>
            </div>
        </div>

        <!-- Estadísticas del semestre -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Cursos Inscritos</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $total_cursos; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Calificaciones</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $total_calificaciones; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 <?php echo $promedio_general >= 3.0 ? 'bg-green-100' : 'bg-red-100'; ?> rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 <?php echo $promedio_general >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <?php if ($promedio_general >= 3.0): ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <?php else: ?>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                <?php endif; ?>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Estado General</p>
                        <p class="text-lg font-bold <?php echo $promedio_general >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo estado_aprobacion($promedio_general); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de cursos con notas -->
        <?php if (!empty($inscripciones_con_notas)): ?>
        <div class="space-y-6">
            <?php foreach ($inscripciones_con_notas as $curso_data): ?>
            <?php 
            $inscripcion = $curso_data['inscripcion'];
            $calificaciones = $curso_data['calificaciones'];
            $promedio = $curso_data['promedio'];
            $estado = $curso_data['estado'];
            $porcentaje_progreso = $curso_data['porcentaje_progreso'];
            ?>
            
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                
                <!-- Header del curso -->
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-white">
                                <?php echo escapar_html($inscripcion['curso_nombre']); ?>
                            </h3>
                            <p class="text-indigo-100 text-sm">
                                <?php echo escapar_html($inscripcion['curso_codigo']); ?> - 
                                <?php echo escapar_html($inscripcion['tecnica_nombre']); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-white text-2xl font-bold"><?php echo formatear_nota($promedio); ?></p>
                            <p class="text-indigo-100 text-sm"><?php echo $estado; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Contenido del curso -->
                <div class="p-6">
                    
                    <!-- Barra de progreso -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Progreso del Curso</span>
                            <span class="text-sm font-bold <?php echo $promedio >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo formatear_nota($promedio); ?> / <?php echo NOTA_MAXIMA; ?>
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full <?php echo obtener_color_barra_progreso($promedio); ?>" 
                                 style="width: <?php echo $porcentaje_progreso; ?>%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span><?php echo NOTA_MINIMA; ?></span>
                            <span><?php echo NOTA_APROBACION; ?> (Aprobación)</span>
                            <span><?php echo NOTA_MAXIMA; ?></span>
                        </div>
                    </div>

                    <!-- Calificaciones -->
                    <?php if (!empty($calificaciones)): ?>
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-medium text-gray-900">Calificaciones</h4>
                            <span class="text-sm text-gray-500"><?php echo count($calificaciones); ?> calificación(es)</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($calificaciones as $calificacion): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-2xl font-bold <?php echo obtener_color_nota($calificacion['nota']); ?>">
                                        <?php echo formatear_nota($calificacion['nota']); ?>
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php 
                                        if ($calificacion['nota'] >= 4.5) echo 'bg-green-100 text-green-800';
                                        elseif ($calificacion['nota'] >= 4.0) echo 'bg-blue-100 text-blue-800';
                                        elseif ($calificacion['nota'] >= 3.0) echo 'bg-yellow-100 text-yellow-800';
                                        else echo 'bg-red-100 text-red-800';
                                        ?>">
                                        <?php 
                                        if ($calificacion['nota'] >= 4.5) echo 'Excelente';
                                        elseif ($calificacion['nota'] >= 4.0) echo 'Bueno';
                                        elseif ($calificacion['nota'] >= 3.0) echo 'Aceptable';
                                        else echo 'Deficiente';
                                        ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">
                                    <?php echo formatear_fecha_hora($calificacion['fecha_registro']); ?>
                                </p>
                                <?php if (!empty($calificacion['observaciones'])): ?>
                                <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded">
                                    <?php echo escapar_html($calificacion['observaciones']); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <?php else: ?>
                    <!-- Sin calificaciones -->
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No hay calificaciones registradas</p>
                        <p class="text-xs text-gray-400 mt-1">Las calificaciones aparecerán aquí cuando sean registradas por el docente</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <!-- Sin cursos inscritos -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No tienes cursos inscritos</h3>
                <p class="mt-2 text-sm text-gray-500">No hay cursos inscritos en el semestre seleccionado.</p>
                <p class="text-xs text-gray-400 mt-1">Contacta al administrador para gestionar tus inscripciones</p>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- Sin semestre seleccionado -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Selecciona un semestre</h3>
                <p class="mt-2 text-sm text-gray-500">Elige un semestre del selector de arriba para ver tus calificaciones.</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Función para cambiar semestre
function cambiarSemestre() {
    const selector = document.getElementById('semestreSelector');
    const semestreId = selector.value;
    
    if (semestreId) {
        const url = new URL(window.location);
        url.searchParams.set('semestre_id', semestreId);
        window.location.href = url.toString();
    } else {
        // Si no se selecciona semestre, ir a la página sin parámetros
        const url = new URL(window.location);
        url.searchParams.delete('semestre_id');
        window.location.href = url.toString();
    }
}

// Animaciones de entrada
document.addEventListener('DOMContentLoaded', function() {
    // Animar las tarjetas de cursos
    const cursoCards = document.querySelectorAll('.bg-white.rounded-lg.shadow-lg');
    cursoCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
