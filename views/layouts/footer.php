    </div> <!-- Cierre del main content container -->

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                
                <!-- Copyright -->
                <div class="text-sm text-gray-500">
                    <p>&copy; <?php echo date('Y'); ?> SISGENOT - Sistema de Gestión de Notas Académicas. Todos los derechos reservados.</p>
                </div>

                <!-- Links y versión -->
                <div class="flex items-center space-x-4 mt-2 md:mt-0">
                    <span class="text-xs text-gray-400">Versión <?php echo APP_VERSION; ?></span>
                    
                    <!-- Enlaces útiles -->
                    <div class="flex items-center space-x-2 text-xs">
                        <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors duration-150">
                            Ayuda
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors duration-150">
                            Soporte
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors duration-150">
                            Términos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información adicional del sistema -->
            <div class="mt-2 pt-2 border-t border-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-gray-400">
                    <div>
                        <span>Servidor: <?php echo $_SERVER['HTTP_HOST']; ?></span>
                        <span class="mx-2">•</span>
                        <span>PHP: <?php echo PHP_VERSION; ?></span>
                    </div>
                    <div class="mt-1 sm:mt-0">
                        <span>Última actualización: <?php echo date('d/m/Y H:i'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts globales -->
    <script src="<?php echo APP_URL; ?>/public/js/main.js?v=<?php echo APP_VERSION; ?>"></script>
    
    <?php
    // Incluir scripts específicos según el rol
    $usuario_actual = SessionManager::obtener_usuario();
    $rol_usuario = $usuario_actual['rol'];
    
    switch ($rol_usuario) {
        case 'admin':
            echo '<script src="' . APP_URL . '/public/js/admin.js?v=' . APP_VERSION . '"></script>';
            break;
        case 'docente':
            echo '<script src="' . APP_URL . '/public/js/docente.js?v=' . APP_VERSION . '"></script>';
            break;
        case 'estudiante':
            echo '<script src="' . APP_URL . '/public/js/estudiante.js?v=' . APP_VERSION . '"></script>';
            break;
    }
    ?>

    <!-- Script para mantener la sesión activa -->
    <script>
        // Verificar sesión cada 5 minutos
        setInterval(function() {
            fetch('<?php echo APP_URL; ?>/index.php?action=verificar_sesion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (!data.exito) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sesión Expirada',
                        text: 'Tu sesión ha expirado. Serás redirigido al login.',
                        confirmButtonColor: '#4F46E5',
                        confirmButtonText: 'Entendido'
                    }).then(() => {
                        window.location.href = '<?php echo APP_URL; ?>/index.php?action=login';
                    });
                }
            })
            .catch(error => {
                console.log('Error verificando sesión:', error);
            });
        }, 300000); // 5 minutos

        // Mostrar confirmación antes de cerrar la página si hay cambios sin guardar
        let cambiosSinGuardar = false;
        
        window.addEventListener('beforeunload', function(e) {
            if (cambiosSinGuardar) {
                e.preventDefault();
                e.returnValue = '¿Estás seguro de que quieres salir? Los cambios no guardados se perderán.';
                return e.returnValue;
            }
        });

        // Función global para marcar cambios sin guardar
        window.marcarCambiosSinGuardar = function(estado = true) {
            cambiosSinGuardar = estado;
        };

        // Función global para mostrar loading
        window.mostrarLoading = function(mensaje = 'Cargando...') {
            Swal.fire({
                title: mensaje,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        };

        // Función global para ocultar loading
        window.ocultarLoading = function() {
            Swal.close();
        };

        // Función global para mostrar notificación de éxito
        window.mostrarExito = function(mensaje, titulo = '¡Éxito!') {
            Swal.fire({
                icon: 'success',
                title: titulo,
                text: mensaje,
                confirmButtonColor: '#4F46E5',
                timer: 3000,
                timerProgressBar: true
            });
        };

        // Función global para mostrar notificación de error
        window.mostrarError = function(mensaje, titulo = '¡Error!') {
            Swal.fire({
                icon: 'error',
                title: titulo,
                text: mensaje,
                confirmButtonColor: '#4F46E5'
            });
        };

        // Función global para mostrar confirmación
        window.mostrarConfirmacion = function(mensaje, titulo = '¿Estás seguro?') {
            return Swal.fire({
                icon: 'question',
                title: titulo,
                text: mensaje,
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            });
        };

        // Función global para formatear números
        window.formatearNumero = function(numero, decimales = 1) {
            return parseFloat(numero).toFixed(decimales);
        };

        // Función global para validar email
        window.validarEmail = function(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        };

        // Función global para validar nota
        window.validarNota = function(nota) {
            const num = parseFloat(nota);
            return !isNaN(num) && num >= 1.0 && num <= 5.0;
        };
    </script>

</body>
</html>
