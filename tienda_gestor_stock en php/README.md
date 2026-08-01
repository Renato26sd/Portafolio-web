# Sistema Web de Ventas e Inventario
## Bodega de Armando Tornero

Sistema web profesional para la gestión de ventas e inventario de pequeñas bodegas o minimarkets. Desarrollado con PHP 8+, MySQL, Bootstrap 5 y arquitectura MVC.

## 📋 Tabla de Contenidos

- [Características](#características)
- [Tecnologías](#tecnologías)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Módulos](#módulos)
- [Roles y Permisos](#roles-y-permisos)
- [Base de Datos](#base-de-datos)
- [Documentación UML](#documentación-uml)
- [Seguridad](#seguridad)
- [Contribución](#contribución)
- [Licencia](#licencia)

## ✨ Características

### Módulos Principales

- **Autenticación**: Sistema de login seguro con control de sesiones
- **Usuarios**: Gestión completa de usuarios y roles
- **Productos**: CRUD de productos con control de stock
- **Categorías**: Organización de productos por categorías
- **Proveedores**: Gestión de proveedores
- **Ventas**: Sistema de punto de venta (POS)
- **Inventario**: Control de entradas y salidas
- **Reportes**: Generación de reportes y exportación
- **Dashboard**: Panel de control con estadísticas

### Funcionalidades Extra

- Buscador en tiempo real
- Alertas de stock bajo
- Modo oscuro
- Dashboard con gráficas interactivas
- Logs de actividad
- Auditoría básica
- Exportación a Excel/CSV
- Generación de tickets
- Soporte para códigos de barras

## 🛠 Tecnologías

### Backend
- **PHP 8+**: Lenguaje principal
- **MySQL**: Base de datos relacional
- **PDO**: Conexión segura a la base de datos
- **Arquitectura MVC**: Separación de responsabilidades

### Frontend
- **HTML5**: Estructura semántica
- **CSS3**: Estilos modernos
- **JavaScript**: Interactividad
- **Bootstrap 5**: Framework CSS
- **Chart.js**: Gráficas interactivas
- **FontAwesome**: Iconos

### Seguridad
- **password_hash()**: Encriptación de contraseñas
- **PDO Prepared Statements**: Protección SQL Injection
- **CSRF Tokens**: Protección contra CSRF
- **Session Management**: Control de sesiones
- **Middleware**: Autenticación y autorización

## 📦 Requisitos

### Servidor
- PHP 8.0 o superior
- MySQL 5.7 o superior / MariaDB 10.3+
- Apache 2.4+ con mod_rewrite
- Mínimo 512MB RAM
- 100MB espacio en disco

### Extensiones PHP
- PDO
- PDO_MySQL
- Mbstring
- GD (para imágenes)
- JSON
- Session

### Navegadores Soportados
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/bodega-inventario.git
cd bodega-inventario
```

### 2. Configurar base de datos

```bash
# Crear base de datos
mysql -u root -p
CREATE DATABASE bodega_tornero CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Importar script SQL
mysql -u root -p bodega_tornero < database/script.sql
```

### 3. Configurar variables de entorno

```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Editar archivo .env con tus credenciales
nano .env
```

### 4. Configurar permisos

```bash
# Dar permisos de escritura
chmod -R 755 public/storage
chmod -R 755 public/img
```

### 5. Configurar servidor web

#### Apache

Asegúrate de que `mod_rewrite` esté habilitado:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### PHP Built-in Server (Desarrollo)

```bash
php -S localhost:8000
```

## ⚙️ Configuración

### Archivo `.env`

```env
DB_HOST=localhost
DB_NAME=bodega_tornero
DB_USER=root
DB_PASS=

ENVIRONMENT=development
SITE_URL=http://localhost/bodega-system

SESSION_NAME=bodega_session
SESSION_LIFETIME=7200

IGV_PERCENTAGE=18
CURRENCY_SYMBOL=S/
```

### Usuario por Defecto

- **Usuario**: `admin`
- **Contraseña**: `admin123`
- **Rol**: Administrador

⚠️ **Importante**: Cambia la contraseña del administrador después del primer inicio de sesión.

## 📁 Estructura del Proyecto

```
bodega-inventario/
├── app/
│   ├── controllers/       # Controladores MVC
│   ├── models/           # Modelos de datos
│   ├── views/            # Vistas HTML
│   │   ├── layouts/      # Layouts base
│   │   ├── auth/         # Vistas de autenticación
│   │   ├── dashboard/    # Vistas del dashboard
│   │   ├── usuarios/     # Vistas de usuarios
│   │   ├── productos/    # Vistas de productos
│   │   ├── ventas/       # Vistas de ventas
│   │   ├── categorias/   # Vistas de categorías
│   │   ├── proveedores/  # Vistas de proveedores
│   │   ├── inventario/   # Vistas de inventario
│   │   └── reportes/     # Vistas de reportes
│   ├── middlewares/      # Middlewares de seguridad
│   └── helpers/          # Clases auxiliares
├── config/               # Archivos de configuración
│   ├── config.php        # Configuración principal
│   └── database.php      # Conexión a BD
├── database/             # Scripts de base de datos
│   └── script.sql        # Script SQL completo
├── public/               # Archivos públicos
│   ├── css/              # Estilos CSS
│   ├── js/               # Archivos JavaScript
│   ├── img/              # Imágenes
│   └── storage/          # Archivos de almacenamiento
├── docs/                 # Documentación
│   └── uml/              # Diagramas UML
├── routes/               # Rutas del sistema
├── storage/              # Archivos temporales
├── .env.example          # Ejemplo de configuración
├── .htaccess             # Configuración Apache
├── index.php             # Punto de entrada
├── login.php             # Página de login
├── logout.php            # Página de logout
├── dashboard.php         # Dashboard
└── README.md             # Este archivo
```

## 📚 Módulos

### 1. Autenticación
- Inicio de sesión seguro
- Cierre de sesión
- Control de intentos fallidos
- Expiración de sesión
- Recuperación de contraseña (futuro)

### 2. Usuarios
- Crear, editar, eliminar usuarios
- Asignar roles
- Activar/desactivar usuarios
- Historial de actividad

### 3. Productos
- CRUD completo de productos
- Control de stock
- Alertas de stock bajo
- Subida de imágenes
- Códigos de barras
- Búsqueda y filtrado

### 4. Categorías
- Gestión de categorías
- Organización de productos
- Control de productos por categoría

### 5. Proveedores
- Registro de proveedores
- Información de contacto
- Asociación con productos

### 6. Ventas
- Sistema POS
- Carrito de compras
- Múltiples métodos de pago
- Generación de tickets
- Actualización automática de stock
- Historial de ventas

### 7. Inventario
- Registro de entradas
- Registro de salidas
- Kardex de productos
- Alertas de stock bajo
- Historial de movimientos

### 8. Reportes
- Ventas por fecha
- Productos más vendidos
- Productos sin stock
- Reporte de ganancias
- Exportación a Excel/CSV
- Gráficas estadísticas

## 👥 Roles y Permisos

### Administrador
- Acceso total al sistema
- Gestionar usuarios
- Gestionar productos
- Gestionar categorías
- Gestionar proveedores
- Ver reportes
- Configuración del sistema
- Control de inventario
- Ver historial completo

### Vendedor
- Registrar ventas
- Consultar productos
- Ver stock
- Generar comprobantes
- Buscar clientes
- **NO puede**: Eliminar productos, gestionar usuarios, configuración

### Cajero
- Procesar pagos
- Verificar métodos de pago
- Generar tickets
- Realizar cierre de caja
- **NO puede**: Gestionar inventario, gestionar usuarios

### Almacenero
- Registrar entradas de productos
- Actualizar stock
- Ver inventario
- Ver productos agotados
- **NO puede**: Registrar ventas, administrar usuarios

## 🗄️ Base de Datos

### Tablas Principales

- **usuarios**: Información de usuarios
- **roles**: Roles del sistema
- **permisos**: Permisos del sistema
- **rol_permiso**: Relación roles-permisos
- **categorias**: Categorías de productos
- **proveedores**: Proveedores
- **productos**: Productos del inventario
- **clientes**: Clientes
- **ventas**: Ventas registradas
- **detalle_ventas**: Detalles de ventas
- **movimientos_inventario**: Movimientos de stock
- **logs_actividad**: Logs de auditoría
- **configuracion**: Configuración del sistema

### Características

- Normalizada (3NF)
- Índices optimizados
- Triggers para actualización de stock
- Vistas para reportes
- Procedimientos almacenados

## 📐 Documentación UML

La documentación UML se encuentra en la carpeta `docs/uml/`:

- **casos_uso.md**: Diagrama de casos de uso
- **diagrama_clases.md**: Diagrama de clases
- **diagrama_entidad_relacion.md**: Diagrama entidad-relación
- **diagrama_secuencia.md**: Diagrama de secuencia
- **diagrama_actividades.md**: Diagrama de actividades

## 🔒 Seguridad

### Implementaciones de Seguridad

1. **Autenticación**
   - Contraseñas encriptadas con `password_hash()`
   - Control de intentos fallidos
   - Expiración de sesión
   - Regeneración de ID de sesión

2. **Autorización**
   - Sistema de roles y permisos
   - Middleware de autenticación
   - Verificación de permisos por módulo

3. **Protección contra ataques**
   - PDO Prepared Statements (SQL Injection)
   - CSRF Tokens
   - Validación de formularios
   - Sanitización de datos
   - Headers de seguridad HTTP

4. **Auditoría**
   - Logs de actividad
   - Registro de acciones
   - Dirección IP
   - Fecha y hora

## 🤝 Contribución

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/NuevaFuncionalidad`)
3. Commit tus cambios (`git commit -m 'Añadir nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/NuevaFuncionalidad`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 👤 Autor

**Armando Tornero**

## 🙏 Agradecimientos

- Bootstrap Team por el framework CSS
- Chart.js por las gráficas
- FontAwesome por los iconos
- Comunidad PHP por la documentación

---

**Desarrollado con ❤️ para la Bodega de Armando Tornero**
