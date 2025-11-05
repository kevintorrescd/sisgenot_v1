# SISGENOT - Sistema de Gestión de Notas Académicas

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-Academic-blue?style=for-the-badge)

Sistema completo de gestión de notas académicas desarrollado en PHP puro con arquitectura MVC. Permite la gestión integral de calificaciones, estudiantes, docentes y cursos académicos.

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Tecnologías](#-tecnologías)
- [Seguridad](#-seguridad)
- [Uso](#-uso)
- [Licencia](#-licencia)

## ✨ Características

### 👨‍💼 Administrador
- Gestión completa de usuarios (crear, editar, eliminar)
- Gestión de técnicas educativas
- Gestión de cursos y materias
- Gestión de semestres académicos
- Asignación de docentes a cursos
- Inscripción de estudiantes a cursos
- Dashboard con estadísticas generales

### 👨‍🏫 Docente
- Ver cursos asignados
- Calificar estudiantes por técnica
- Ver listados de estudiantes inscritos
- Dashboard con resumen de actividad

### 👨‍🎓 Estudiante
- Ver notas por curso y semestre
- Ver promedios generales y por curso
- Consultar historial académico
- Dashboard con estadísticas personales

## 🔧 Requisitos

- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior
- **Servidor Web**: Apache con mod_rewrite habilitado
- **Navegador**: Navegador web moderno

## 📦 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/sisgenot.git
cd sisgenot
```

### 2. Configurar el servidor web

Configura tu servidor web para apuntar al directorio del proyecto. Si usas XAMPP, WAMP o MAMP, copia el proyecto en el directorio correspondiente (`htdocs`, `www`, etc.).

### 3. Crear la base de datos

Ejecuta el script SQL proporcionado (ubicado en `sisgenot.md`) para crear la base de datos y las tablas necesarias.

```sql
CREATE DATABASE sisgenot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sisgenot;
-- Ejecutar el resto del script SQL
```

### 4. Configurar la conexión a la base de datos

Edita el archivo `config/database.php` y configura tus credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sisgenot');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 5. Acceder al sistema

Accede al sistema desde tu navegador:

```
http://localhost/sisgenot/
```

## ⚙️ Configuración

### Variables de Entorno

Rmplaza los datos de database.php.example y borra .example

### Usuarios de Prueba

Después de la instalación, puedes crear usuarios de prueba a través del panel de administración. Consulta la documentación del script SQL para obtener información sobre usuarios predeterminados.

## 📁 Estructura del Proyecto

```
sisgenot/
├── config/               # Configuraciones del sistema
│   ├── config.php       # Constantes globales
│   └── database.php.example     # Conexión PDO a MySQL
├── controllers/         # Controladores MVC
│   ├── AuthController.php
│   ├── AdminController.php
│   ├── DocenteController.php
│   └── EstudianteController.php
├── models/              # Modelos de datos
│   ├── User.php
│   ├── Tecnica.php
│   ├── Curso.php
│   ├── Semestre.php
│   ├── Asignacion.php
│   ├── Inscripcion.php
│   └── Calificacion.php
├── views/               # Vistas HTML/PHP
│   ├── auth/           # Login
│   ├── layouts/        # Header, sidebar, footer
│   ├── admin/          # Panel de administrador
│   ├── docente/        # Panel de docente
│   └── estudiante/     # Panel de estudiante
├── includes/            # Archivos auxiliares
│   ├── session.php     # Gestión de sesiones
│   └── functions.php   # Funciones auxiliares
├── public/              # Recursos públicos
│   ├── css/            # Estilos personalizados
│   ├── js/             # JavaScript
│   └── img/            # Imágenes y medios públicos
├── .htaccess            # Configuración de Apache y reglas de acceso
└── index.php            # Punto de entrada
```

## 🛠 Tecnologías

- **Backend**: PHP 7.4+ (sin frameworks)
- **Base de datos**: MySQL con PDO
- **Frontend**: HTML5, CSS3, JavaScript (vanilla)
- **Estilos**: Tailwind CSS (vía CDN)
- **Notificaciones**: SweetAlert2 (vía CDN)
- **Arquitectura**: MVC puro
- **Servidor**: Apache con .htaccess

## 🔒 Seguridad

El sistema implementa múltiples capas de seguridad:

- ✅ Contraseñas hasheadas con `password_hash()` y `password_verify()`
- ✅ Prevención de SQL Injection con prepared statements (PDO)
- ✅ Prevención de XSS con `htmlspecialchars()` y sanitización
- ✅ Validación de datos en backend y frontend
- ✅ Sistema de roles y permisos por usuario
- ✅ Gestión segura de sesiones con regeneración de ID
- ✅ Tokens CSRF para formularios críticos
- ✅ Protección contra ataques de fuerza bruta

## 🚀 Características Técnicas

- **Responsive Design**: Adaptable a dispositivos móviles y tablets
- **AJAX**: Operaciones sin recarga de página para mejor UX
- **Validaciones**: Cliente y servidor para máxima seguridad
- **Cálculo automático**: Promedios y estadísticas en tiempo real
- **Interfaz moderna**: UI/UX profesional y intuitiva
- **Notificaciones**: Feedback visual con SweetAlert2
- **Búsqueda en tiempo real**: Filtros dinámicos para mejor usabilidad
- **Paginación**: Para grandes volúmenes de datos

## 📖 Uso

1. Inicia sesión con tus credenciales
2. Navega por el menú según tu rol (Administrador, Docente o Estudiante)
3. Utiliza las funcionalidades disponibles según tus permisos

## 📄 Licencia

Sistema desarrollado para fines académicos. Todos los derechos reservados.

---

**Versión**: 1.0.0  
**Año**: 2025  
**SISGENOT** © Todos los derechos reservados