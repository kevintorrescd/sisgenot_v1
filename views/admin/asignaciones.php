<?php
// Obtener datos pasados desde el controlador
$asignaciones = $datos['asignaciones'] ?? [];
$docentes = $datos['docentes'] ?? [];
$cursos = $datos['cursos'] ?? [];
$semestres = $datos['semestres'] ?? [];
$semestre_activo = $datos['semestre_activo'] ?? null;
?>

<!-- Main content -->
<div class="flex-1 lg:ml-64">
    <div class="p-6">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Asignaciones Docente-Curso</h1>
                    <p class="text-gray-600">Asigna docentes a cursos por semestre</p>
                </div>
            </div>
        </div>

        <!-- Información del semestre activo -->
        <?php if ($semestre_activo): ?>
        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-indigo-800">
                        Semestre Activo: <?php echo escapar_html($semestre_activo['nombre']); ?>
                    </h3>
                    <div class="mt-2 text-sm text-indigo-700">
                        <p>Las asignaciones se realizan para este semestre por defecto.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        No hay semestre activo
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Debe configurar un semestre activo para realizar asignaciones.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Formulario de asignación -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Nueva Asignación</h2>
                        <p class="text-sm text-gray-500 mt-1">Asignar docente a un curso</p>
                    </div>
                    <div class="p-6">
                        <form id="asignacionForm" class="space-y-4">
                            <div>
                                <label for="docente_id" class="block text-sm font-medium text-gray-700">Docente</label>
                                <select id="docente_id" name="docente_id" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccionar docente</option>
                                    <?php foreach ($docentes as $docente): ?>
                                    <option value="<?php echo $docente['id']; ?>">
                                        <?php echo escapar_html($docente['nombre'] . ' ' . $docente['apellido']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="curso_id" class="block text-sm font-medium text-gray-700">Curso</label>
                                <select id="curso_id" name="curso_id" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccionar curso</option>
                                    <?php foreach ($cursos as $curso): ?>
                                    <option value="<?php echo $curso['id']; ?>">
                                        <?php echo escapar_html($curso['nombre']); ?> (<?php echo escapar_html($curso['codigo']); ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label for="semestre_id" class="block text-sm font-medium text-gray-700">Semestre</label>
                                <select id="semestre_id" name="semestre_id" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <?php if ($semestre_activo): ?>
                                    <option value="<?php echo $semestre_activo['id']; ?>" selected>
                                        <?php echo escapar_html($semestre_activo['nombre']); ?> (Activo)
                                    </option>
                                    <?php endif; ?>
                                    <?php foreach ($semestres as $semestre): ?>
                                        <?php if (!$semestre_activo || $semestre['id'] !== $semestre_activo['id']): ?>
                                        <option value="<?php echo $semestre['id']; ?>">
                                            <?php echo escapar_html($semestre['nombre']); ?>
                                        </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Crear Asignación
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Lista de asignaciones -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Asignaciones Actuales</h2>
                                <p class="text-sm text-gray-500 mt-1"><?php echo count($asignaciones); ?> asignación(es) registrada(s)</p>
                            </div>
                            
                            <!-- Búsqueda -->
                            <div class="w-64">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           id="buscarAsignaciones" 
                                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                           placeholder="Buscar...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($asignaciones)): ?>
                        <div class="overflow-x-auto">
                            <table id="tablaAsignaciones" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Docente
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Curso
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Semestre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha Asignación
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($asignaciones as $asignacion): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-blue-600">
                                                        <?php echo strtoupper(substr($asignacion['docente_nombre'], 0, 1) . substr($asignacion['docente_apellido'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo escapar_html($asignacion['docente_nombre'] . ' ' . $asignacion['docente_apellido']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        <?php echo escapar_html($asignacion['docente_email']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo escapar_html($asignacion['curso_nombre']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500 font-mono">
                                                <?php echo escapar_html($asignacion['curso_codigo']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo escapar_html($asignacion['semestre_nombre']); ?>
                                            </div>
                                            <?php if ($semestre_activo && $asignacion['semestre_id'] == $semestre_activo['id']): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Activo
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo formatear_fecha($asignacion['fecha_asignacion']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button onclick="GestionAsignaciones.eliminar(<?php echo $asignacion['id']; ?>, '<?php echo escapar_html($asignacion['docente_nombre'] . ' ' . $asignacion['docente_apellido']); ?>', '<?php echo escapar_html($asignacion['curso_nombre']); ?>')" 
                                                    class="text-red-600 hover:text-red-900">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay asignaciones</h3>
                            <p class="mt-1 text-sm text-gray-500">Comienza creando una asignación docente-curso.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
<script src="<?php echo APP_URL; ?>/public/js/admin.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar filtros
    Filtros.configurarFiltroTiempoReal('buscarAsignaciones', 'tablaAsignaciones');
    
    // Manejar envío del formulario
    document.getElementById('asignacionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        GestionAsignaciones.crear();
    });
});
</script>
