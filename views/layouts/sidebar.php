<?php
// Obtener datos del usuario actual
$usuario_actual = SessionManager::obtener_usuario();
$rol_usuario = $usuario_actual['rol'];
$accion_actual = $_GET['action'] ?? '';

// Definir menús según el rol
$menus = [
    'admin' => [
        [
            'titulo' => 'Dashboard',
            'icono' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2z M3 7l9 6 9-6',
            'url' => 'admin_dashboard',
            'activo' => $accion_actual === 'admin_dashboard'
        ],
        [
            'titulo' => 'Usuarios',
            'icono' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'url' => 'admin_usuarios',
            'activo' => $accion_actual === 'admin_usuarios'
        ],
        [
            'titulo' => 'Técnicas',
            'icono' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
            'url' => 'admin_tecnicas',
            'activo' => $accion_actual === 'admin_tecnicas'
        ],
        [
            'titulo' => 'Cursos',
            'icono' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'url' => 'admin_cursos',
            'activo' => $accion_actual === 'admin_cursos'
        ],
        [
            'titulo' => 'Semestres',
            'icono' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'url' => 'admin_semestres',
            'activo' => $accion_actual === 'admin_semestres'
        ],
        [
            'titulo' => 'Asignaciones',
            'icono' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'url' => 'admin_asignaciones',
            'activo' => $accion_actual === 'admin_asignaciones'
        ],
        [
            'titulo' => 'Inscripciones',
            'icono' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
            'url' => 'admin_inscripciones',
            'activo' => $accion_actual === 'admin_inscripciones'
        ]
    ],
    'docente' => [
        [
            'titulo' => 'Dashboard',
            'icono' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z M3 7l9 6 9-6',
            'url' => 'docente_dashboard',
            'activo' => $accion_actual === 'docente_dashboard'
        ],
        [
            'titulo' => 'Mis Cursos',
            'icono' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'url' => 'docente_cursos',
            'activo' => in_array($accion_actual, ['docente_cursos', 'docente_calificar'])
        ]
    ],
    'estudiante' => [
        [
            'titulo' => 'Dashboard',
            'icono' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z M3 7l9 6 9-6',
            'url' => 'estudiante_dashboard',
            'activo' => $accion_actual === 'estudiante_dashboard'
        ],
        [
            'titulo' => 'Mis Notas',
            'icono' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'url' => 'estudiante_notas',
            'activo' => $accion_actual === 'estudiante_notas'
        ]
    ]
];

$menu_items = $menus[$rol_usuario] ?? [];
?>

<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:fixed flex flex-col">
    
    <!-- Sidebar header -->
    <div class="flex items-center justify-between h-16 px-4 bg-indigo-600 flex-shrink-0">
        <div class="flex items-center">
            <div class="h-8 w-8 bg-white rounded-lg flex items-center justify-center">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="ml-2 text-white font-semibold">SISGENOT</span>
        </div>
        
        <!-- Close button (mobile) -->
        <button id="sidebarClose" class="lg:hidden p-2 rounded-md text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-white">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- User info -->
    <div class="p-4 bg-gray-50 border-b border-gray-200 flex-shrink-0">
        <div class="flex items-center">
            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-medium text-indigo-600">
                    <?php echo strtoupper(substr($usuario_actual['nombre'], 0, 1) . substr($usuario_actual['apellido'], 0, 1)); ?>
                </span>
            </div>
            <div class="ml-3 min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-900 truncate"><?php echo escapar_html($usuario_actual['nombre'] . ' ' . $usuario_actual['apellido']); ?></p>
                <p class="text-xs text-gray-500 capitalize"><?php echo ucfirst($rol_usuario); ?></p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 overflow-y-auto">
        <ul class="space-y-1">
            <?php foreach ($menu_items as $item): ?>
            <li>
                <a href="<?php echo APP_URL; ?>/index.php?action=<?php echo $item['url']; ?>" 
                   class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out <?php echo $item['activo'] ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'; ?>">
                    
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo $item['activo'] ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500'; ?>" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $item['icono']; ?>"></path>
                    </svg>
                    
                    <span class="flex-1 truncate"><?php echo $item['titulo']; ?></span>
                    
                    <?php if ($item['activo']): ?>
                    <svg class="ml-2 h-4 w-4 text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <?php endif; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Información del sistema -->
    <div class="mt-auto p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
        <div class="text-center">
            <p class="text-xs text-gray-500">SISGENOT v<?php echo APP_VERSION; ?></p>
            <p class="text-xs text-gray-400 mt-1">&copy; <?php echo date('Y'); ?></p>
        </div>
    </div>
</div>

<!-- Sidebar overlay (mobile) -->
<div id="sidebarOverlay" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 transition-opacity duration-300 ease-linear opacity-0 pointer-events-none lg:hidden"></div>

<script>
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('opacity-0', 'pointer-events-none');
        sidebarOverlay.classList.add('opacity-100');
        document.body.classList.add('overflow-hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.remove('opacity-100');
        sidebarOverlay.classList.add('opacity-0', 'pointer-events-none');
        document.body.classList.remove('overflow-hidden');
    }

    // Event listeners
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', openSidebar);
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    });

    // Close sidebar on window resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });
});
</script>
