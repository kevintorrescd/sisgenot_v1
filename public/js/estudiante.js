/**
 * JavaScript para el panel de estudiante
 */

// Variables globales para el estudiante
let semestreSeleccionado = null;
let estadisticasGenerales = null;

// Funciones para visualización de notas
const VisualizacionNotas = {
    
    /**
     * Mostrar detalles de un curso
     */
    mostrarDetallesCurso: function(inscripcionId, nombreCurso) {
        Notificaciones.loading('Cargando detalles del curso...');
        
        Ajax.get(`${CONFIG.APP_URL}/index.php?action=ajax_obtener_notas_curso&inscripcion_id=${inscripcionId}`)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    this.mostrarModalDetallesCurso(response.datos, nombreCurso);
                } else {
                    Notificaciones.error(response.mensaje);
                }
            })
            .catch(error => {
                Notificaciones.ocultarLoading();
                console.error('Error:', error);
                Notificaciones.error('Error al cargar los detalles del curso');
            });
    },
    
    /**
     * Mostrar modal con detalles del curso
     */
    mostrarModalDetallesCurso: function(datos, nombreCurso) {
        const colorPromedio = datos.promedio >= CONFIG.NOTA_APROBACION ? 'text-green-600' : 'text-red-600';
        const bgPromedio = datos.promedio >= CONFIG.NOTA_APROBACION ? 'bg-green-100' : 'bg-red-100';
        
        let htmlCalificaciones = '';
        
        if (datos.calificaciones && datos.calificaciones.length > 0) {
            htmlCalificaciones = '<div class="space-y-3 max-h-64 overflow-y-auto">';
            
            datos.calificaciones.forEach(calificacion => {
                const colorNota = calificacion.nota >= CONFIG.NOTA_APROBACION ? 'text-green-600' : 'text-red-600';
                const fecha = Utils.formatearFechaHora(calificacion.fecha_registro);
                
                htmlCalificaciones += `
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xl font-bold ${colorNota}">${Utils.formatearNumero(calificacion.nota)}</span>
                            <span class="text-sm text-gray-500">${fecha}</span>
                        </div>
                        ${calificacion.observaciones ? `<p class="text-sm text-gray-600">${Utils.escaparHtml(calificacion.observaciones)}</p>` : ''}
                    </div>
                `;
            });
            
            htmlCalificaciones += '</div>';
        } else {
            htmlCalificaciones = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No hay calificaciones registradas</p>
                </div>
            `;
        }
        
        Swal.fire({
            title: `Detalles de ${nombreCurso}`,
            html: `
                <div class="text-left">
                    <!-- Resumen del curso -->
                    <div class="mb-6 p-4 ${bgPromedio} rounded-lg">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold ${colorPromedio}">${Utils.formatearNumero(datos.promedio)}</p>
                                <p class="text-sm text-gray-600">Promedio</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-700">${datos.total_calificaciones}</p>
                                <p class="text-sm text-gray-600">Calificaciones</p>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${bgPromedio} ${colorPromedio}">
                                ${datos.estado}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Estadísticas adicionales -->
                    ${datos.total_calificaciones > 0 ? `
                    <div class="mb-6 grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-lg font-semibold text-green-600">${Utils.formatearNumero(datos.nota_maxima)}</p>
                            <p class="text-xs text-gray-600">Mejor Nota</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-lg font-semibold text-red-600">${Utils.formatearNumero(datos.nota_minima)}</p>
                            <p class="text-xs text-gray-600">Peor Nota</p>
                        </div>
                    </div>
                    ` : ''}
                    
                    <!-- Barra de progreso -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Progreso</span>
                            <span class="text-sm font-bold ${colorPromedio}">${Utils.formatearNumero(datos.promedio)} / ${CONFIG.NOTA_MAXIMA}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full ${this.obtenerColorBarraProgreso(datos.promedio)}" 
                                 style="width: ${datos.porcentaje_progreso}%"></div>
                        </div>
                    </div>
                    
                    <!-- Lista de calificaciones -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-3">Historial de Calificaciones</h4>
                        ${htmlCalificaciones}
                    </div>
                </div>
            `,
            width: '600px',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                popup: 'text-left'
            }
        });
    },
    
    /**
     * Obtener color para barra de progreso
     */
    obtenerColorBarraProgreso: function(promedio) {
        if (promedio >= 4.5) return 'bg-green-500';
        if (promedio >= 4.0) return 'bg-blue-500';
        if (promedio >= 3.0) return 'bg-yellow-500';
        return 'bg-red-500';
    },
    
    /**
     * Generar gráfico de progreso (usando Chart.js si está disponible)
     */
    generarGraficoProgreso: function(datos, contenedorId) {
        const contenedor = document.getElementById(contenedorId);
        if (!contenedor) return;
        
        // Si Chart.js está disponible, crear gráfico
        if (typeof Chart !== 'undefined') {
            this.crearGraficoConChartJS(datos, contenedorId);
        } else {
            // Fallback: crear representación visual simple
            this.crearGraficoSimple(datos, contenedor);
        }
    },
    
    /**
     * Crear gráfico simple sin librerías externas
     */
    crearGraficoSimple: function(datos, contenedor) {
        if (!datos.calificaciones || datos.calificaciones.length === 0) {
            contenedor.innerHTML = '<p class="text-center text-gray-500">No hay datos para mostrar</p>';
            return;
        }
        
        let html = '<div class="space-y-2">';
        
        datos.calificaciones.forEach((calificacion, index) => {
            const porcentaje = (calificacion.nota / CONFIG.NOTA_MAXIMA) * 100;
            const color = this.obtenerColorBarraProgreso(calificacion.nota);
            const fecha = Utils.formatearFecha(calificacion.fecha_registro);
            
            html += `
                <div class="flex items-center space-x-3">
                    <span class="text-xs text-gray-500 w-16">${fecha}</span>
                    <div class="flex-1 bg-gray-200 rounded-full h-4 relative">
                        <div class="${color} h-4 rounded-full" style="width: ${porcentaje}%"></div>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-medium text-white">
                            ${Utils.formatearNumero(calificacion.nota)}
                        </span>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        contenedor.innerHTML = html;
    }
};

// Funciones para estadísticas del estudiante
const EstadisticasEstudiante = {
    
    /**
     * Cargar estadísticas generales
     */
    cargar: function(semestreId = null) {
        const params = semestreId ? { semestre_id: semestreId } : {};
        
        Ajax.get(`${CONFIG.APP_URL}/index.php?action=ajax_obtener_estadisticas_estudiante`, params)
            .then(response => {
                if (response.exito) {
                    estadisticasGenerales = response.datos;
                    this.mostrar(response.datos);
                } else {
                    console.error('Error al cargar estadísticas:', response.mensaje);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    },
    
    /**
     * Mostrar estadísticas en el dashboard
     */
    mostrar: function(stats) {
        this.actualizarTarjetasEstadisticas(stats);
        this.actualizarGraficoDistribucion(stats);
        this.actualizarResumenRendimiento(stats);
    },
    
    /**
     * Actualizar tarjetas de estadísticas
     */
    actualizarTarjetasEstadisticas: function(stats) {
        const elementos = {
            'total-cursos': stats.total_cursos,
            'cursos-aprobados': stats.cursos_aprobados,
            'cursos-reprobados': stats.cursos_reprobados,
            'total-calificaciones': stats.total_calificaciones,
            'promedio-general': Utils.formatearNumero(stats.promedio_general),
            'mejor-nota': Utils.formatearNumero(stats.mejor_nota),
            'peor-nota': Utils.formatearNumero(stats.peor_nota)
        };
        
        Object.keys(elementos).forEach(id => {
            const elemento = document.getElementById(id);
            if (elemento) {
                elemento.textContent = elementos[id];
            }
        });
    },
    
    /**
     * Actualizar gráfico de distribución de notas
     */
    actualizarGraficoDistribucion: function(stats) {
        const contenedor = document.getElementById('graficoDistribucion');
        if (!contenedor || !stats.distribucion_notas) return;
        
        const distribucion = stats.distribucion_notas;
        const total = distribucion.excelente + distribucion.bueno + distribucion.aceptable + distribucion.deficiente;
        
        if (total === 0) {
            contenedor.innerHTML = '<p class="text-center text-gray-500">No hay calificaciones para mostrar</p>';
            return;
        }
        
        const datos = [
            { label: 'Excelente (4.5-5.0)', valor: distribucion.excelente, color: 'bg-green-500' },
            { label: 'Bueno (4.0-4.4)', valor: distribucion.bueno, color: 'bg-blue-500' },
            { label: 'Aceptable (3.0-3.9)', valor: distribucion.aceptable, color: 'bg-yellow-500' },
            { label: 'Deficiente (1.0-2.9)', valor: distribucion.deficiente, color: 'bg-red-500' }
        ];
        
        let html = '<div class="space-y-3">';
        
        datos.forEach(item => {
            const porcentaje = total > 0 ? (item.valor / total) * 100 : 0;
            
            html += `
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 ${item.color} rounded"></div>
                        <span class="text-sm text-gray-700">${item.label}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium">${item.valor}</span>
                        <span class="text-xs text-gray-500">(${porcentaje.toFixed(1)}%)</span>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="${item.color} h-2 rounded-full" style="width: ${porcentaje}%"></div>
                </div>
            `;
        });
        
        html += '</div>';
        contenedor.innerHTML = html;
    },
    
    /**
     * Actualizar resumen de rendimiento
     */
    actualizarResumenRendimiento: function(stats) {
        const contenedor = document.getElementById('resumenRendimiento');
        if (!contenedor) return;
        
        const porcentajeAprobacion = stats.total_cursos > 0 ? (stats.cursos_aprobados / stats.total_cursos) * 100 : 0;
        const colorRendimiento = porcentajeAprobacion >= 70 ? 'text-green-600' : porcentajeAprobacion >= 50 ? 'text-yellow-600' : 'text-red-600';
        
        let mensaje = '';
        if (porcentajeAprobacion >= 90) mensaje = '¡Excelente rendimiento académico!';
        else if (porcentajeAprobacion >= 70) mensaje = 'Buen rendimiento académico';
        else if (porcentajeAprobacion >= 50) mensaje = 'Rendimiento académico regular';
        else mensaje = 'Es necesario mejorar el rendimiento';
        
        contenedor.innerHTML = `
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold ${colorRendimiento}">${porcentajeAprobacion.toFixed(1)}%</p>
                <p class="text-sm text-gray-600">Tasa de Aprobación</p>
                <p class="text-xs ${colorRendimiento} mt-2 font-medium">${mensaje}</p>
            </div>
        `;
    }
};

// Funciones para manejo de semestres
const ManejoSemestres = {
    
    /**
     * Cambiar semestre seleccionado
     */
    cambiar: function(semestreId) {
        semestreSeleccionado = semestreId;
        
        // Actualizar URL
        const url = new URL(window.location);
        if (semestreId) {
            url.searchParams.set('semestre_id', semestreId);
        } else {
            url.searchParams.delete('semestre_id');
        }
        window.history.pushState({}, '', url);
        
        // Recargar página o actualizar contenido
        window.location.reload();
    },
    
    /**
     * Obtener semestre seleccionado desde URL
     */
    obtenerSeleccionado: function() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('semestre_id');
    }
};

// Funciones de utilidad específicas para estudiantes
const UtilidadesEstudiante = {
    
    /**
     * Formatear estado de aprobación
     */
    formatearEstadoAprobacion: function(promedio) {
        if (promedio >= CONFIG.NOTA_APROBACION) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aprobado</span>';
        } else if (promedio > 0) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Reprobado</span>';
        } else {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Sin calificar</span>';
        }
    },
    
    /**
     * Calcular porcentaje de progreso
     */
    calcularPorcentajeProgreso: function(promedio) {
        return Math.min(100, (promedio / CONFIG.NOTA_MAXIMA) * 100);
    },
    
    /**
     * Obtener mensaje motivacional
     */
    obtenerMensajeMotivacional: function(promedio) {
        if (promedio >= 4.5) return '¡Excelente trabajo! Sigue así.';
        if (promedio >= 4.0) return '¡Muy bien! Estás haciendo un gran trabajo.';
        if (promedio >= 3.5) return 'Buen trabajo. Puedes mejorar aún más.';
        if (promedio >= 3.0) return 'Estás aprobando. ¡Sigue esforzándote!';
        if (promedio > 0) return 'Necesitas mejorar. ¡Tú puedes lograrlo!';
        return 'Aún no tienes calificaciones registradas.';
    },
    
    /**
     * Generar reporte de progreso
     */
    generarReporteProgreso: function() {
        if (!estadisticasGenerales) {
            Notificaciones.error('No hay datos estadísticos disponibles');
            return;
        }
        
        const stats = estadisticasGenerales;
        const fecha = new Date().toLocaleDateString('es-ES');
        
        const reporte = `
            REPORTE DE PROGRESO ACADÉMICO
            Fecha: ${fecha}
            
            RESUMEN GENERAL:
            - Total de cursos: ${stats.total_cursos}
            - Cursos aprobados: ${stats.cursos_aprobados}
            - Cursos reprobados: ${stats.cursos_reprobados}
            - Total de calificaciones: ${stats.total_calificaciones}
            - Promedio general: ${Utils.formatearNumero(stats.promedio_general)}
            - Mejor nota: ${Utils.formatearNumero(stats.mejor_nota)}
            - Peor nota: ${Utils.formatearNumero(stats.peor_nota)}
            
            DISTRIBUCIÓN DE NOTAS:
            - Excelente (4.5-5.0): ${stats.distribucion_notas.excelente}
            - Bueno (4.0-4.4): ${stats.distribucion_notas.bueno}
            - Aceptable (3.0-3.9): ${stats.distribucion_notas.aceptable}
            - Deficiente (1.0-2.9): ${stats.distribucion_notas.deficiente}
            
            TASA DE APROBACIÓN: ${stats.total_cursos > 0 ? ((stats.cursos_aprobados / stats.total_cursos) * 100).toFixed(1) : 0}%
        `;
        
        // Mostrar en modal o descargar como archivo
        Swal.fire({
            title: 'Reporte de Progreso',
            html: `<pre class="text-left text-sm bg-gray-100 p-4 rounded overflow-auto max-h-64">${reporte}</pre>`,
            showCloseButton: true,
            showConfirmButton: false,
            width: '600px'
        });
    }
};

// Inicialización específica del estudiante
document.addEventListener('DOMContentLoaded', function() {
    
    // Cargar estadísticas si estamos en el dashboard
    if (window.location.pathname.includes('dashboard')) {
        EstadisticasEstudiante.cargar();
    }
    
    // Configurar selector de semestre
    const selectorSemestre = document.getElementById('semestreSelector');
    if (selectorSemestre) {
        selectorSemestre.addEventListener('change', function() {
            ManejoSemestres.cambiar(this.value);
        });
    }
    
    // Configurar animaciones de entrada para las tarjetas
    const tarjetas = document.querySelectorAll('.bg-white.rounded-lg.shadow');
    tarjetas.forEach((tarjeta, index) => {
        tarjeta.style.opacity = '0';
        tarjeta.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            tarjeta.style.transition = 'all 0.5s ease-out';
            tarjeta.style.opacity = '1';
            tarjeta.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Configurar tooltips para las notas
    const elementosNota = document.querySelectorAll('[data-nota]');
    elementosNota.forEach(elemento => {
        const nota = parseFloat(elemento.dataset.nota);
        let mensaje = UtilidadesEstudiante.obtenerMensajeMotivacional(nota);
        
        elemento.title = mensaje;
        elemento.style.cursor = 'help';
    });
    
    console.log('JavaScript de estudiante inicializado');
});

// Exportar funciones para uso global
window.VisualizacionNotas = VisualizacionNotas;
window.EstadisticasEstudiante = EstadisticasEstudiante;
window.ManejoSemestres = ManejoSemestres;
window.UtilidadesEstudiante = UtilidadesEstudiante;
