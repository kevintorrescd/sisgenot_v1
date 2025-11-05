/**
 * JavaScript principal para SISGENOT
 */

// Configuración global
const CONFIG = {
    APP_URL: (function() {
        // Detectar automáticamente la URL base
        const path = window.location.pathname;
        const segments = path.split('/').filter(s => s);
        
        // Si estamos en una subcarpeta, tomar hasta el primer segmento
        // Ejemplo: /sisgenot/index.php -> /sisgenot
        // Ejemplo: /index.php -> ''
        if (segments.length > 0 && segments[0] !== 'index.php') {
            return window.location.origin + '/' + segments[0];
        }
        
        return window.location.origin;
    })(),
    NOTA_MINIMA: 1.0,
    NOTA_MAXIMA: 5.0,
    NOTA_APROBACION: 3.0
};

// Utilidades globales
const Utils = {
    
    /**
     * Formatear número con decimales
     */
    formatearNumero: function(numero, decimales = 1) {
        return parseFloat(numero).toFixed(decimales);
    },
    
    /**
     * Validar email
     */
    validarEmail: function(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    },
    
    /**
     * Validar nota
     */
    validarNota: function(nota) {
        const num = parseFloat(nota);
        return !isNaN(num) && num >= CONFIG.NOTA_MINIMA && num <= CONFIG.NOTA_MAXIMA;
    },
    
    /**
     * Limpiar formulario
     */
    limpiarFormulario: function(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.reset();
            // Limpiar clases de error
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.classList.remove('border-red-500', 'border-green-500');
                input.classList.add('border-gray-300');
            });
        }
    },
    
    /**
     * Mostrar/ocultar elemento
     */
    toggleElemento: function(elementId, mostrar = null) {
        const elemento = document.getElementById(elementId);
        if (elemento) {
            if (mostrar === null) {
                elemento.classList.toggle('hidden');
            } else {
                if (mostrar) {
                    elemento.classList.remove('hidden');
                } else {
                    elemento.classList.add('hidden');
                }
            }
        }
    },
    
    /**
     * Escapar HTML
     */
    escaparHtml: function(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    },
    
    /**
     * Debounce function
     */
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    /**
     * Formatear fecha
     */
    formatearFecha: function(fecha) {
        if (!fecha) return '';
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES');
    },
    
    /**
     * Formatear fecha y hora
     */
    formatearFechaHora: function(fechaHora) {
        if (!fechaHora) return '';
        const date = new Date(fechaHora);
        return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
};

// Manejo de notificaciones con SweetAlert2
const Notificaciones = {
    
    /**
     * Mostrar notificación de éxito
     */
    exito: function(mensaje, titulo = '¡Éxito!', timer = 3000) {
        return Swal.fire({
            icon: 'success',
            title: titulo,
            text: mensaje,
            confirmButtonColor: '#4F46E5',
            timer: timer,
            timerProgressBar: timer > 0,
            showConfirmButton: timer === 0
        });
    },
    
    /**
     * Mostrar notificación de error
     */
    error: function(mensaje, titulo = '¡Error!') {
        return Swal.fire({
            icon: 'error',
            title: titulo,
            text: mensaje,
            confirmButtonColor: '#4F46E5',
            confirmButtonText: 'Entendido'
        });
    },
    
    /**
     * Mostrar notificación de advertencia
     */
    advertencia: function(mensaje, titulo = '¡Atención!') {
        return Swal.fire({
            icon: 'warning',
            title: titulo,
            text: mensaje,
            confirmButtonColor: '#4F46E5',
            confirmButtonText: 'Entendido'
        });
    },
    
    /**
     * Mostrar notificación de información
     */
    info: function(mensaje, titulo = 'Información') {
        return Swal.fire({
            icon: 'info',
            title: titulo,
            text: mensaje,
            confirmButtonColor: '#4F46E5',
            confirmButtonText: 'Entendido'
        });
    },
    
    /**
     * Mostrar confirmación
     */
    confirmacion: function(mensaje, titulo = '¿Estás seguro?') {
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
    },
    
    /**
     * Mostrar loading
     */
    loading: function(mensaje = 'Cargando...') {
        Swal.fire({
            title: mensaje,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },
    
    /**
     * Ocultar loading
     */
    ocultarLoading: function() {
        Swal.close();
    }
};

// Manejo de AJAX
const Ajax = {
    
    /**
     * Realizar petición GET
     */
    get: function(url, params = {}) {
        const urlObj = new URL(url, window.location.origin);
        Object.keys(params).forEach(key => {
            urlObj.searchParams.append(key, params[key]);
        });
        
        return fetch(urlObj.toString(), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        });
    },
    
    /**
     * Realizar petición POST
     */
    post: function(url, data = {}) {
        const formData = new FormData();
        
        // Si data es FormData, usarlo directamente
        if (data instanceof FormData) {
            return fetch(url, {
                method: 'POST',
                body: data,
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            });
        }
        
        // Si es objeto, convertir a FormData
        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });
        
        return fetch(url, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        });
    }
};

// Validación de formularios
const Validacion = {
    
    /**
     * Validar campo requerido
     */
    requerido: function(valor, nombreCampo) {
        if (!valor || valor.trim() === '') {
            return `El campo ${nombreCampo} es requerido`;
        }
        return null;
    },
    
    /**
     * Validar email
     */
    email: function(valor) {
        if (valor && !Utils.validarEmail(valor)) {
            return 'El formato del email es inválido';
        }
        return null;
    },
    
    /**
     * Validar longitud mínima
     */
    longitudMinima: function(valor, minimo, nombreCampo) {
        if (valor && valor.length < minimo) {
            return `${nombreCampo} debe tener al menos ${minimo} caracteres`;
        }
        return null;
    },
    
    /**
     * Validar longitud máxima
     */
    longitudMaxima: function(valor, maximo, nombreCampo) {
        if (valor && valor.length > maximo) {
            return `${nombreCampo} no puede tener más de ${maximo} caracteres`;
        }
        return null;
    },
    
    /**
     * Validar nota
     */
    nota: function(valor) {
        if (valor !== '' && !Utils.validarNota(valor)) {
            return `La nota debe estar entre ${CONFIG.NOTA_MINIMA} y ${CONFIG.NOTA_MAXIMA}`;
        }
        return null;
    },
    
    /**
     * Validar formulario completo
     */
    validarFormulario: function(formId, reglas) {
        const form = document.getElementById(formId);
        if (!form) return { valido: false, errores: ['Formulario no encontrado'] };
        
        const errores = [];
        let valido = true;
        
        Object.keys(reglas).forEach(campo => {
            const input = form.querySelector(`[name="${campo}"]`);
            if (!input) return;
            
            const valor = input.value;
            const reglasDelCampo = reglas[campo];
            
            // Limpiar estilos previos
            input.classList.remove('border-red-500', 'border-green-500');
            input.classList.add('border-gray-300');
            
            reglasDelCampo.forEach(regla => {
                const error = regla(valor);
                if (error) {
                    errores.push(error);
                    valido = false;
                    input.classList.remove('border-gray-300');
                    input.classList.add('border-red-500');
                }
            });
            
            // Si no hay errores, marcar como válido
            if (valido && input.classList.contains('border-gray-300')) {
                input.classList.remove('border-gray-300');
                input.classList.add('border-green-500');
            }
        });
        
        return { valido, errores };
    }
};

// Manejo de modales
const Modal = {
    
    /**
     * Abrir modal
     */
    abrir: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Focus en el primer input
            const primerInput = modal.querySelector('input, select, textarea');
            if (primerInput) {
                setTimeout(() => primerInput.focus(), 100);
            }
        }
    },
    
    /**
     * Cerrar modal
     */
    cerrar: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    },
    
    /**
     * Configurar modal para cerrar al hacer clic fuera
     */
    configurarCierreExterno: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    Modal.cerrar(modalId);
                }
            });
        }
    }
};

// Manejo de tablas
const Tabla = {
    
    /**
     * Filtrar tabla por texto
     */
    filtrar: function(tablaId, texto) {
        const tabla = document.getElementById(tablaId);
        if (!tabla) return;
        
        const filas = tabla.querySelectorAll('tbody tr');
        const filtro = texto.toLowerCase();
        
        filas.forEach(fila => {
            const textoFila = fila.textContent.toLowerCase();
            if (textoFila.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    },
    
    /**
     * Ordenar tabla por columna
     */
    ordenar: function(tablaId, columna, direccion = 'asc') {
        const tabla = document.getElementById(tablaId);
        if (!tabla) return;
        
        const tbody = tabla.querySelector('tbody');
        const filas = Array.from(tbody.querySelectorAll('tr'));
        
        filas.sort((a, b) => {
            const valorA = a.cells[columna].textContent.trim();
            const valorB = b.cells[columna].textContent.trim();
            
            // Intentar comparar como números
            const numA = parseFloat(valorA);
            const numB = parseFloat(valorB);
            
            if (!isNaN(numA) && !isNaN(numB)) {
                return direccion === 'asc' ? numA - numB : numB - numA;
            }
            
            // Comparar como texto
            return direccion === 'asc' 
                ? valorA.localeCompare(valorB)
                : valorB.localeCompare(valorA);
        });
        
        // Reordenar filas en el DOM
        filas.forEach(fila => tbody.appendChild(fila));
    }
};

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    
    // Configurar tooltips (si se usa alguna librería)
    // Configurar dropdowns
    // Configurar modales para cerrar con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Cerrar todos los modales visibles
            const modalesAbiertos = document.querySelectorAll('.modal:not(.hidden)');
            modalesAbiertos.forEach(modal => {
                modal.classList.add('hidden');
            });
            document.body.classList.remove('overflow-hidden');
        }
    });
    
    // Configurar formularios para prevenir envío doble
    const formularios = document.querySelectorAll('form');
    formularios.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                setTimeout(() => {
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    });
    
    // Configurar inputs numéricos para notas
    const inputsNota = document.querySelectorAll('input[type="number"][step="0.1"]');
    inputsNota.forEach(input => {
        input.addEventListener('input', function() {
            const valor = parseFloat(this.value);
            if (!isNaN(valor)) {
                if (valor < CONFIG.NOTA_MINIMA) this.value = CONFIG.NOTA_MINIMA;
                if (valor > CONFIG.NOTA_MAXIMA) this.value = CONFIG.NOTA_MAXIMA;
            }
        });
    });
    
    console.log('SiSGENOT JavaScript inicializado correctamente');
});

// Exportar para uso global
window.Utils = Utils;
window.Notificaciones = Notificaciones;
window.Ajax = Ajax;
window.Validacion = Validacion;
window.Modal = Modal;
window.Tabla = Tabla;
window.CONFIG = CONFIG;
