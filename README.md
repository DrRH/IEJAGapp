# 🏫 Sistema de Gestión I.E. José Antonio Galán

Sistema integral de gestión institucional desarrollado con Laravel 11 para la Institución Educativa José Antonio Galán de Medellín, Colombia.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## 📋 Descripción

Sistema web moderno y responsive para la gestión administrativa, académica y de convivencia escolar. Diseñado específicamente para instituciones educativas colombianas, integrando las mejores prácticas de desarrollo y seguridad.

## ✨ Características Principales

### 🔐 Autenticación y Seguridad
- Autenticación mediante Google OAuth 2.0
- Sistema de roles y permisos con Spatie Permission
- Gestión granular de accesos
- Protección CSRF y seguridad Laravel nativa

### 👥 Gestión de Usuarios
- CRUD completo de usuarios
- Asignación múltiple de roles
- Perfiles de usuario personalizables
- Integración con cuentas de Google
- Avatar automático o desde Google

### 📊 Módulos Implementados

#### ✅ Administración
- **Gestión de Usuarios**: Crear, editar, eliminar y asignar roles
- **Configuración Institucional**: Sistema completo de configuración con 28 campos organizados en pestañas:
  - Información básica (nombre, dirección, ciudad, departamento, país)
  - Datos legales (NIT, código DANE, resolución de aprobación)
  - Información de contacto (emails, teléfonos, sitio web)
  - Datos de directivos (rector, coordinador)
  - Configuración académica (año lectivo, calendario, niveles educativos)
  - Redes sociales (Facebook, Instagram, Twitter, YouTube)
  - Preferencias del sistema (tema, zona horaria)
- **Logs y Auditoría**: Seguimiento de actividades (en desarrollo)

#### 🎓 Académico (Estructura preparada)
- Gestión de estudiantes
- Registro de calificaciones
- Control de asistencia
- Boletines y reportes académicos

#### 🤝 Convivencia (Estructura preparada)
- Observador del estudiante
- Gestión de casos disciplinarios
- Atención psicosocial
- Comité de convivencia

#### 📝 Actas y Comités (Estructura preparada)
- Actas generales
- Comité académico
- Comité de convivencia
- Gestión documental

#### 📈 Reportes e Indicadores (Estructura preparada)
- Reportes académicos
- Reportes de convivencia
- Tableros de indicadores
- Exportación de datos

#### 🔗 Integraciones Google (Estructura preparada)
- Google Drive
- Google Sheets
- Google Calendar

### 🎨 Interfaz de Usuario
- Diseño moderno con [Tabler UI](https://tabler.io/)
- Responsive y mobile-first
- Iconos Tabler Icons
- Tema claro/oscuro (preparado)
- Notificaciones flash
- Modales de confirmación

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel 11.x** - Framework PHP
- **PHP 8.3+** - Lenguaje de programación
- **MySQL/MariaDB** - Base de datos
- **Spatie Permission** - Gestión de roles y permisos

### Frontend
- **Tabler UI** - Framework CSS
- **Vite** - Build tool
- **Tailwind CSS** - Utilidades CSS
- **Alpine.js** (integrable) - JavaScript reactivo

### Autenticación
- **Laravel Socialite** - OAuth con Google
- **Google OAuth 2.0** - Proveedor de autenticación

## 📦 Requisitos del Sistema

- PHP >= 8.3
- Composer
- Node.js >= 18.x y npm
- MySQL >= 8.0 o MariaDB >= 10.6
- Git
- Servidor web (Apache/Nginx)

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/DrRH/IEJAGapp.git
cd IEJAGapp
```

### 2. Instalar dependencias

```bash
# Dependencias de PHP
composer install

# Dependencias de Node.js
npm install
```

### 3. Configurar variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` con tus configuraciones:

```env
APP_NAME="I.E. José Antonio Galán"
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iejag_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

# Google OAuth
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret
GOOGLE_REDIRECT_URI=https://tu-dominio.com/auth/google/callback
```

### 4. Configurar base de datos

```bash
# Crear base de datos
mysql -u root -p
CREATE DATABASE iejag_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Ejecutar migraciones
php artisan migrate

# (Opcional) Ejecutar seeders para datos de prueba
php artisan db:seed
```

### 5. Compilar assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 6. Configurar permisos

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Iniciar servidor de desarrollo

```bash
php artisan serve
```

Visita: `http://localhost:8000`

## 🔑 Google OAuth Setup

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita "Google+ API"
4. En "Credenciales", crea un OAuth 2.0 Client ID
5. Agrega las URIs autorizadas:
   - **JavaScript origins**: `https://tu-dominio.com`
   - **Redirect URIs**: `https://tu-dominio.com/auth/google/callback`
6. Copia el Client ID y Client Secret al `.env`

## 👤 Roles Predefinidos

El sistema incluye 4 roles básicos:

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **Administrador** | Control total del sistema | Todos los permisos |
| **Coordinador** | Gestión académica y disciplinaria | Gestión de estudiantes, reportes |
| **Docente** | Gestión de calificaciones y asistencia | Calificaciones, observador |
| **Secretaria** | Gestión administrativa | Matrículas, documentos |

## 📁 Estructura del Proyecto

```
IEJAGapp/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/
│   │       │   └── GoogleController.php
│   │       ├── UsersController.php
│   │       ├── SettingsController.php
│   │       └── ProfileController.php
│   ├── Models/
│   │   ├── User.php
│   │   └── Setting.php
│   └── Services/
│       └── GoogleService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── administracion/
│       │   └── usuarios/
│       ├── auth/
│       ├── layouts/
│       └── settings/
├── routes/
│   └── web.php
└── public/
```

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Con cobertura
php artisan test --coverage
```

## 📝 Uso Básico

### Crear un usuario administrador

```bash
php artisan tinker
```

```php
$user = User::create([
    'name' => 'Administrador',
    'email' => 'admin@josegalan.edu.co',
    'password' => Hash::make('password')
]);

$user->assignRole('Administrador');
```

### Acceder al sistema

1. Visita la URL de tu aplicación
2. Click en "Ingresar con Google"
3. Autoriza con tu cuenta de Google
4. Serás redirigido al dashboard

## 🔄 Actualización

```bash
# Obtener últimos cambios
git pull origin main

# Actualizar dependencias
composer install
npm install

# Ejecutar migraciones pendientes
php artisan migrate

# Recompilar assets
npm run build

# Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 🐛 Troubleshooting

### Error de permisos en storage

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Error de conexión a base de datos

Verifica que:
- MySQL/MariaDB esté corriendo
- Las credenciales en `.env` sean correctas
- La base de datos exista
- El usuario tenga permisos

### Error con caracteres especiales (tildes, ñ)

Asegúrate que:
- La base de datos use `utf8mb4_unicode_ci`
- En `config/database.php` esté configurado UTF-8MB4
- Las tablas usen `CHARACTER SET utf8mb4`

## 🤝 Contribución

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👨‍💻 Autor

**Dorian Ruiz Hernández**
- GitHub: [@DrRH](https://github.com/DrRH)
- Email: dorianrodrigo@gmail.com

## 🏛️ Institución

**Institución Educativa José Antonio Galán**
- Ubicación: Medellín, Colombia
- Sitio web: [josegalan.edu.co](https://josegalan.edu.co)

## 🙏 Agradecimientos

- [Laravel](https://laravel.com/) - Framework PHP
- [Tabler](https://tabler.io/) - UI Framework
- [Spatie](https://spatie.be/) - Laravel Permissions
- Claude Code - Asistencia en desarrollo

---

⭐ Si este proyecto te es útil, considera darle una estrella en GitHub

📧 Para soporte o consultas: convivencia@josegalan.edu.co
