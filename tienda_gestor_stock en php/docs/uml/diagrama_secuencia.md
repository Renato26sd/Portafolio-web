# Diagrama de Secuencia
## Sistema Web de Ventas e Inventario - Bodega Armando Tornero

### 1. Secuencia de Autenticación

```
Usuario          LoginView      AuthController      UsuarioModel      Database
  │                  │                 │                  │              │
  │--Iniciar sesión-->│                 │                  │              │
  │                  │--Mostrar form-->│                  │              │
  │<--Formulario-----│                 │                  │              │
  │                  │                 │                  │              │
  │--Credenciales---->│                 │                  │              │
  │                  │--login()-------->│                  │              │
  │                  │                 │--autenticar()---->│              │
  │                  │                 │                  │--Query------>│
  │                  │                 │                  │<--Resultado--│
  │                  │                 │<--Datos usuario--│              │
  │                  │                 │--obtenerPermisos()>│              │
  │                  │                 │                  │--Query------>│
  │                  │                 │                  │<--Permisos---│
  │                  │                 │<--Permisos-------│              │
  │                  │<--Redireccionar-│                  │              │
  │<--Dashboard------│                 │                  │              │
```

### 2. Secuencia de Registrar Venta

```
Vendedor       VentaView    VentaController    VentaModel    ProductoModel    Database
  │                │                 │               │               │          │
  │--Nueva venta--->│                 │               │               │          │
  │                │--Mostrar form-->│               │               │          │
  │<--Formulario---│                 │               │               │          │
  │                │                 │               │               │          │
  │--Buscar prod-->│                 │               │               │          │
  │                │--buscar()------>│               │               │          │
  │                │                 │--buscarParaVenta()>│          │          │
  │                │                 │               │--Query------>│          │
  │                │                 │               │<--Productos---│          │
  │                │                 │<--Resultados--│               │          │
  │                │<--Lista prod---│               │               │          │
  │                │                 │               │               │          │
  │--Agregar prod->│                 │               │               │          │
  │                │--Al carrito---->│               │               │          │
  │                │                 │               │               │          │
  │--Completar---->│                 │               │               │          │
  │                │--guardar()----->│               │               │          │
  │                │                 │--crear()------>│               │          │
  │                │                 │               │--BEGIN TRANS-->│          │
  │                │                 │               │--INSERT venta->│          │
  │                │                 │               │<--id_venta----│          │
  │                │                 │               │--INSERT detalles>│          │
  │                │                 │               │--COMMIT------>│          │
  │                │                 │<--id_venta-----│               │          │
  │                │<--Redireccionar-│               │               │          │
  │<--Ticket/View--│                 │               │               │          │
```

### 3. Secuencia de Registrar Movimiento de Inventario

```
Almacenero      InventarioView   InventarioController   MovimientoModel   ProductoModel   Database
  │                  │                    │                    │                │           │
  │--Registrar mov-->│                    │                    │                │           │
  │                  │--Mostrar form----->│                    │                │           │
  │<--Formulario-----│                    │                    │                │           │
  │                  │                    │                    │                │           │
  │--Datos mov------>│                    │                    │                │           │
  │                  │--guardar()-------->│                    │                │           │
  │                  │                    │--crear()---------->│                │           │
  │                  │                    │                    │--BEGIN TRANS-->│           │
  │                  │                    │                    |--actualizarStock()>│           │
  │                  │                    │                    │                |--UPDATE-->│
  │                  │                    │                    │                │<--OK-----│
  │                  │                    │                    |--INSERT mov---->│           │
  │                  │                    │                    │<--OK-----------│           │
  │                  │                    │                    |--COMMIT------>│           │
  │                  │                    │<--Resultado--------│                │           │
  │                  │<--Redireccionar----│                    │                │           │
  │<--Confirmación---│                    │                    │                │           │
```

### 4. Secuencia de Generar Reporte

```
Admin        ReporteView    ReporteController    ReporteModel    Database
  │               │                  │                 │           │
  │--Ver reporte-->│                  │                 │           │
  │               │--Mostrar filtros->│                 │           │
  │<--Filtros------│                  │                 │           │
  │               │                  │                 │           │
  │--Fechas------>│                  │                 │           │
  │               │--index()-------->│                 │           │
  │               │                  │--ventasPorFecha()>│           │
  │               │                  │                 |--Query---->│
  │               │                  │                 │<--Datos----│
  │               │                  │<--Resultados----│           │
  │               │                  |--productosMasVendidos()>│    │
  │               │                  │                 |--Query---->│
  │               │                  │                 │<--Datos----│
  │               │                  │<--Resultados----│           │
  │               │                  |--ganancias()---->│           │
  │               │                  │                 |--Query---->│
  │               │                  │                 │<--Datos----│
  │               │                  │<--Ganancias------│           │
  │               │<--Mostrar datos--│                 │           │
  │<--Reporte------│                  │                 │           │
  │               │                  │                 │           │
  │--Exportar---->│                  │                 │           │
  │               │--exportarExcel()>│                 │           │
  │               │                  |--ventasPorFecha()>│           │
  │               │                  │                 |--Query---->│
  │               │                  │                 │<--Datos----│
  │               │                  │<--CSV-----------│           │
  │<--Archivo CSV--│                  │                 │           │
```

### 5. Secuencia de Gestionar Productos

```
Admin       ProductoView    ProductoController    ProductoModel    Database
  │               │                  │                 │           │
  │--Ver prod---->│                  │                 │           │
  │               │--index()-------->│                 │           │
  │               │                  │--obtenerTodos()->│           │
  │               │                  │                 |--Query---->│
  │               │                  │                 │<--Productos│
  │               │                  │<--Lista---------│           │
  │               │<--Tabla---------│                 │           │
  │<--Lista prod---│                  │                 │           │
  │               │                  │                 │           │
  │--Crear prod-->│                  │                 │           │
  │               │--crear()-------->│                 │           │
  │               │                  │--Mostrar form---│           │
  │<--Formulario---│                  │                 │           │
  │               │                  │                 │           │
  │--Datos prod-->│                  │                 │           │
  │               │--guardar()------>│                 │           │
  │               │                  │--crear()-------->│           │
  │               │                  │                 |--INSERT-->│
  │               │                  │                 │<--ID------│
  │               │                  │<--ID------------│           │
  │               │<--Redireccionar-│                 │           │
  │<--Confirmación│                  │                 │           │
```

### 6. Secuencia de Actualizar Stock (Trigger)

```
VentaController    VentaModel    Database    Trigger    ProductoModel
  │                    │              │           │              │
  │--crear()---------->│              │           │              │
  │                    |--BEGIN TRANS>│           │              │
  │                    |--INSERT venta>│           │              │
  │                    │<--id_venta---│           │              │
  │                    |--INSERT detalles>│       │              │
  │                    │              │           │              │
  │                    │              │<--Trigger activado│         │
  │                    │              │           │              │
  │                    │              │           |--UPDATE stock>│
  │                    │              │           │              │
  │                    │              │           |--INSERT mov inventario>│
  │                    │              │           │              │
  │                    |--COMMIT---->│           │              │
  │                    │<--OK--------│           │              │
  │<--id_venta---------│              │           │              │
```

### Leyenda

- **→**: Mensaje síncrono
- **-->**: Llamada a método
- **<--**: Retorno de valor
- **BEGIN TRANS**: Iniciar transacción
- **COMMIT**: Confirmar transacción
- **INSERT**: Insertar datos
- **UPDATE**: Actualizar datos
- **Query**: Consulta a base de datos
- **Trigger**: Disparador automático
