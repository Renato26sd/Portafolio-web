# Diagrama de Clases
## Sistema Web de Ventas e Inventario - Bodega Armando Tornero

### Estructura de Clases

```
┌─────────────────────────────────────────────────────────────────┐
│                        Model Layer                               │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│     Usuario     │
├──────────────────┤
│ - id_usuario    │
│ - nombres       │
│ - apellidos      │
│ - correo        │
│ - usuario       │
│ - contraseña    │
│ - id_rol        │
│ - estado        │
│ - fecha_registro│
├──────────────────┤
│ + autenticar()  │
│ + obtenerTodos()│
│ + obtenerPorId()│
│ + crear()       │
│ + actualizar()  │
│ + eliminar()    │
│ + cambiarEstado()│
└──────────────────┘
         │
         │ 1
         │
         │ *
┌────────▼──────────┐
│       Rol         │
├───────────────────┤
│ - id_rol          │
│ - nombre_rol      │
│ - descripcion     │
│ - fecha_creacion  │
│ - estado          │
├───────────────────┤
│ + obtenerTodos()  │
│ + obtenerPorId()  │
│ + crear()         │
│ + actualizar()   │
│ + eliminar()     │
│ + obtenerPermisos()│
│ + asignarPermisos()│
└───────────────────┘

┌──────────────────┐
│    Producto      │
├──────────────────┤
│ - id_producto    │
│ - codigo         │
│ - codigo_barras  │
│ - nombre         │
│ - descripcion    │
│ - precio_compra  │
│ - precio_venta   │
│ - stock          │
│ - stock_minimo   │
│ - imagen         │
│ - id_categoria   │
│ - id_proveedor   │
│ - estado         │
├──────────────────┤
│ + obtenerTodos() │
│ + obtenerPorId() │
│ + obtenerPorCodigo()│
│ + buscarParaVenta()│
│ + crear()        │
│ + actualizar()   │
│ + eliminar()     │
│ + actualizarStock()│
│ + obtenerBajoStock()│
│ + obtenerSinStock()│
└──────────────────┘
         │
         │ *
         │
         │ 1
┌────────▼──────────┐      ┌──────────────────┐
│    Categoria      │      │    Proveedor     │
├───────────────────┤      ├──────────────────┤
│ - id_categoria    │      │ - id_proveedor   │
│ - nombre          │      │ - ruc            │
│ - descripcion     │      │ - razon_social   │
│ - estado          │      │ - nombre_contacto│
├───────────────────┤      │ - telefono       │
│ + obtenerTodos()  │      │ - direccion      │
│ + obtenerPorId()  │      │ - correo         │
│ + crear()         │      │ - estado         │
│ + actualizar()    │      ├──────────────────┤
│ + eliminar()      │      │ + obtenerTodos() │
│ + existeNombre()  │      │ + obtenerPorId() │
│ + contarProductos()│     │ + crear()        │
└───────────────────┘      │ + actualizar()   │
                           │ + eliminar()     │
                           │ + existeRuc()    │
                           │ + contarProductos()│
                           └──────────────────┘

┌──────────────────┐
│      Venta       │
├──────────────────┤
│ - id_venta       │
│ - id_usuario     │
│ - id_cliente     │
│ - fecha          │
│ - subtotal       │
│ - igv            │
│ - total          │
│ - descuento      │
│ - metodo_pago    │
│ - estado         │
│ - observaciones  │
├──────────────────┤
│ + obtenerTodos() │
│ + obtenerPorId() │
│ + obtenerDetalles()│
│ + crear()        │
│ + anular()       │
│ + obtenerVentasDia()│
│ + obtenerTotalDia()│
│ + obtenerPorRangoFechas()│
└──────────────────┘
         │
         │ 1
         │
         │ *
┌────────▼──────────┐
│  DetalleVenta     │
├───────────────────┤
│ - id_detalle      │
│ - id_venta        │
│ - id_producto     │
│ - cantidad        │
│ - precio          │
│ - subtotal        │
└───────────────────┘

┌──────────────────┐
│MovimientoInventario│
├──────────────────┤
│ - id_movimiento  │
│ - id_producto    │
│ - tipo_movimiento│
│ - cantidad       │
│ - stock_anterior │
│ - stock_nuevo    │
│ - fecha          │
│ - observacion    │
│ - id_usuario     │
│ - referencia     │
├──────────────────┤
│ + obtenerTodos() │
│ + obtenerPorId() │
│ + crear()        │
│ + obtenerPorProducto()│
│ + obtenerKardex()│
└──────────────────┘

┌──────────────────┐
│     Cliente      │
├──────────────────┤
│ - id_cliente     │
│ - dni            │
│ - nombres        │
│ - apellidos      │
│ - telefono       │
│ - direccion      │
│ - correo         │
│ - estado         │
├──────────────────┤
│ + obtenerTodos() │
│ + obtenerPorId() │
│ + buscarPorDni() │
│ + buscar()       │
│ + crear()        │
│ + actualizar()   │
│ + eliminar()     │
└──────────────────┘

┌──────────────────┐
│     Reporte      │
├──────────────────┤
├──────────────────┤
│ + ventasPorFecha()│
│ + productosMasVendidos()│
│ + productosSinStock()│
│ + productosBajoStock()│
│ + ganancias()    │
│ + ventasPorMetodoPago()│
│ + ventasPorVendedor()│
│ + movimientosInventario()│
│ + resumenDashboard()│
│ + ultimasVentas()│
└──────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    Controller Layer                              │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│  AuthController  │
├──────────────────┤
│ + mostrarLogin() │
│ + login()        │
│ + logout()       │
└──────────────────┘

┌──────────────────┐
│DashboardController│
├──────────────────┤
│ + index()        │
└──────────────────┘

┌──────────────────┐
│UsuarioController │
├──────────────────┤
│ + index()        │
│ + crear()        │
│ + guardar()      │
│ + editar()       │
│ + actualizar()   │
│ + eliminar()     │
│ + cambiarEstado()│
└──────────────────┘

┌──────────────────┐
│ProductoController│
├──────────────────┤
│ + index()        │
│ + crear()        │
│ + guardar()      │
│ + editar()       │
│ + actualizar()   │
│ + eliminar()     │
│ + buscar()       │
└──────────────────┘

┌──────────────────┐
│  VentaController │
├──────────────────┤
│ + index()        │
│ + crear()        │
│ + guardar()      │
│ + ver()          │
│ + anular()       │
│ + ticket()       │
└──────────────────┘

┌──────────────────┐
│InventarioController│
├──────────────────┤
│ + index()        │
│ + registrar()    │
│ + guardar()      │
│ + kardex()       │
└──────────────────┘

┌──────────────────┐
│ReporteController │
├──────────────────┤
│ + index()        │
│ + exportarExcel()│
└──────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                      Helper Layer                               │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│     Session      │
├──────────────────┤
│ + start()        │
│ + set()          │
│ + get()          │
│ + has()          │
│ + remove()       │
│ + destroy()      │
│ + regenerate()   │
│ + setFlash()     │
│ + getFlash()     │
│ + isLoggedIn()   │
│ + getUser()      │
│ + getUserId()    │
│ + getUserRole()  │
│ + hasPermission()│
└──────────────────┘

┌──────────────────┐
│    Validator     │
├──────────────────┤
│ + required()     │
│ + email()        │
│ + minLength()    │
│ + maxLength()    │
│ + numeric()      │
│ + integer()      │
│ + positive()     │
│ + matches()      │
│ + unique()       │
│ + inArray()      │
│ + date()         │
│ + file()         │
│ + image()        │
│ + maxSize()      │
│ + passes()       │
│ + fails()        │
│ + getErrors()    │
└──────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                   Middleware Layer                               │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────┐
│ AuthMiddleware   │
├──────────────────┤
│ + handle()       │
│ + guest()        │
│ + permission()   │
│ + role()         │
│ + roles()        │
└──────────────────┘

┌──────────────────┐
│ CsrfMiddleware   │
├──────────────────┤
│ + generateToken()│
│ + verifyToken()  │
│ + getField()     │
│ + validateRequest()│
└──────────────────┘
```

### Relaciones entre Clases

**Herencia**: No hay herencia directa en este diseño, se usa composición.

**Asociaciones**:
- Usuario → Rol (Muchos a Uno)
- Producto → Categoria (Muchos a Uno)
- Producto → Proveedor (Muchos a Uno)
- Venta → Usuario (Muchos a Uno)
- Venta → Cliente (Muchos a Uno)
- Venta → DetalleVenta (Uno a Muchos)
- DetalleVenta → Producto (Muchos a Uno)
- MovimientoInventario → Producto (Muchos a Uno)
- MovimientoInventario → Usuario (Muchos a Uno)

**Dependencias**:
- Controllers dependen de Models
- Models dependen de Database
- Controllers dependen de Helpers
- Controllers dependen de Middlewares
- Views dependen de Helpers
