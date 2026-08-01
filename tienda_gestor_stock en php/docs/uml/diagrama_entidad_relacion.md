# Diagrama Entidad-Relación (DER)
## Sistema Web de Ventas e Inventario - Bodega Armando Tornero

### Entidades Principales

```
┌─────────────────────┐
│      ROLES          │
├─────────────────────┤
│ PK id_rol           │
│    nombre_rol       │
│    descripcion      │
│    fecha_creacion   │
│    estado           │
└─────────┬───────────┘
          │
          │ 1
          │
          │ *
┌─────────▼───────────┐
│     USUARIOS        │
├─────────────────────┤
│ PK id_usuario       │
│    nombres          │
│    apellidos        │
│    correo           │
│    usuario          │
│    contraseña       │
│ FK id_rol          │
│    telefono         │
│    direccion        │
│    estado           │
│    fecha_registro   │
│    ultimo_acceso    │
│    intentos_fallidos│
└─────────┬───────────┘
          │
          │ 1
          │
          │ *
┌─────────▼───────────┐
│      VENTAS         │
├─────────────────────┤
│ PK id_venta         │
│ FK id_usuario       │
│ FK id_cliente       │
│    fecha            │
│    subtotal         │
│    igv              │
│    total            │
│    descuento        │
│    metodo_pago      │
│    estado           │
│    observaciones    │
└─────────┬───────────┘
          │
          │ 1
          │
          │ *
┌─────────▼───────────┐
│   DETALLE_VENTAS    │
├─────────────────────┤
│ PK id_detalle       │
│ FK id_venta         │
│ FK id_producto      │
│    cantidad         │
│    precio           │
│    subtotal         │
└─────────────────────┘

┌─────────────────────┐
│    CATEGORIAS       │
├─────────────────────┤
│ PK id_categoria     │
│    nombre           │
│    descripcion      │
│    estado           │
│    fecha_creacion   │
└─────────┬───────────┘
          │
          │ 1
          │
          │ *
┌─────────▼───────────┐
│     PRODUCTOS       │
├─────────────────────┤
│ PK id_producto      │
│    codigo           │
│    codigo_barras    │
│    nombre           │
│    descripcion      │
│    precio_compra    │
│    precio_venta     │
│    stock            │
│    stock_minimo     │
│    imagen           │
│ FK id_categoria     │
│ FK id_proveedor     │
│    estado           │
│    fecha_registro   │
│    fecha_actualizacion│
└─────────┬───────────┘
          │
          │ *
          │
          │ 1
┌─────────▼───────────┐
│    PROVEEDORES      │
├─────────────────────┤
│ PK id_proveedor     │
│    ruc              │
│    razon_social     │
│    nombre_contacto  │
│    telefono         │
│    direccion        │
│    correo           │
│    estado           │
│    fecha_registro   │
└─────────────────────┘

┌─────────────────────┐
│     CLIENTES        │
├─────────────────────┤
│ PK id_cliente       │
│    dni              │
│    nombres          │
│    apellidos        │
│    telefono         │
│    direccion        │
│    correo           │
│    estado           │
│    fecha_registro   │
└─────────────────────┘

┌─────────────────────┐
│MOVIMIENTOS_INVENTARIO│
├─────────────────────┤
│ PK id_movimiento    │
│ FK id_producto      │
│    tipo_movimiento  │
│    cantidad         │
│    stock_anterior   │
│    stock_nuevo      │
│    fecha            │
│    observacion      │
│ FK id_usuario       │
│    referencia       │
└─────────────────────┘

┌─────────────────────┐
│   PERMISOS          │
├─────────────────────┤
│ PK id_permiso       │
│    nombre_permiso   │
│    descripcion      │
└─────────┬───────────┘
          │
          │ *
          │
          │ *
┌─────────▼───────────┐
│    ROL_PERMISO      │
├─────────────────────┤
│ PK id_rol_permiso   │
│ FK id_rol           │
│ FK id_permiso       │
│    fecha_asignacion │
└─────────────────────┘

┌─────────────────────┐
│   LOGS_ACTIVIDAD    │
├─────────────────────┤
│ PK id_log           │
│ FK id_usuario       │
│    accion           │
│    modulo           │
│    descripcion      │
│    ip_address       │
│    fecha            │
└─────────────────────┘

┌─────────────────────┐
│      CAJA           │
├─────────────────────┤
│ PK id_caja          │
│ FK id_usuario       │
│    fecha_apertura   │
│    fecha_cierre     │
│    monto_apertura   │
│    monto_cierre     │
│    total_ventas     │
│    estado           │
│    observaciones    │
└─────────────────────┘

┌─────────────────────┐
│   CONFIGURACION     │
├─────────────────────┤
│ PK id_config        │
│    clave            │
│    valor            │
│    descripcion      │
│    fecha_actualizacion│
└─────────────────────┘
```

### Relaciones y Cardinalidades

| Tabla Origen | Tabla Destino | Relación | Cardinalidad |
|--------------|---------------|-----------|---------------|
| ROLES | USUARIOS | Uno a Muchos | 1:N |
| USUARIOS | VENTAS | Uno a Muchos | 1:N |
| CLIENTES | VENTAS | Uno a Muchos | 1:N |
| VENTAS | DETALLE_VENTAS | Uno a Muchos | 1:N |
| PRODUCTOS | DETALLE_VENTAS | Uno a Muchos | 1:N |
| CATEGORIAS | PRODUCTOS | Uno a Muchos | 1:N |
| PROVEEDORES | PRODUCTOS | Uno a Muchos | 1:N |
| PRODUCTOS | MOVIMIENTOS_INVENTARIO | Uno a Muchos | 1:N |
| USUARIOS | MOVIMIENTOS_INVENTARIO | Uno a Muchos | 1:N |
| ROLES | ROL_PERMISO | Uno a Muchos | 1:N |
| PERMISOS | ROL_PERMISO | Uno a Muchos | 1:N |
| USUARIOS | LOGS_ACTIVIDAD | Uno a Muchos | 1:N |
| USUARIOS | CAJA | Uno a Muchos | 1:N |

### Normalización

**Primera Forma Normal (1NF)**:
- Todos los atributos son atómicos
- No hay grupos repetitivos
- Cada tabla tiene una clave primaria única

**Segunda Forma Normal (2NF)**:
- Cumple con 1NF
- Todos los atributos no clave dependen completamente de la clave primaria
- No hay dependencias parciales

**Tercera Forma Normal (3NF)**:
- Cumple con 2NF
- No hay dependencias transitivas
- Todos los atributos no clave dependen solo de la clave primaria

### Índices

**Índices Primarios**:
- Todas las tablas tienen índice en la clave primaria

**Índices Secundarios**:
- productos: id_categoria, id_proveedor, estado
- ventas: id_usuario, fecha, id_cliente
- detalle_ventas: id_venta, id_producto
- movimientos_inventario: id_producto, fecha
- logs_actividad: id_usuario, fecha

### Restricciones de Integridad

**Foreign Keys**:
- usuarios.id_rol → roles.id_rol
- ventas.id_usuario → usuarios.id_usuario
- ventas.id_cliente → clientes.id_cliente
- detalle_ventas.id_venta → ventas.id_venta
- detalle_ventas.id_producto → productos.id_producto
- productos.id_categoria → categorias.id_categoria
- productos.id_proveedor → proveedores.id_proveedor
- movimientos_inventario.id_producto → productos.id_producto
- movimientos_inventario.id_usuario → usuarios.id_usuario
- rol_permiso.id_rol → roles.id_rol
- rol_permiso.id_permiso → permisos.id_permiso
- logs_actividad.id_usuario → usuarios.id_usuario
- caja.id_usuario → usuarios.id_usuario

**Reglas de Eliminación**:
- ON DELETE RESTRICT: usuarios, categorías
- ON DELETE CASCADE: ventas (detalle_ventas), roles (rol_permiso)
- ON DELETE SET NULL: productos (id_proveedor)
