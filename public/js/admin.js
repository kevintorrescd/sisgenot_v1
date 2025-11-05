/**
 * JavaScript para el panel de administrador
 */

// Variables globales para el admin
let tablaActual = null;
let modalActual = null;
let editandoId = null;

// Funciones para gestión de usuarios
const GestionUsuarios = {
    
    /**
     * Abrir modal para crear usuario
     */
    abrirModalCrear: function() {
        editandoId = null;
        Utils.limpiarFormulario('usuarioForm');
        document.getElementById('modalTitulo').textContent = 'Crear Usuario';
        document.getElementById('passwordGroup').style.display = 'block';
        document.getElementById('passwordInput').required = true;
        Modal.abrir('usuarioModal');
    },
    
    /**
     * Abrir modal para editar usuario
     */
    abrirModalEditar: function(id) {
        editandoId = id;
        document.getElementById('modalTitulo').textContent = 'Editar Usuario';
        document.getElementById('passwordGroup').style.display = 'none';
        document.getElementById('passwordInput').required = false;
        
        // Cargar datos del usuario
        Notificaciones.loading('Cargando datos del usuario...');
        
        Ajax.get(`${CONFIG.APP_URL}/index.php?action=ajax_obtener_usuario&id=${id}`)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    const usuario = response.datos;
                    document.getElementById('nombre').value = usuario.nombre;
                    document.getElementById('apellido').value = usuario.apellido;
                    document.getElementById('email').value = usuario.email;
                    document.getElementById('rol').value = usuario.rol;
                    document.getElementById('estado').value = usuario.estado;
                    
                    Modal.abrir('usuarioModal');
                } else {
                    Notificaciones.error(response.mensaje);
                }
            })
            .catch(error => {
                Notificaciones.ocultarLoading();
                console.error('Error:', error);
                Notificaciones.error('Error al cargar los datos del usuario');
            });
    },
    
    /**
     * Guardar usuario (crear o editar)
     */
    guardar: function() {
        const reglas = {
            nombre: [
                valor => Validacion.requerido(valor, 'nombre'),
                valor => Validacion.longitudMaxima(valor, 100, 'nombre')
            ],
            apellido: [
                valor => Validacion.requerido(valor, 'apellido'),
                valor => Validacion.longitudMaxima(valor, 100, 'apellido')
            ],
            email: [
                valor => Validacion.requerido(valor, 'email'),
                valor => Validacion.email(valor),
                valor => Validacion.longitudMaxima(valor, 150, 'email')
            ],
            rol: [
                valor => Validacion.requerido(valor, 'rol')
            ]
        };
        
        // Agregar validación de contraseña solo si es creación
        if (!editandoId) {
            reglas.password = [
                valor => Validacion.requerido(valor, 'contraseña'),
                valor => Validacion.longitudMinima(valor, 6, 'contraseña')
            ];
        }
        
        const validacion = Validacion.validarFormulario('usuarioForm', reglas);
        
        if (!validacion.valido) {
            Notificaciones.error(validacion.errores.join('\n'));
            return;
        }
        
        const formData = new FormData(document.getElementById('usuarioForm'));
        if (editandoId) {
            formData.append('id', editandoId);
        }
        
        const accion = editandoId ? 'ajax_actualizar_usuario' : 'ajax_crear_usuario';
        const mensaje = editandoId ? 'Actualizando usuario...' : 'Creando usuario...';
        
        Notificaciones.loading(mensaje);
        
        Ajax.post(`${CONFIG.APP_URL}/index.php?action=${accion}`, formData)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    Notificaciones.exito(response.mensaje);
                    Modal.cerrar('usuarioModal');
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
                Notificaciones.error('Error al guardar el usuario');
            });
    },
    
    /**
     * Eliminar usuario
     */
    eliminar: function(id, nombre) {
        Notificaciones.confirmacion(
            `¿Estás seguro de que quieres eliminar al usuario "${nombre}"? Esta acción no se puede deshacer.`,
            '¡Atención!'
        ).then((result) => {
            if (result.isConfirmed) {
                Notificaciones.loading('Eliminando usuario...');
                
                Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_eliminar_usuario`, { id: id })
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
                        Notificaciones.error('Error al eliminar el usuario');
                    });
            }
        });
    }
};

// Funciones para gestión de técnicas
const GestionTecnicas = {
    
    /**
     * Abrir modal para crear técnica
     */
    abrirModalCrear: function() {
        editandoId = null;
        Utils.limpiarFormulario('tecnicaForm');
        document.getElementById('modalTitulo').textContent = 'Crear Técnica';
        Modal.abrir('tecnicaModal');
    },
    
    /**
     * Abrir modal para editar técnica
     */
    abrirModalEditar: function(id) {
        editandoId = id;
        document.getElementById('modalTitulo').textContent = 'Editar Técnica';
        
        Notificaciones.loading('Cargando datos de la técnica...');
        
        Ajax.get(`${CONFIG.APP_URL}/index.php?action=ajax_obtener_tecnica&id=${id}`)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    const tecnica = response.datos;
                    document.getElementById('codigo').value = tecnica.codigo;
                    document.getElementById('nombre').value = tecnica.nombre;
                    document.getElementById('descripcion').value = tecnica.descripcion || '';
                    document.getElementById('estado').value = tecnica.estado;
                    
                    Modal.abrir('tecnicaModal');
                } else {
                    Notificaciones.error(response.mensaje);
                }
            })
            .catch(error => {
                Notificaciones.ocultarLoading();
                console.error('Error:', error);
                Notificaciones.error('Error al cargar los datos de la técnica');
            });
    },
    
    /**
     * Guardar técnica
     */
    guardar: function() {
        const reglas = {
            codigo: [
                valor => Validacion.requerido(valor, 'código'),
                valor => Validacion.longitudMaxima(valor, 20, 'código')
            ],
            nombre: [
                valor => Validacion.requerido(valor, 'nombre'),
                valor => Validacion.longitudMaxima(valor, 200, 'nombre')
            ],
            descripcion: [
                valor => Validacion.longitudMaxima(valor, 1000, 'descripción')
            ]
        };
        
        const validacion = Validacion.validarFormulario('tecnicaForm', reglas);
        
        if (!validacion.valido) {
            Notificaciones.error(validacion.errores.join('\n'));
            return;
        }
        
        const formData = new FormData(document.getElementById('tecnicaForm'));
        if (editandoId) {
            formData.append('id', editandoId);
        }
        
        const accion = editandoId ? 'ajax_actualizar_tecnica' : 'ajax_crear_tecnica';
        const mensaje = editandoId ? 'Actualizando técnica...' : 'Creando técnica...';
        
        Notificaciones.loading(mensaje);
        
        Ajax.post(`${CONFIG.APP_URL}/index.php?action=${accion}`, formData)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    Notificaciones.exito(response.mensaje);
                    Modal.cerrar('tecnicaModal');
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
                Notificaciones.error('Error al guardar la técnica');
            });
    },
    
    /**
     * Eliminar técnica
     */
    eliminar: function(id, nombre) {
        Notificaciones.confirmacion(
            `¿Estás seguro de que quieres eliminar la técnica "${nombre}"? Esta acción eliminará también todos los cursos asociados.`,
            '¡Atención!'
        ).then((result) => {
            if (result.isConfirmed) {
                Notificaciones.loading('Eliminando técnica...');
                
                Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_eliminar_tecnica`, { id: id })
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
                        Notificaciones.error('Error al eliminar la técnica');
                    });
            }
        });
    }
};

// Funciones para gestión de cursos
const GestionCursos = {
    
    /**
     * Abrir modal para crear curso
     */
    abrirModalCrear: function() {
        editandoId = null;
        Utils.limpiarFormulario('cursoForm');
        document.getElementById('modalTitulo').textContent = 'Crear Curso';
        Modal.abrir('cursoModal');
    },
    
    /**
     * Abrir modal para editar curso
     */
    abrirModalEditar: function(id) {
        editandoId = id;
        document.getElementById('modalTitulo').textContent = 'Editar Curso';
        
        Notificaciones.loading('Cargando datos del curso...');
        
        Ajax.get(`${CONFIG.APP_URL}/index.php?action=ajax_obtener_curso&id=${id}`)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    const curso = response.datos;
                    document.getElementById('tecnica_id').value = curso.tecnica_id;
                    document.getElementById('codigo').value = curso.codigo;
                    document.getElementById('nombre').value = curso.nombre;
                    document.getElementById('descripcion').value = curso.descripcion || '';
                    document.getElementById('estado').value = curso.estado;
                    
                    Modal.abrir('cursoModal');
                } else {
                    Notificaciones.error(response.mensaje);
                }
            })
            .catch(error => {
                Notificaciones.ocultarLoading();
                console.error('Error:', error);
                Notificaciones.error('Error al cargar los datos del curso');
            });
    },
    
    /**
     * Guardar curso
     */
    guardar: function() {
        const reglas = {
            tecnica_id: [
                valor => Validacion.requerido(valor, 'técnica')
            ],
            codigo: [
                valor => Validacion.requerido(valor, 'código'),
                valor => Validacion.longitudMaxima(valor, 20, 'código')
            ],
            nombre: [
                valor => Validacion.requerido(valor, 'nombre'),
                valor => Validacion.longitudMaxima(valor, 200, 'nombre')
            ],
            descripcion: [
                valor => Validacion.longitudMaxima(valor, 1000, 'descripción')
            ]
        };
        
        const validacion = Validacion.validarFormulario('cursoForm', reglas);
        
        if (!validacion.valido) {
            Notificaciones.error(validacion.errores.join('\n'));
            return;
        }
        
        const formData = new FormData(document.getElementById('cursoForm'));
        if (editandoId) {
            formData.append('id', editandoId);
        }
        
        const accion = editandoId ? 'ajax_actualizar_curso' : 'ajax_crear_curso';
        const mensaje = editandoId ? 'Actualizando curso...' : 'Creando curso...';
        
        Notificaciones.loading(mensaje);
        
        Ajax.post(`${CONFIG.APP_URL}/index.php?action=${accion}`, formData)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    Notificaciones.exito(response.mensaje);
                    Modal.cerrar('cursoModal');
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
                Notificaciones.error('Error al guardar el curso');
            });
    },
    
    /**
     * Eliminar curso
     */
    eliminar: function(id, nombre) {
        Notificaciones.confirmacion(
            `¿Estás seguro de que quieres eliminar el curso "${nombre}"? Esta acción eliminará también todas las asignaciones e inscripciones asociadas.`,
            '¡Atención!'
        ).then((result) => {
            if (result.isConfirmed) {
                Notificaciones.loading('Eliminando curso...');
                
                Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_eliminar_curso`, { id: id })
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
                        Notificaciones.error('Error al eliminar el curso');
                    });
            }
        });
    }
};

// Funciones para gestión de semestres
const GestionSemestres = {
    
    /**
     * Abrir modal para crear semestre
     */
    abrirModalCrear: function() {
        editandoId = null;
        Utils.limpiarFormulario('semestreForm');
        document.getElementById('modalTitulo').textContent = 'Crear Semestre';
        Modal.abrir('semestreModal');
    },
    
    /**
     * Abrir modal para editar semestre
     */
    abrirModalEditar: function(id) {
        editandoId = id;
        document.getElementById('modalTitulo').textContent = 'Editar Semestre';
        
        Notificaciones.loading('Cargando datos del semestre...');
        
        Ajax.get(`${CONFIG.APP_URL}/index.php?action=ajax_obtener_semestre&id=${id}`)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    const semestre = response.datos;
                    document.getElementById('nombre').value = semestre.nombre;
                    document.getElementById('fecha_inicio').value = semestre.fecha_inicio;
                    document.getElementById('fecha_fin').value = semestre.fecha_fin;
                    document.getElementById('estado').value = semestre.estado;
                    
                    Modal.abrir('semestreModal');
                } else {
                    Notificaciones.error(response.mensaje);
                }
            })
            .catch(error => {
                Notificaciones.ocultarLoading();
                console.error('Error:', error);
                Notificaciones.error('Error al cargar los datos del semestre');
            });
    },
    
    /**
     * Guardar semestre
     */
    guardar: function() {
        const reglas = {
            nombre: [
                valor => Validacion.requerido(valor, 'nombre'),
                valor => Validacion.longitudMaxima(valor, 50, 'nombre')
            ],
            fecha_inicio: [
                valor => Validacion.requerido(valor, 'fecha de inicio')
            ],
            fecha_fin: [
                valor => Validacion.requerido(valor, 'fecha de fin')
            ]
        };
        
        const validacion = Validacion.validarFormulario('semestreForm', reglas);
        
        if (!validacion.valido) {
            Notificaciones.error(validacion.errores.join('\n'));
            return;
        }
        
        // Validar que fecha_fin sea posterior a fecha_inicio
        const fechaInicio = new Date(document.getElementById('fecha_inicio').value);
        const fechaFin = new Date(document.getElementById('fecha_fin').value);
        
        if (fechaFin <= fechaInicio) {
            Notificaciones.error('La fecha de fin debe ser posterior a la fecha de inicio');
            return;
        }
        
        const formData = new FormData(document.getElementById('semestreForm'));
        if (editandoId) {
            formData.append('id', editandoId);
        }
        
        const accion = editandoId ? 'ajax_actualizar_semestre' : 'ajax_crear_semestre';
        const mensaje = editandoId ? 'Actualizando semestre...' : 'Creando semestre...';
        
        Notificaciones.loading(mensaje);
        
        Ajax.post(`${CONFIG.APP_URL}/index.php?action=${accion}`, formData)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    Notificaciones.exito(response.mensaje);
                    Modal.cerrar('semestreModal');
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
                Notificaciones.error('Error al guardar el semestre');
            });
    },
    
    /**
     * Eliminar semestre
     */
    eliminar: function(id, nombre) {
        Notificaciones.confirmacion(
            `¿Estás seguro de que quieres eliminar el semestre "${nombre}"? Esta acción eliminará también todas las asignaciones, inscripciones y calificaciones asociadas.`,
            '¡Atención!'
        ).then((result) => {
            if (result.isConfirmed) {
                Notificaciones.loading('Eliminando semestre...');
                
                Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_eliminar_semestre`, { id: id })
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
                        Notificaciones.error('Error al eliminar el semestre');
                    });
            }
        });
    }
};

// Funciones para gestión de asignaciones
const GestionAsignaciones = {
    
    /**
     * Crear asignación
     */
    crear: function() {
        const reglas = {
            docente_id: [
                valor => Validacion.requerido(valor, 'docente')
            ],
            curso_id: [
                valor => Validacion.requerido(valor, 'curso')
            ],
            semestre_id: [
                valor => Validacion.requerido(valor, 'semestre')
            ]
        };
        
        const validacion = Validacion.validarFormulario('asignacionForm', reglas);
        
        if (!validacion.valido) {
            Notificaciones.error(validacion.errores.join('\n'));
            return;
        }
        
        const formData = new FormData(document.getElementById('asignacionForm'));
        
        Notificaciones.loading('Creando asignación...');
        
        Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_crear_asignacion`, formData)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    Notificaciones.exito(response.mensaje);
                    Utils.limpiarFormulario('asignacionForm');
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
                Notificaciones.error('Error al crear la asignación');
            });
    },
    
    /**
     * Eliminar asignación
     */
    eliminar: function(id, docente, curso) {
        Notificaciones.confirmacion(
            `¿Estás seguro de que quieres eliminar la asignación del docente "${docente}" al curso "${curso}"?`,
            '¡Atención!'
        ).then((result) => {
            if (result.isConfirmed) {
                Notificaciones.loading('Eliminando asignación...');
                
                Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_eliminar_asignacion`, { id: id })
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
                        Notificaciones.error('Error al eliminar la asignación');
                    });
            }
        });
    }
};

// Funciones para gestión de inscripciones
const GestionInscripciones = {
    
    /**
     * Crear inscripción
     */
    crear: function() {
        const reglas = {
            estudiante_id: [
                valor => Validacion.requerido(valor, 'estudiante')
            ],
            curso_id: [
                valor => Validacion.requerido(valor, 'curso')
            ],
            semestre_id: [
                valor => Validacion.requerido(valor, 'semestre')
            ]
        };
        
        const validacion = Validacion.validarFormulario('inscripcionForm', reglas);
        
        if (!validacion.valido) {
            Notificaciones.error(validacion.errores.join('\n'));
            return;
        }
        
        const formData = new FormData(document.getElementById('inscripcionForm'));
        
        Notificaciones.loading('Creando inscripción...');
        
        Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_crear_inscripcion`, formData)
            .then(response => {
                Notificaciones.ocultarLoading();
                
                if (response.exito) {
                    Notificaciones.exito(response.mensaje);
                    Utils.limpiarFormulario('inscripcionForm');
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
                Notificaciones.error('Error al crear la inscripción');
            });
    },
    
    /**
     * Eliminar inscripción
     */
    eliminar: function(id, estudiante, curso) {
        Notificaciones.confirmacion(
            `¿Estás seguro de que quieres eliminar la inscripción del estudiante "${estudiante}" al curso "${curso}"? Esto también eliminará todas las calificaciones asociadas.`,
            '¡Atención!'
        ).then((result) => {
            if (result.isConfirmed) {
                Notificaciones.loading('Eliminando inscripción...');
                
                Ajax.post(`${CONFIG.APP_URL}/index.php?action=ajax_eliminar_inscripcion`, { id: id })
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
                        Notificaciones.error('Error al eliminar la inscripción');
                    });
            }
        });
    }
};

// Funciones de filtrado y búsqueda
const Filtros = {
    
    /**
     * Filtrar tabla por texto de búsqueda
     */
    filtrarTabla: function(inputId, tablaId) {
        const input = document.getElementById(inputId);
        const tabla = document.getElementById(tablaId);
        
        if (!input || !tabla) return;
        
        const filtro = input.value.toLowerCase();
        const filas = tabla.querySelectorAll('tbody tr');
        
        filas.forEach(fila => {
            const texto = fila.textContent.toLowerCase();
            if (texto.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    },
    
    /**
     * Configurar filtro en tiempo real
     */
    configurarFiltroTiempoReal: function(inputId, tablaId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', Utils.debounce(() => {
                this.filtrarTabla(inputId, tablaId);
            }, 300));
        }
    }
};

// Inicialización específica del admin
document.addEventListener('DOMContentLoaded', function() {
    
    // Configurar modales para cerrar al hacer clic fuera
    const modales = ['usuarioModal', 'tecnicaModal', 'cursoModal', 'semestreModal'];
    modales.forEach(modalId => {
        Modal.configurarCierreExterno(modalId);
    });
    
    // Configurar filtros de búsqueda
    Filtros.configurarFiltroTiempoReal('buscarUsuarios', 'tablaUsuarios');
    Filtros.configurarFiltroTiempoReal('buscarTecnicas', 'tablaTecnicas');
    Filtros.configurarFiltroTiempoReal('buscarCursos', 'tablaCursos');
    Filtros.configurarFiltroTiempoReal('buscarSemestres', 'tablaSemestres');
    Filtros.configurarFiltroTiempoReal('buscarAsignaciones', 'tablaAsignaciones');
    Filtros.configurarFiltroTiempoReal('buscarInscripciones', 'tablaInscripciones');
    
    console.log('JavaScript de administrador inicializado');
});

// Exportar funciones para uso global
window.GestionUsuarios = GestionUsuarios;
window.GestionTecnicas = GestionTecnicas;
window.GestionCursos = GestionCursos;
window.GestionSemestres = GestionSemestres;
window.GestionAsignaciones = GestionAsignaciones;
window.GestionInscripciones = GestionInscripciones;
window.Filtros = Filtros;
