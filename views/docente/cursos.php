<?php
// Obtener datos pasados desde el controlador
$semestre_activo = $datos['semestre_activo'] ?? null;
$cursos_con_estudiantes = $datos['cursos_con_estudiantes'] ?? [];
?>

<!-- Main content -->
<div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
    <div class="flex-1 p-6">
        
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Mis Cursos</h1>
            <p class="text-gray-600">Gestiona tus cursos asignados y estudiantes inscritos</p>
        </div>

        <!-- Información del semestre -->
        <?php if ($semestre_activo): ?>
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-indigo-800">Semestre Activo: <?php echo escapar_html($semestre_activo['nombre']); ?></h3>
                    <p class="mt-1 text-sm text-indigo-700">
                        <?php echo formatear_fecha($semestre_activo['fecha_inicio']); ?> - <?php echo formatear_fecha($semestre_activo['fecha_fin']); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Lista de cursos -->
        <?php if (!empty($cursos_con_estudiantes)): ?>
        <div class="space-y-6">
            <?php foreach ($cursos_con_estudiantes as $curso): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                
                <!-- Header del curso -->
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-white">
                                <?php echo escapar_html($curso['curso_nombre']); ?>
                            </h2>
                            <p class="text-indigo-100 text-sm">
                                <?php echo escapar_html($curso['curso_codigo']); ?> - <?php echo escapar_html($curso['tecnica_nombre']); ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-white text-lg font-bold"><?php echo $curso['total_estudiantes']; ?></p>
                            <p class="text-indigo-100 text-sm">Estudiantes</p>
                        </div>
                    </div>
                </div>

                <!-- Contenido del curso -->
                <div class="p-6">
                    <?php if (!empty($curso['estudiantes'])): ?>
                    
                    <!-- Acciones del curso -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Estudiantes Inscritos</h3>
                        <a href="<?php echo APP_URL; ?>/index.php?action=docente_calificar&curso_id=<?php echo $curso['curso_id']; ?>" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Calificar Estudiantes
                        </a>
                    </div>

                    <!-- Tabla de estudiantes -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estudiante
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha Inscripción
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($curso['estudiantes'] as $estudiante): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-indigo-600">
                                                    <?php echo strtoupper(substr($estudiante['nombre'], 0, 1) . substr($estudiante['apellido'], 0, 1)); ?>
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo escapar_html($estudiante['nombre'] . ' ' . $estudiante['apellido']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo escapar_html($estudiante['email']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            <?php echo formatear_fecha($estudiante['fecha_inscripcion']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="<?php echo APP_URL; ?>/index.php?action=docente_calificar&curso_id=<?php echo $curso['curso_id']; ?>&estudiante_id=<?php echo $estudiante['estudiante_id']; ?>" 
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full text-green-700 bg-green-100 hover:bg-green-200">
                                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Calificar
                                            </a>
                                            <button onclick="verHistorialEstudiante(<?php echo $estudiante['inscripcion_id']; ?>, '<?php echo escapar_html($estudiante['nombre'] . ' ' . $estudiante['apellido']); ?>')" 
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full text-blue-700 bg-blue-100 hover:bg-blue-200">
                                                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                                Historial
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php else: ?>
                    <!-- Sin estudiantes -->
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No hay estudiantes inscritos en este curso</p>
                        <p class="text-xs text-gray-400 mt-1">Contacta al administrador para gestionar inscripciones</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <!-- Sin cursos asignados -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No tienes cursos asignados</h3>
                <p class="mt-2 text-sm text-gray-500">
                    <?php if (!$semestre_activo): ?>
                    No hay un semestre activo configurado.
                    <?php else: ?>
                    No tienes cursos asignados para el semestre actual.
                    <?php endif ?>
                </p>
                <p class="text-xs text-gray-400 mt-1">Contacta al administrador para obtener asignaciones de cursos</p>
                
                <div class="mt-6">
                    <a href="<?php echo APP_URL; ?>/index.php?action=docente_dashboard" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para historial de calificaciones -->
<div id="historialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="historialTitulo">
                            Historial de Calificaciones
                        </h3>
                        <div id="historialContenido">
                            <!-- Contenido del historial se carga aquí -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="cerrarHistorialModal()" 
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para ver historial de calificaciones
function verHistorialEstudiante(inscripcionId, nombreEstudiante) {
    document.getElementById('historialTitulo').textContent = `Historial de ${nombreEstudiante}`;
    document.getElementById('historialContenido').innerHTML = '<div class="text-center py-4"><div class="loading"></div><p class="mt-2 text-sm text-gray-500">Cargando historial...</p></div>';
    document.getElementById('historialModal').classList.remove('hidden');
    
    // Cargar historial via AJAX
    fetch(`<?php echo APP_URL; ?>/index.php?action=ajax_obtener_calificaciones_estudiante&inscripcion_id=${inscripcionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                mostrarHistorial(data.datos);
            } else {
                document.getElementById('historialContenido').innerHTML = 
                    `<div class="text-center py-4 text-red-600">${data.mensaje}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('historialContenido').innerHTML = 
                '<div class="text-center py-4 text-red-600">Error al cargar el historial</div>';
        });
}

// Función para mostrar el historial
function mostrarHistorial(datos) {
    let html = '';
    
    if (datos.calificaciones && datos.calificaciones.length > 0) {
        html += `
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Promedio:</span>
                    <span class="text-lg font-bold ${datos.promedio >= 3.0 ? 'text-green-600' : 'text-red-600'}">
                        ${datos.promedio.toFixed(1)}
                    </span>
                </div>
                <div class="flex justify-between items-center mt-1">
                    <span class="text-sm text-gray-600">Estado:</span>
                    <span class="text-sm font-medium ${datos.promedio >= 3.0 ? 'text-green-600' : 'text-red-600'}">
                        ${datos.estado}
                    </span>
                </div>
            </div>
            
            <div class="space-y-3 max-h-64 overflow-y-auto">
        `;
        
        datos.calificaciones.forEach(calificacion => {
            const fecha = new Date(calificacion.fecha_registro).toLocaleDateString('es-ES');
            html += `
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-bold ${calificacion.nota >= 3.0 ? 'text-green-600' : 'text-red-600'}">
                                    ${parseFloat(calificacion.nota).toFixed(1)}
                                </span>
                                <span class="text-sm text-gray-500">${fecha}</span>
                            </div>
                            ${calificacion.observaciones ? `<p class="text-sm text-gray-600 mt-1">${calificacion.observaciones}</p>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
    } else {
        html = '<div class="text-center py-8 text-gray-500">No hay calificaciones registradas</div>';
    }
    
    document.getElementById('historialContenido').innerHTML = html;
}

// Función para cerrar el modal
function cerrarHistorialModal() {
    document.getElementById('historialModal').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('historialModal').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarHistorialModal();
    }
});
</script>
