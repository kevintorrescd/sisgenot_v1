<?php
// Obtener datos pasados desde el controlador
$cursos = $datos['cursos'] ?? [];
$tecnicas = $datos['tecnicas'] ?? [];
$total_cursos = count($cursos);
?>

<!-- Main content -->
<div class="flex-1 lg:ml-64">
    <div class="p-6">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Gestión de Cursos</h1>
                    <p class="text-gray-600">Administra los cursos del sistema</p>
                </div>
                <button onclick="GestionCursos.abrirModalCrear()" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Crear Curso
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Cursos</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $total_cursos; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Cursos Activos</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php echo count(array_filter($cursos, fn($c) => $c['estado'] === 'activo')); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Técnicas</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($tecnicas); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    
                    <!-- Búsqueda -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   id="buscarCursos" 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" 
                                   placeholder="Buscar cursos...">
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="flex items-center space-x-4">
                        <select id="filtroTecnica" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                            <option value="">Todas las técnicas</option>
                            <?php foreach ($tecnicas as $tecnica): ?>
                            <option value="<?php echo $tecnica['id']; ?>"><?php echo escapar_html($tecnica['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <select id="filtroEstado" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                            <option value="">Todos los estados</option>
                            <option value="activo">Activos</option>
                            <option value="inactivo">Inactivos</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de cursos -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table id="tablaCursos" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Curso
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Código
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Técnica
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha Creación
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($cursos as $curso): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo escapar_html($curso['nombre']); ?>
                                        </div>
                                        <?php if (!empty($curso['descripcion'])): ?>
                                        <div class="text-sm text-gray-500 max-w-xs truncate">
                                            <?php echo escapar_html($curso['descripcion']); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-mono"><?php echo escapar_html($curso['codigo']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo escapar_html($curso['tecnica_nombre']); ?></div>
                                <div class="text-sm text-gray-500 font-mono"><?php echo escapar_html($curso['tecnica_codigo']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php echo $curso['estado'] === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full
                                        <?php echo $curso['estado'] === 'activo' ? 'bg-green-400' : 'bg-red-400'; ?>"></span>
                                    <?php echo ucfirst($curso['estado']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo formatear_fecha($curso['fecha_creacion']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="GestionCursos.abrirModalEditar(<?php echo $curso['id']; ?>)" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="GestionCursos.eliminar(<?php echo $curso['id']; ?>, '<?php echo escapar_html($curso['nombre']); ?>')" 
                                            class="text-red-600 hover:text-red-900">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar curso -->
<div id="cursoModal" class="fixed inset-0 hidden overflow-y-auto" style="z-index: 10000;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="cursoForm">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitulo">
                                Crear Curso
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="tecnica_id" class="block text-sm font-medium text-gray-700">Técnica</label>
                                    <select id="tecnica_id" name="tecnica_id" required
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Seleccionar técnica</option>
                                        <?php foreach ($tecnicas as $tecnica): ?>
                                        <option value="<?php echo $tecnica['id']; ?>">
                                            <?php echo escapar_html($tecnica['nombre']); ?> (<?php echo escapar_html($tecnica['codigo']); ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="codigo" class="block text-sm font-medium text-gray-700">Código</label>
                                    <input type="text" id="codigo" name="codigo" required
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="Ej: CUR001">
                                    <p class="mt-1 text-xs text-gray-500">Código único para identificar el curso</p>
                                </div>
                                
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" required
                                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="Nombre del curso">
                                </div>
                                
                                <div>
                                    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <textarea id="descripcion" name="descripcion" rows="3"
                                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                              placeholder="Descripción del curso (opcional)"></textarea>
                                </div>
                                
                                <div>
                                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                                    <select id="estado" name="estado" required
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Guardar
                    </button>
                    <button type="button" onclick="Modal.cerrar('cursoModal')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="<?php echo APP_URL; ?>/public/js/main.js"></script>
<script src="<?php echo APP_URL; ?>/public/js/admin.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar filtros
    Filtros.configurarFiltroTiempoReal('buscarCursos', 'tablaCursos');
    
    // Configurar modal
    Modal.configurarCierreExterno('cursoModal');
    
    // Manejar envío del formulario
    document.getElementById('cursoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        GestionCursos.guardar();
    });
});
</script>
