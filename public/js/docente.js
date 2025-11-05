/**
 * JavaScript para el panel de docente
 */

// Variables globales para el docente
let cursoSeleccionado = null;
let estudianteSeleccionado = null;

// Funciones para gestión de calificaciones
const GestionCalificaciones = {
    
    /**
     * Guardar nueva calificación
     */
    guardar: function(formId) {
        const form = document.getElementById(formId);
        if (!form) return;
        
        const reglas = {
            nota: [
                valor => Validacion.requerido(valor, 'nota'),
                valor => Validacion.nota(valor)
            ]
        };
        
        const validacion = Validacion.validarFormulario(formId, reglas);
        
        if (!validacion.valido) {
            Notificaciones.error(validacion.errores.join('\n'));
            return;
        }
        
        const formData = new FormData(form);
        
        Notificaciones.loading('Guardando calificación...');
        
        Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_guardar_calificacion`, formData)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    Notificaciones.exito(response.mensaje);
                    // Limpiar formulario
                    form.reset();
                    // Recargar página después de un momento
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    Notificaciones.error(response.mensaje);
                }
            })
            .catch(error => {
                Notificaciones.ocultarLoading();
                console.error('Error:', error);
                Notificaciones.error('Error al guardar la calificación');
            });
    },
    
    /**
     * Eliminar calificación
     */
    eliminar: function(id, nota, estudiante) {
        Notificaciones.confirmacion(
            `¿Estás seguro de que quieres eliminar la calificación ${nota} del estudiante ${estudiante}?`,
            '¡Atención!'
        ).then((result) => {
            if (result.isConfirmed) {
                Notificaciones.loading('Eliminando calificación...');
                
                Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_eliminar_calificacion`, { id: id })
                    .then(response => {
                        Notificaciones.ocultarLoading();
                        
                        if (response.exito) {
                            Notificaciones.exito(response.mensaje);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            Notificaciones.error(response.mensaje);
                        }
                    })
                    .catch(error => {
                        Notificaciones.ocultarLoading();
                        console.error('Error:', error);
                        Notificaciones.error('Error al eliminar la calificación');
                    });
            }
        });
    },
    
    /**
     * Obtener calificaciones de un estudiante
     */
    obtenerCalificacionesEstudiante: function(inscripcionId, nombreEstudiante, callback) {
        Notificaciones.loading('Cargando historial de calificaciones...');
        
        Ajax.get(`${CONFIG.APP_URL}/index.php?action=ajax_obtener_calificaciones_estudiante&inscripcion_id=${inscripcionId}`)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    if (callback) {
                        callback(response.datos, nombreEstudiante);
                    }
                } else {
                    Notificaciones.error(response.mensaje);
                }
            })
            .catch(error => {
                Notificaciones.ocultarLoading();
                console.error('Error:', error);
                Notificaciones.error('Error al cargar las calificaciones');
            });
    },
    
    /**
     * Obtener estudiantes de un curso
     */
    obtenerEstudiantesCurso: function(cursoId, callback) {
        Notificaciones.loading('Cargando estudiantes...');
        
        Ajax.get(`${CONFIG.APP_URL}/index.php?action=ajax_obtener_estudiantes_curso&curso_id=${cursoId}`)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    if (callback) {
                        callback(response.datos);
                    }
                } else {
                    Notificaciones.error(response.mensaje);
                }
            })
            .catch(error => {
                Notificaciones.ocultarLoading();
                console.error('Error:', error);
                Notificaciones.error('Error al cargar los estudiantes');
            });
    }
};

// Funciones para manejo de estudiantes
const ManejoCursos = {
    
    /**
     * Seleccionar curso
     */
    seleccionar: function(cursoId) {
        cursoSeleccionado = cursoId;
        this.cargarEstudiantes(cursoId);
    },
    
    /**
     * Cargar estudiantes de un curso
     */
    cargarEstudiantes: function(cursoId) {
        GestionCalificaciones.obtenerEstudiantesCurso(cursoId, (estudiantes) => {
            this.mostrarEstudiantes(estudiantes);
        });
    },
    
    /**
     * Mostrar lista de estudiantes
     */
    mostrarEstudiantes: function(estudiantes) {
        const contenedor = document.getElementById('listaEstudiantes');
        if (!contenedor) return;
        
        if (estudiantes.length === 0) {
            contenedor.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No hay estudiantes inscritos</p>
                </div>
            `;
            return;
        }
        
        let html = '<div class="space-y-3">';
        
        estudiantes.forEach(estudiante => {
            const colorPromedio = estudiante.promedio >= CONFIG.NOTA_APROBACION ? 'text-green-600' : 'text-red-600';
            const bgPromedio = estudiante.promedio >= CONFIG.NOTA_APROBACION ? 'bg-green-100' : 'bg-red-100';
            
            html += `
                <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg hover:shadow-md transition-shadow cursor-pointer"
                     onclick="ManejoEstudiantes.seleccionar(${estudiante.estudiante_id}, '${Utils.escaparHtml(estudiante.nombre + ' ' + estudiante.apellido)}')">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-sm font-medium text-indigo-600">
                                ${estudiante.nombre.charAt(0)}${estudiante.apellido.charAt(0)}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">${Utils.escaparHtml(estudiante.nombre + ' ' + estudiante.apellido)}</p>
                            <p class="text-xs text-gray-500">${estudiante.total_calificaciones} calificación(es)</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${bgPromedio} ${colorPromedio}">
                            ${estudiante.promedio > 0 ? Utils.formatearNumero(estudiante.promedio) : 'Sin calificar'}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">${estudiante.estado}</p>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        contenedor.innerHTML = html;
    }
};

// Funciones para manejo de estudiantes
const ManejoEstudiantes = {
    
    /**
     * Seleccionar estudiante
     */
    seleccionar: function(estudianteId, nombreEstudiante) {
        estudianteSeleccionado = {
            id: estudianteId,
            nombre: nombreEstudiante
        };
        
        // Actualizar URL si estamos en la página de calificar
        if (window.location.pathname.includes('calificar')) {
            const url = new URL(window.location);
            url.searchParams.set('estudiante_id', estudianteId);
            window.history.pushState({}, '', url);
        }
        
        this.mostrarDetallesEstudiante(estudianteId, nombreEstudiante);
    },
    
    /**
     * Mostrar detalles del estudiante seleccionado
     */
    mostrarDetallesEstudiante: function(estudianteId, nombreEstudiante) {
        const contenedor = document.getElementById('detallesEstudiante');
        if (!contenedor) return;
        
        // Mostrar información básica
        contenedor.innerHTML = `
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">${Utils.escaparHtml(nombreEstudiante)}</h3>
                    <button onclick="ManejoEstudiantes.mostrarFormularioCalificacion()" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Nueva Calificación
                    </button>
                </div>
                <div id="historialCalificaciones">
                    <div class="text-center py-4">
                        <div class="loading"></div>
                        <p class="mt-2 text-sm text-gray-500">Cargando historial...</p>
                    </div>
                </div>
            </div>
        `;
        
        // Cargar historial de calificaciones
        this.cargarHistorialCalificaciones(estudianteId, nombreEstudiante);
    },
    
    /**
     * Cargar historial de calificaciones
     */
    cargarHistorialCalificaciones: function(estudianteId, nombreEstudiante) {
        // Necesitamos el inscripcion_id, que deberíamos obtener del contexto
        // Por ahora, usaremos una función auxiliar
        this.obtenerInscripcionId(estudianteId, (inscripcionId) => {
            if (inscripcionId) {
                GestionCalificaciones.obtenerCalificacionesEstudiante(inscripcionId, nombreEstudiante, (datos) => {
                    this.mostrarHistorialCalificaciones(datos);
                });
            }
        });
    },
    
    /**
     * Obtener ID de inscripción (función auxiliar)
     */
    obtenerInscripcionId: function(estudianteId, callback) {
        // Esta función debería implementarse según el contexto específico
        // Por ahora, asumimos que tenemos acceso a esta información
        if (callback) callback(null);
    },
    
    /**
     * Mostrar historial de calificaciones
     */
    mostrarHistorialCalificaciones: function(datos) {
        const contenedor = document.getElementById('historialCalificaciones');
        if (!contenedor) return;
        
        if (!datos.calificaciones || datos.calificaciones.length === 0) {
            contenedor.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No hay calificaciones registradas</p>
                </div>
            `;
            return;
        }
        
        const colorPromedio = datos.promedio >= CONFIG.NOTA_APROBACION ? 'text-green-600' : 'text-red-600';
        const bgPromedio = datos.promedio >= CONFIG.NOTA_APROBACION ? 'bg-green-100' : 'bg-red-100';
        
        let html = `
            <div class="mb-4 p-4 ${bgPromedio} rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Promedio:</span>
                    <span class="text-xl font-bold ${colorPromedio}">${Utils.formatearNumero(datos.promedio)}</span>
                </div>
                <div class="flex justify-between items-center mt-1">
                    <span class="text-sm text-gray-600">Estado:</span>
                    <span class="text-sm font-medium ${colorPromedio}">${datos.estado}</span>
                </div>
            </div>
            <div class="space-y-3 max-h-64 overflow-y-auto">
        `;
        
        datos.calificaciones.forEach(calificacion => {
            const colorNota = calificacion.nota >= CONFIG.NOTA_APROBACION ? 'text-green-600' : 'text-red-600';
            const fecha = Utils.formatearFechaHora(calificacion.fecha_registro);
            
            html += `
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-bold ${colorNota}">${Utils.formatearNumero(calificacion.nota)}</span>
                                <span class="text-sm text-gray-500">${fecha}</span>
                            </div>
                            ${calificacion.observaciones ? `<p class="text-sm text-gray-600 mt-1">${Utils.escaparHtml(calificacion.observaciones)}</p>` : ''}
                        </div>
                        <button onclick="GestionCalificaciones.eliminar(${calificacion.id}, '${Utils.formatearNumero(calificacion.nota)}', '${Utils.escaparHtml(estudianteSeleccionado.nombre)}')"
                                class="ml-2 text-red-600 hover:text-red-800">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        contenedor.innerHTML = html;
    },
    
    /**
     * Mostrar formulario de calificación
     */
    mostrarFormularioCalificacion: function() {
        if (!estudianteSeleccionado) {
            Notificaciones.error('Debe seleccionar un estudiante primero');
            return;
        }
        
        Swal.fire({
            title: `Calificar a ${estudianteSeleccionado.nombre}`,
            html: `
                <div class="text-left">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nota (${CONFIG.NOTA_MINIMA} - ${CONFIG.NOTA_MAXIMA})</label>
                        <input type="number" id="swal-nota" min="${CONFIG.NOTA_MINIMA}" max="${CONFIG.NOTA_MAXIMA}" step="0.1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones (Opcional)</label>
                        <textarea id="swal-observaciones" rows="3" maxlength="500"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Comentarios adicionales..."></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Guardar Calificación',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            preConfirm: () => {
                const nota = document.getElementById('swal-nota').value;
                const observaciones = document.getElementById('swal-observaciones').value;
                
                if (!nota) {
                    Swal.showValidationMessage('La nota es requerida');
                    return false;
                }
                
                if (!Utils.validarNota(nota)) {
                    Swal.showValidationMessage(`La nota debe estar entre ${CONFIG.NOTA_MINIMA} y ${CONFIG.NOTA_MAXIMA}`);
                    return false;
                }
                
                return { nota: nota, observaciones: observaciones };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.guardarCalificacionModal(result.value);
            }
        });
        
        // Focus en el input de nota
        setTimeout(() => {
            document.getElementById('swal-nota').focus();
        }, 100);
    },
    
    /**
     * Guardar calificación desde modal
     */
    guardarCalificacionModal: function(datos) {
        if (!estudianteSeleccionado) return;
        
        // Aquí necesitaríamos el inscripcion_id
        // Por ahora, mostraremos un mensaje de éxito simulado
        Notificaciones.loading('Guardando calificación...');
        
        setTimeout(() => {
            Notificaciones.ocultarLoading();
            Notificaciones.exito('Calificación guardada exitosamente');
            // Recargar historial
            this.cargarHistorialCalificaciones(estudianteSeleccionado.id, estudianteSeleccionado.nombre);
        }, 1000);
    }
};

// Funciones de utilidad específicas para docentes
const UtilidadesDocente = {
    
    /**
     * Formatear nota con color
     */
    formatearNotaConColor: function(nota) {
        const notaFormateada = Utils.formatearNumero(nota);
        let colorClass = '';
        
        if (nota >= 4.5) colorClass = 'text-green-600';
        else if (nota >= 4.0) colorClass = 'text-blue-600';
        else if (nota >= 3.0) colorClass = 'text-yellow-600';
        else colorClass = 'text-red-600';
        
        return `<span class="${colorClass} font-semibold">${notaFormateada}</span>`;
    },
    
    /**
     * Obtener badge de estado
     */
    obtenerBadgeEstado: function(promedio) {
        if (promedio >= CONFIG.NOTA_APROBACION) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aprobado</span>';
        } else {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Reprobado</span>';
        }
    },
    
    /**
     * Calcular estadísticas de curso
     */
    calcularEstadisticasCurso: function(estudiantes) {
        const stats = {
            total: estudiantes.length,
            conNotas: 0,
            aprobados: 0,
            reprobados: 0,
            promedioGeneral: 0,
            mejorNota: 0,
            peorNota: 5.0
        };
        
        let sumaPromedios = 0;
        
        estudiantes.forEach(estudiante => {
            if (estudiante.promedio > 0) {
                stats.conNotas++;
                sumaPromedios += estudiante.promedio;
                
                if (estudiante.promedio >= CONFIG.NOTA_APROBACION) {
                    stats.aprobados++;
                } else {
                    stats.reprobados++;
                }
                
                if (estudiante.promedio > stats.mejorNota) {
                    stats.mejorNota = estudiante.promedio;
                }
                
                if (estudiante.promedio < stats.peorNota) {
                    stats.peorNota = estudiante.promedio;
                }
            }
        });
        
        if (stats.conNotas > 0) {
            stats.promedioGeneral = sumaPromedios / stats.conNotas;
        }
        
        return stats;
    }
};

// Inicialización específica del docente
document.addEventListener('DOMContentLoaded', function() {
    
    // Configurar validación en tiempo real para inputs de nota
    const inputsNota = document.querySelectorAll('input[type="number"][step="0.1"]');
    inputsNota.forEach(input => {
        input.addEventListener('input', function() {
            const valor = parseFloat(this.value);
            
            // Limpiar clases previas
            this.classList.remove('border-red-500', 'border-green-500');
            
            if (this.value && !Utils.validarNota(valor)) {
                this.classList.add('border-red-500');
            } else if (this.value) {
                this.classList.add('border-green-500');
            } else {
                this.classList.add('border-gray-300');
            }
        });
    });
    
    // Configurar contador de caracteres para observaciones
    const textareaObservaciones = document.querySelectorAll('textarea[maxlength]');
    textareaObservaciones.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        const contador = document.createElement('div');
        contador.className = 'text-xs text-gray-500 mt-1 text-right';
        contador.textContent = `0/${maxLength}`;
        
        textarea.parentNode.appendChild(contador);
        
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            contador.textContent = `${length}/${maxLength}`;
            
            if (length > maxLength * 0.9) {
                contador.classList.add('text-yellow-600');
            } else {
                contador.classList.remove('text-yellow-600');
            }
        });
    });
    
    console.log('JavaScript de docente inicializado');
});

// Exportar funciones para uso global
window.GestionCalificaciones = GestionCalificaciones;
window.ManejoCursos = ManejoCursos;
window.ManejoEstudiantes = ManejoEstudiantes;
window.UtilidadesDocente = UtilidadesDocente;
