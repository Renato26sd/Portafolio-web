CREATE DATABASE IF NOT EXISTS bodega_stock CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bodega_stock;

-- =============================================
-- TABLA: roles
-- =============================================
CREATE TABLE IF NOT EXISTS roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar roles por defecto
INSERT INTO roles (nombre_rol, descripcion) VALUES
('Administrador', 'Acceso total al sistema, gestión de usuarios, configuración y reportes'),
('Vendedor', 'Registrar ventas, consultar productos, ver stock y generar comprobantes'),
('Cajero', 'Procesar pagos, verificar métodos de pago, generar tickets y cierre de caja'),
('Almacenero', 'Registrar entradas de productos, actualizar stock y ver inventario');

-- =============================================
-- TABLA: permisos
-- =============================================
CREATE TABLE IF NOT EXISTS permisos (
    id_permiso INT AUTO_INCREMENT PRIMARY KEY,
    nombre_permiso VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar permisos
INSERT INTO permisos (nombre_permiso, descripcion) VALUES
('usuarios.ver', 'Ver lista de usuarios'),
('usuarios.crear', 'Crear nuevos usuarios'),
('usuarios.editar', 'Editar usuarios existentes'),
('usuarios.eliminar', 'Eliminar usuarios'),
('productos.ver', 'Ver lista de productos'),
('productos.crear', 'Crear nuevos productos'),
('productos.editar', 'Editar productos existentes'),
('productos.eliminar', 'Eliminar productos'),
('categorias.ver', 'Ver lista de categorías'),
('categorias.crear', 'Crear nuevas categorías'),
('categorias.editar', 'Editar categorías'),
('categorias.eliminar', 'Eliminar categorías'),
('proveedores.ver', 'Ver lista de proveedores'),
('proveedores.crear', 'Crear nuevos proveedores'),
('proveedores.editar', 'Editar proveedores'),
('proveedores.eliminar', 'Eliminar proveedores'),
('ventas.ver', 'Ver lista de ventas'),
('ventas.crear', 'Registrar nuevas ventas'),
('ventas.editar', 'Editar ventas'),
('ventas.eliminar', 'Eliminar ventas'),
('inventario.ver', 'Ver inventario'),
('inventario.entradas', 'Registrar entradas de inventario'),
('inventario.salidas', 'Registrar salidas de inventario'),
('reportes.ver', 'Ver reportes'),
('reportes.exportar', 'Exportar reportes'),
('configuracion.ver', 'Ver configuración del sistema'),
('configuracion.editar', 'Editar configuración del sistema');

-- =============================================
-- TABLA: rol_permiso
-- =============================================
CREATE TABLE IF NOT EXISTS rol_permiso (
    id_rol_permiso INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT NOT NULL,
    id_permiso INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON DELETE CASCADE,
    FOREIGN KEY (id_permiso) REFERENCES permisos(id_permiso) ON DELETE CASCADE,
    UNIQUE KEY unique_rol_permiso (id_rol, id_permiso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Asignar permisos al rol Administrador (todos los permisos)
INSERT INTO rol_permiso (id_rol, id_permiso)
SELECT 1, id_permiso FROM permisos;

-- Asignar permisos al rol Vendedor
INSERT INTO rol_permiso (id_rol, id_permiso) VALUES
(2, 5), (2, 6), (2, 7), -- productos
(2, 9), -- categorías.ver
(2, 13), -- proveedores.ver
(2, 17), (2, 18), -- ventas
(2, 21), -- inventario.ver
(2, 25), (2, 26); -- reportes

-- Asignar permisos al rol Cajero
INSERT INTO rol_permiso (id_rol, id_permiso) VALUES
(3, 17), (3, 18), -- ventas
(3, 21); -- inventario.ver

-- Asignar permisos al rol Almacenero
INSERT INTO rol_permiso (id_rol, id_permiso) VALUES
(4, 5), -- productos.ver
(4, 9), -- categorías.ver
(4, 13), -- proveedores.ver
(4, 21), (4, 22), (4, 23); -- inventario

-- =============================================
-- TABLA: usuarios
-- =============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contraseña VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso DATETIME,
    intentos_fallidos INT DEFAULT 0,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario administrador por defecto
-- Contraseña: admin123 (hash generado con password_hash)
INSERT INTO usuarios (nombres, apellidos, correo, usuario, contraseña, id_rol, telefono) VALUES
('Armando', 'Tornero', 'admin@bodegatornero.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, '955212016');

-- =============================================
-- TABLA: categorias
-- =============================================
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar categorías por defecto
INSERT INTO categorias (nombre, descripcion) VALUES
('Bebidas', 'Bebidas variadas incluyendo gaseosas, jugos, aguas'),
('Abarrotes', 'Productos de abarrotes y despensa'),
('Limpieza', 'Productos de limpieza del hogar'),
('Snacks', 'Snacks y golosinas'),
('Lácteos', 'Productos lácteos'),
('Panadería', 'Productos de panadería'),
('Carnes', 'Carnes y embutidos'),
('Verduras', 'Verduras y frutas');

-- =============================================
-- TABLA: proveedores
-- =============================================
CREATE TABLE IF NOT EXISTS proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    ruc VARCHAR(20) NOT NULL UNIQUE,
    razon_social VARCHAR(200) NOT NULL,
    nombre_contacto VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT,
    correo VARCHAR(100),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO proveedores (ruc,razon_social,nombre_contacto, telefono,direccion,correo,estado) VALUES
('20100070970', 'Coca-Cola Servicios de Peru S.A.', 'Juan Perez', '987654321', 'Av. Industrial 123, Lima', 'ventas@cocacola.com', 'activo'),
('20131312955', 'Alicorp S.A.A.', 'Maria Lopez', '987654322', 'Av. Argentina 4793, Callao', 'contacto@alicorp.com', 'activo'),
('20100190797', 'Gloria S.A.', 'Carlos Torres', '987654323', 'Av. Republica de Panama 2461, Lima', 'ventas@gloria.com.pe', 'activo'),
('20512345678', 'Distribuidora San Martin S.R.L.', 'Ana Ruiz', '987654324', 'Jr. Los Olivos 456, Lima', 'informes@sanmartin.com', 'activo'),
('20698765432', 'Comercializadora Los Andes E.I.R.L.', 'Luis Vargas', '987654325', 'Av. Los Proceres 789, Lima', 'ventas@losandes.com', 'activo');

-- =============================================
-- TABLA: productos
-- =============================================
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    codigo_barras VARCHAR(50),
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio_compra DECIMAL(10, 2) NOT NULL,
    precio_venta DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    imagen VARCHAR(255),
    id_categoria INT NOT NULL,
    id_proveedor INT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE RESTRICT,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- =============================================
-- TABLA: clientes
-- =============================================
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(20) UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT,
    correo VARCHAR(100),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLA: ventas
-- =============================================
CREATE TABLE IF NOT EXISTS ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_cliente INT,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10, 2) NOT NULL,
    igv DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    descuento DECIMAL(10, 2) DEFAULT 0,
    metodo_pago ENUM('efectivo', 'yape', 'plin', 'tarjeta') NOT NULL,
    estado ENUM('completada', 'anulada', 'pendiente') DEFAULT 'completada',
    observaciones TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLA: detalle_ventas
-- =============================================
CREATE TABLE IF NOT EXISTS detalle_ventas (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLA: movimientos_inventario
-- =============================================
CREATE TABLE IF NOT EXISTS movimientos_inventario (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida') NOT NULL,
    cantidad INT NOT NULL,
    stock_anterior INT NOT NULL,
    stock_nuevo INT NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    observacion TEXT,
    id_usuario INT NOT NULL,
    referencia VARCHAR(50),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE RESTRICT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLA: caja
-- =============================================
CREATE TABLE IF NOT EXISTS caja (
    id_caja INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_apertura DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre DATETIME,
    monto_apertura DECIMAL(10, 2) DEFAULT 0,
    monto_cierre DECIMAL(10, 2),
    total_ventas DECIMAL(10, 2) DEFAULT 0,
    estado ENUM('abierta', 'cerrada') DEFAULT 'abierta',
    observaciones TEXT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLA: logs_actividad
-- =============================================
CREATE TABLE IF NOT EXISTS logs_actividad (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    descripcion TEXT,
    ip_address VARCHAR(45),
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- TABLA: configuracion
-- =============================================
CREATE TABLE IF NOT EXISTS configuracion (
    id_config INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    descripcion TEXT,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración por defecto
INSERT INTO configuracion (clave, valor, descripcion) VALUES
('nombre_empresa', 'Bodega Armando Tornero', 'Nombre de la empresa'),
('ruc_empresa', '', 'RUC de la empresa'),
('direccion_empresa', '', 'Dirección de la empresa'),
('telefono_empresa', '', 'Teléfono de la empresa'),
('igv_porcentaje', '18', 'Porcentaje de IGV'),
('moneda', 'S/', 'Símbolo de moneda'),
('ticket_footer', '¡Gracias por su compra!', 'Pie de página del ticket');

-- =============================================
-- ÍNDICES PARA MEJORAR RENDIMIENTO
-- =============================================
CREATE INDEX idx_productos_categoria ON productos(id_categoria);
CREATE INDEX idx_productos_proveedor ON productos(id_proveedor);
CREATE INDEX idx_productos_estado ON productos(estado);
CREATE INDEX idx_ventas_usuario ON ventas(id_usuario);
CREATE INDEX idx_ventas_fecha ON ventas(fecha);
CREATE INDEX idx_ventas_cliente ON ventas(id_cliente);
CREATE INDEX idx_detalle_ventas_venta ON detalle_ventas(id_venta);
CREATE INDEX idx_detalle_ventas_producto ON detalle_ventas(id_producto);
CREATE INDEX idx_movimientos_producto ON movimientos_inventario(id_producto);
CREATE INDEX idx_movimientos_fecha ON movimientos_inventario(fecha);
CREATE INDEX idx_logs_usuario ON logs_actividad(id_usuario);
CREATE INDEX idx_logs_fecha ON logs_actividad(fecha);

-- =============================================
-- VISTAS ÚTILES
-- =============================================

-- Vista: Productos con bajo stock
CREATE OR REPLACE VIEW vista_productos_bajo_stock AS
SELECT p.*, c.nombre as categoria_nombre, pr.razon_social as proveedor_nombre
FROM productos p
LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
WHERE p.stock <= p.stock_minimo AND p.estado = 'activo';

-- Vista: Resumen de ventas por día
CREATE OR REPLACE VIEW vista_ventas_diarias AS
SELECT 
    DATE(fecha) as fecha,
    COUNT(*) as total_ventas,
    SUM(total) as total_monto,
    SUM(subtotal) as total_subtotal,
    SUM(igv) as total_igv
FROM ventas
WHERE estado = 'completada'
GROUP BY DATE(fecha);

-- Vista: Productos más vendidos
CREATE OR REPLACE VIEW vista_productos_mas_vendidos AS
SELECT 
    p.id_producto,
    p.codigo,
    p.nombre,
    SUM(dv.cantidad) as total_vendido,
    SUM(dv.subtotal) as total_ingresos
FROM productos p
INNER JOIN detalle_ventas dv ON p.id_producto = dv.id_producto
INNER JOIN ventas v ON dv.id_venta = v.id_venta
WHERE v.estado = 'completada'
GROUP BY p.id_producto, p.codigo, p.nombre
ORDER BY total_vendido DESC;

-- =============================================
-- TRIGGERS
-- =============================================

-- Trigger: Actualizar stock al registrar venta
DELIMITER //
CREATE TRIGGER tr_actualizar_stock_venta
AFTER INSERT ON detalle_ventas
FOR EACH ROW
BEGIN
    UPDATE productos 
    SET stock = stock - NEW.cantidad
    WHERE id_producto = NEW.id_producto;
    
    INSERT INTO movimientos_inventario (
        id_producto, 
        tipo_movimiento, 
        cantidad, 
        stock_anterior, 
        stock_nuevo, 
        observacion, 
        id_usuario,
        referencia
    )
    VALUES (
        NEW.id_producto, 
        'salida', 
        NEW.cantidad, 
        (SELECT stock + NEW.cantidad FROM productos WHERE id_producto = NEW.id_producto),
        (SELECT stock FROM productos WHERE id_producto = NEW.id_producto),
        'Salida por venta', 
        (SELECT id_usuario FROM ventas WHERE id_venta = NEW.id_venta),
        CONCAT('Venta #', NEW.id_venta)
    );
END//
DELIMITER ;

-- Trigger: Restaurar stock al anular venta
DELIMITER //
CREATE TRIGGER tr_restaurar_stock_anulacion
BEFORE UPDATE ON ventas
FOR EACH ROW
BEGIN
    IF NEW.estado = 'anulada' AND OLD.estado != 'anulada' THEN
        UPDATE productos p
        INNER JOIN detalle_ventas dv ON p.id_producto = dv.id_producto
        SET p.stock = p.stock + dv.cantidad
        WHERE dv.id_venta = NEW.id_venta;
    END IF;
END//
DELIMITER ;

-- =============================================
-- PROCEDIMIENTOS ALMACENADOS
-- =============================================

DELIMITER //
CREATE PROCEDURE sp_reporte_ventas_por_fecha(IN fecha_inicio DATE, IN fecha_fin DATE)
BEGIN
    SELECT 
        v.id_venta,
        v.fecha,
        u.nombres,
        u.apellidos,
        v.total,
        v.metodo_pago,
        v.estado
    FROM ventas v
    INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
    WHERE DATE(v.fecha) BETWEEN fecha_inicio AND fecha_fin
    ORDER BY v.fecha DESC;
END//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_reporte_productos_sin_stock()
BEGIN
    SELECT 
        p.id_producto,
        p.codigo,
        p.nombre,
        p.stock,
        p.stock_minimo,
        c.nombre as categoria
    FROM productos p
    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE p.stock = 0 AND p.estado = 'activo'
    ORDER BY p.nombre;
END//
DELIMITER ;

DELIMITER //
CREATE PROCEDURE sp_reporte_ganancias(IN fecha_inicio DATE, IN fecha_fin DATE)
BEGIN
    SELECT 
        SUM(dv.subtotal) as total_ventas,
        SUM(dv.cantidad * (dv.precio_unitario - 
            (SELECT precio_compra FROM productos WHERE id_producto = dv.id_producto)
        )) as total_ganancia
    FROM detalle_ventas dv
    INNER JOIN ventas v ON dv.id_venta = v.id_venta
    WHERE DATE(v.fecha) BETWEEN fecha_inicio AND fecha_fin
    AND v.estado = 'completada';
END//
DELIMITER ;











