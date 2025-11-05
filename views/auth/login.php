<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?php echo APP_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo APP_URL; ?>/public/assets/images/logo.png">
    
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body class="bg-white min-h-screen">
    
    <div class="flex min-h-screen">
        <!-- Panel Izquierdo - Información -->
        <div class="hidden lg:flex lg:w-[80%] bg-gradient-to-br from-indigo-700 via-indigo-600 to-blue-700 p-12 flex-col justify-between relative overflow-hidden">
            <!-- Decoración de fondo -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/3 translate-y-1/3"></div>
            </div>
            
            <!-- Logo y título (arriba) -->
            <div class="relative z-10 flex items-center space-x-4 animate-fade-in-up">
                <div class="h-14 w-14 bg-white rounded-lg p-2 shadow-lg">
                    <img src="<?php echo APP_URL; ?>/public/assets/images/logo.png" alt="SISGENOT Logo" class="h-full w-full object-contain">
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">SISGENOT</h1>
                    
                </div>
            </div>
            
            <!-- Contenido central -->
            <div class="relative z-10 flex-grow flex flex-col justify-center -mt-16">
                <!-- Título principal -->
                <div class="mb-10 animate-fade-in-up" style="animation-delay: 0.1s;">
                    <h2 class="text-4xl lg:text-5xl font-bold text-white leading-tight mb-6">
                        Gestiona tu experiencia académica
                    </h2>
                    <p class="text-blue-100 text-lg leading-relaxed">
                        Accede a tus calificaciones, y gestiona tus asignaturas en un solo lugar. Diseñado para estudiantes y profesores.
                    </p>
                </div>
                
                <!-- Estadísticas -->
                <div class="grid grid-cols-3 gap-8 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div>
                        <div class="text-4xl font-bold text-white mb-2">15K+</div>
                        <div class="text-blue-100 text-sm">Estudiantes</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-white mb-2">850+</div>
                        <div class="text-blue-100 text-sm">Docentes</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-white mb-2">120+</div>
                        <div class="text-blue-100 text-sm">Programas</div>
                    </div>
                </div>
            </div>
            
            <!-- Footer izquierdo (abajo) -->
            <div class="relative z-10 text-blue-100 text-sm animate-fade-in-up" style="animation-delay: 0.3s;">
                <p>&copy; <?php echo date('Y'); ?> SISGENOT. Todos los derechos reservados.</p>
            </div>
        </div>
        
        <!-- Panel Derecho - Formulario -->
        <div class="w-full lg:w-[30%] flex items-center justify-center p-8 lg:p-16 bg-gray-50">
            <div class="w-full max-w-md">
                <!-- Logo móvil -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center space-x-3">
                        <div class="h-12 w-12 bg-indigo-600 rounded-lg p-2">
                            <img src="<?php echo APP_URL; ?>/public/assets/images/logo.png" alt="SISGENOT Logo" class="h-full w-full object-contain">
                        </div>
                        <div class="text-left">
                            <h1 class="text-xl font-bold text-gray-900">SISGENOT</h1>
                            <p class="text-gray-600 text-sm">Sistema de Gestión de Notas</p>
                        </div>
                    </div>
                </div>
                
                <!-- Título del formulario -->
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Iniciar Sesión</h2>
                    <p class="text-gray-600">Ingresa tus credenciales para continuar</p>
                </div>
                
                <!-- Formulario -->
                <form id="loginForm" action="<?php echo APP_URL; ?>/index.php?action=authenticate" method="POST" class="space-y-5">
                    
                    <!-- Campo Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Correo Institucional
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 ease-in-out"
                                placeholder="kevin@sisgenot.com"
                                autocomplete="email"
                            >
                        </div>
                    </div>

                    <!-- Campo Contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 ease-in-out"
                                placeholder="••••••••••"
                                autocomplete="current-password"
                            >
                            <button 
                                type="button" 
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg id="eyeIcon" class="h-5 w-5 text-gray-400 hover:text-gray-600 cursor-pointer transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Recordar sesión y Olvidaste contraseña -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                name="remember" 
                                type="checkbox" 
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                Recordar sesión
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500 transition">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <div>
                        <button 
                            type="submit" 
                            id="submitBtn"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl"
                        >
                            <span id="submitText">Acceder al Sistema</span>
                            <svg id="loadingIcon" class="hidden animate-spin ml-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                
                <!-- Separador -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-gray-50 text-gray-500">¿Necesitas ayuda?</span>
                    </div>
                </div>
                
                <!-- Enlaces de ayuda -->
                <div class="flex justify-center space-x-6 text-sm">
                    <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium transition">
                        Soporte Técnico
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium transition">
                        Primer Acceso
                    </a>
                </div>
                
                <!-- Versión -->
                <div class="mt-6 text-center text-xs text-gray-500">
                    Versión <?php echo APP_VERSION; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });

        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const loadingIcon = document.getElementById('loadingIcon');
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Iniciando sesión...';
            loadingIcon.classList.remove('hidden');
        });

        // Show flash messages
        <?php 
        $mensaje = SessionManager::obtener_mensaje();
        if ($mensaje): 
        ?>
        Swal.fire({
            icon: '<?php echo $mensaje['tipo'] === 'success' ? 'success' : 'error'; ?>',
            title: '<?php echo $mensaje['tipo'] === 'success' ? '¡Éxito!' : '¡Error!'; ?>',
            text: '<?php echo addslashes($mensaje['mensaje']); ?>',
            confirmButtonColor: '#4F46E5',
            confirmButtonText: 'Entendido'
        });
        <?php endif; ?>

        // Focus on email field
        document.getElementById('email').focus();
    </script>
</body>
</html>
