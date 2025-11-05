<?php
// Obtener datos pasados desde el controlador
$semestre_activo = $datos['semestre_activo'] ?? null;
$curso_asignado = $datos['curso_asignado'] ?? null;
$estudiantes = $datos['estudiantes'] ?? [];
$estudiante_seleccionado = $datos['estudiante_seleccionado'] ?? null;
$calificaciones_estudiante = $datos['calificaciones_estudiante'] ?? [];
$promedio_estudiante = $datos['promedio_estudiante'] ?? 0;
?>

<!-- Main content -->
<div class="flex-1 lg:ml-64">
    <div class="p-6">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Calificar Estudiantes</h1>
                    <p class="text-gray-600">Registra y gestiona las calificaciones de tus estudiantes</p>
                </div>
                <a href="<?php echo APP_URL; ?>/index.php?action=docente_cursos" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a Cursos
                </a>
            </div>
        </div>

        <!-- Información del curso -->
        <?php if ($curso_asignado): ?>
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-indigo-800">
                            <?php echo escapar_html($curso_asignado['curso_nombre']); ?>
                        </h3>
                        <p class="mt-1 text-sm text-indigo-700">
                            <?php echo escapar_html($curso_asignado['curso_codigo']); ?> - 
                            <?php echo escapar_html($curso_asignado['tecnica_nombre']); ?> - 
                            <?php echo escapar_html($semestre_activo['nombre']); ?>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-indigo-800 text-lg font-bold"><?php echo count($estudiantes); ?></p>
                    <p class="text-indigo-700 text-sm">Estudiantes</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Lista de estudiantes -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Estudiantes</h2>
                        <p class="text-sm text-gray-500 mt-1">Selecciona un estudiante para calificar</p>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($estudiantes)): ?>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <?php foreach ($estudiantes as $estudiante): ?>
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer <?php echo $estudiante_seleccionado && $estudiante_seleccionado['estudiante_id'] == $estudiante['estudiante_id'] ? 'bg-indigo-50 border-indigo-300' : ''; ?>"
                                 onclick="seleccionarEstudiante(<?php echo $estudiante['estudiante_id']; ?>)">
                                <div class="flex items-center space-x-3">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-xs font-medium text-indigo-600">
                                            <?php echo strtoupper(substr($estudiante['nombre'], 0, 1) . substr($estudiante['apellido'], 0, 1)); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            <?php echo escapar_html($estudiante['nombre'] . ' ' . $estudiante['apellido']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
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
                            <p class="mt-2 text-sm text-gray-500">No hay estudiantes inscritos</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Panel de calificación -->
            <div class="lg:col-span-2">
                <?php if ($estudiante_seleccionado): ?>
                
                <!-- Información del estudiante -->
                <div class="bg-white rounded-lg shadow-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-lg font-medium text-indigo-600">
                                        <?php echo strtoupper(substr($estudiante_seleccionado['nombre'], 0, 1) . substr($estudiante_seleccionado['apellido'], 0, 1)); ?>
                                    </span>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">
                                        <?php echo escapar_html($estudiante_seleccionado['nombre'] . ' ' . $estudiante_seleccionado['apellido']); ?>
                                    </h2>
                                    <p class="text-sm text-gray-500"><?php echo escapar_html($estudiante_seleccionado['email']); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold <?php echo $promedio_estudiante >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo formatear_nota($promedio_estudiante); ?>
                                </p>
                                <p class="text-sm text-gray-500">Promedio</p>
                                <p class="text-xs font-medium <?php echo $promedio_estudiante >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo estado_aprobacion($promedio_estudiante); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de nueva calificación -->
                <div class="bg-white rounded-lg shadow-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Registrar Nueva Calificación</h3>
                    </div>
                    <div class="p-6">
                        <form id="calificacionForm" class="space-y-4">
                            <input type="hidden" id="inscripcion_id" value="<?php echo $estudiante_seleccionado['inscripcion_id']; ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nota" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nota (<?php echo NOTA_MINIMA; ?> - <?php echo NOTA_MAXIMA; ?>)
                                    </label>
                                    <input type="number" 
                                           id="nota" 
                                           name="nota" 
                                           min="<?php echo NOTA_MINIMA; ?>" 
                                           max="<?php echo NOTA_MAXIMA; ?>" 
                                           step="0.1" 
                                           required
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                
                                <div class="flex items-end">
                                    <button type="submit" 
                                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Registrar Calificación
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">
                                    Observaciones (Opcional)
                                </label>
                                <textarea id="observaciones" 
                                          name="observaciones" 
                                          rows="3" 
                                          maxlength="500"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                          placeholder="Comentarios adicionales sobre la calificación..."></textarea>
                                <p class="mt-1 text-xs text-gray-500">Máximo 500 caracteres</p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Historial de calificaciones -->
                <div class="bg-white rounded-lg shadow-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Historial de Calificaciones</h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($calificaciones_estudiante)): ?>
                        <div class="space-y-4 max-h-64 overflow-y-auto">
                            <?php foreach ($calificaciones_estudiante as $calificacion): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-xl font-bold <?php echo $calificacion['nota'] >= 3.0 ? 'text-green-600' : 'text-red-600'; ?>">
                                            <?php echo formatear_nota($calificacion['nota']); ?>
                                        </span>
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                <?php echo formatear_fecha_hora($calificacion['fecha_registro']); ?>
                                            </p>
                                            <?php if (!empty($calificacion['observaciones'])): ?>
                                            <p class="text-sm text-gray-700 mt-1">
                                                <?php echo escapar_html($calificacion['observaciones']); ?>
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="eliminarCalificacion(<?php echo $calificacion['id']; ?>)" 
                                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No hay calificaciones registradas</p>
                            <p class="text-xs text-gray-400 mt-1">Las calificaciones que registres aparecerán aquí</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php else: ?>
                <!-- Sin estudiante seleccionado -->
                <div class="bg-white rounded-lg shadow-lg">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Selecciona un estudiante</h3>
                        <p class="mt-2 text-sm text-gray-500">Elige un estudiante de la lista para comenzar a calificar</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Función para seleccionar estudiante
function seleccionarEstudiante(estudianteId) {
    const url = new URL(window.location);
    url.searchParams.set('estudiante_id', estudianteId);
    window.location.href = url.toString();
}

// Manejar envío del formulario de calificación
document.getElementById('calificacionForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('inscripcion_id', document.getElementById('inscripcion_id').value);
    formData.append('nota', document.getElementById('nota').value);
    formData.append('observaciones', document.getElementById('observaciones').value);
    
    // Validar nota
    const nota = parseFloat(document.getElementById('nota').value);
    if (nota < <?php echo NOTA_MINIMA; ?> || nota > <?php echo NOTA_MAXIMA; ?>) {
        mostrarError(`La nota debe estar entre <?php echo NOTA_MINIMA; ?> y <?php echo NOTA_MAXIMA; ?>`);
        return;
    }
    
    mostrarLoading('Guardando calificación...');
    
    fetch('<?php echo APP_URL; ?>/index.php?action=ajax_guardar_calificacion', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        ocultarLoading();
        
        if (data.exito) {
            mostrarExito(data.mensaje);
            // Recargar la página para mostrar la nueva calificación
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            mostrarError(data.mensaje);
        }
    })
    .catch(error => {
        ocultarLoading();
        console.error('Error:', error);
        mostrarError('Error al guardar la calificación');
    });
});

// Función para eliminar calificación
function eliminarCalificacion(calificacionId) {
    mostrarConfirmacion('¿Estás seguro de que quieres eliminar esta calificación?', '¡Atención!')
        .then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', calificacionId);
                
                mostrarLoading('Eliminando calificación...');
                
                fetch('<?php echo APP_URL; ?>/index.php?action=ajax_eliminar_calificacion', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    ocultarLoading();
                    
                    if (data.exito) {
                        mostrarExito(data.mensaje);
                        // Recargar la página para actualizar el historial
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        mostrarError(data.mensaje);
                    }
                })
                .catch(error => {
                    ocultarLoading();
                    console.error('Error:', error);
                    mostrarError('Error al eliminar la calificación');
                });
            }
        });
}

// Validación en tiempo real de la nota
document.getElementById('nota')?.addEventListener('input', function() {
    const nota = parseFloat(this.value);
    const isValid = !isNaN(nota) && nota >= <?php echo NOTA_MINIMA; ?> && nota <= <?php echo NOTA_MAXIMA; ?>;
    
    if (this.value && !isValid) {
        this.classList.add('border-red-500');
        this.classList.remove('border-gray-300');
    } else {
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300');
    }
});
</script>
