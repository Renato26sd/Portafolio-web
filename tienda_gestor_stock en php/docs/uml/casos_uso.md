# Diagrama de Casos de Uso
## Sistema Web de Ventas e Inventario - Bodega Armando Tornero

### Actores
- **Administrador**: Usuario con acceso total al sistema
- **Vendedor**: Usuario encargado de registrar ventas
- **Cajero**: Usuario encargado de procesar pagos
- **Almacenero**: Usuario encargado de gestionar el inventario
- **Sistema**: El sistema automatizado

### Casos de Uso Principales

#### 1. Gestionar Autenticación
- **Actor**: Todos los usuarios
- **Descripción**: Permitir a los usuarios iniciar y cerrar sesión en el sistema
- **Flujo Principal**:
  1. El usuario ingresa sus credenciales
  2. El sistema valida las credenciales
  3. El sistema otorga acceso según el rol del usuario
- **Precondiciones**: El usuario debe estar registrado en el sistema
- **Postcondiciones**: El usuario accede al sistema con sus permisos correspondientes

#### 2. Gestionar Usuarios (CU-01)
- **Actor**: Administrador
- **Descripción**: Crear, editar, eliminar y asignar roles a usuarios
- **Flujo Principal**:
  1. El administrador accede al módulo de usuarios
  2. El administrador selecciona la acción (crear/editar/eliminar)
  3. El administrador ingresa los datos del usuario
  4. El sistema guarda los cambios
- **Precondiciones**: El administrador debe estar autenticado
- **Postcondiciones**: Los datos del usuario son actualizados en la base de datos

#### 3. Gestionar Productos (CU-02)
- **Actor**: Administrador
- **Descripción**: Registrar, editar, eliminar y consultar productos
- **Flujo Principal**:
  1. El administrador accede al módulo de productos
  2. El administrador selecciona la acción deseada
  3. El administrador ingresa los datos del producto
  4. El sistema valida y guarda la información
- **Precondiciones**: El administrador debe estar autenticado
- **Postcondiciones**: El producto es actualizado en el inventario

#### 4. Registrar Venta (CU-03)
- **Actor**: Vendedor
- **Descripción**: Registrar una nueva venta de productos
- **Flujo Principal**:
  1. El vendedor accede al módulo de ventas
  2. El vendedor busca y agrega productos al carrito
  3. El vendedor selecciona el método de pago
  4. El sistema calcula totales y registra la venta
  5. El sistema actualiza el stock automáticamente
- **Precondiciones**: El vendedor debe estar autenticado, los productos deben tener stock
- **Postcondiciones**: La venta es registrada y el stock es actualizado

#### 5. Gestionar Inventario (CU-04)
- **Actor**: Almacenero
- **Descripción**: Registrar entradas y salidas de productos
- **Flujo Principal**:
  1. El almacenero accede al módulo de inventario
  2. El almacenero selecciona el producto
  3. El almacenero indica el tipo de movimiento (entrada/salida)
  4. El almacenero ingresa la cantidad y observaciones
  5. El sistema actualiza el stock y registra el movimiento
- **Precondiciones**: El almacenero debe estar autenticado
- **Postcondiciones**: El stock del producto es actualizado

#### 6. Generar Reportes (CU-05)
- **Actor**: Administrador
- **Descripción**: Generar reportes de ventas, productos e inventario
- **Flujo Principal**:
  1. El administrador accede al módulo de reportes
  2. El administrador selecciona el tipo de reporte
  3. El administrador define el rango de fechas
  4. El sistema genera y muestra el reporte
  5. El administrador puede exportar el reporte
- **Precondiciones**: El administrador debe estar autenticado
- **Postcondiciones**: El reporte es generado y disponible para exportación

#### 7. Gestionar Categorías (CU-06)
- **Actor**: Administrador
- **Descripción**: Crear, editar y eliminar categorías de productos
- **Flujo Principal**:
  1. El administrador accede al módulo de categorías
  2. El administrador realiza la acción deseada
  3. El sistema guarda los cambios
- **Precondiciones**: El administrador debe estar autenticado
- **Postcondiciones**: Las categorías son actualizadas

#### 8. Gestionar Proveedores (CU-07)
- **Actor**: Administrador
- **Descripción**: Registrar, editar y eliminar proveedores
- **Flujo Principal**:
  1. El administrador accede al módulo de proveedores
  2. El administrador realiza la acción deseada
  3. El sistema guarda los cambios
- **Precondiciones**: El administrador debe estar autenticado
- **Postcondiciones**: Los proveedores son actualizados

#### 9. Ver Dashboard (CU-08)
- **Actor**: Todos los usuarios
- **Descripción**: Visualizar el resumen de operaciones del sistema
- **Flujo Principal**:
  1. El usuario accede al dashboard
  2. El sistema muestra estadísticas de ventas, stock y alertas
  3. El usuario puede navegar a otros módulos
- **Precondiciones**: El usuario debe estar autenticado
- **Postcondiciones**: El usuario visualiza la información del dashboard

### Relaciones entre Casos de Uso

```
┌─────────────────────────────────────────────────────────────┐
│                      SISTEMA                                  │
└─────────────────────────────────────────────────────────────┘
         │
         │
    ┌────┴────┐
    │         │
┌───▼───┐ ┌──▼────┐ ┌──────────┐ ┌──────────┐
│Admin  │ │Vendedor│ │  Cajero  │ │Almacenero│
└───┬───┘ └──┬────┘ └────┬─────┘ └────┬─────┘
    │        │           │            │
    │        │           │            │
    ├────────┴───────────┴────────────┤
    │            Autenticación        │
    └─────────────────────────────────┘

Casos de Uso por Rol:

Administrador:
├── Gestionar Usuarios
├── Gestionar Productos
├── Gestionar Categorías
├── Gestionar Proveedores
├── Gestionar Inventario
├── Registrar Ventas
├── Generar Reportes
└── Ver Dashboard

Vendedor:
├── Registrar Ventas
├── Consultar Productos
├── Ver Stock
├── Generar Comprobantes
└── Ver Dashboard

Cajero:
├── Procesar Pagos
├── Verificar Métodos de Pago
├── Generar Tickets
├── Realizar Cierre de Caja
└── Ver Dashboard

Almacenero:
├── Registrar Entradas
├── Actualizar Stock
├── Ver Inventario
├── Ver Productos Agotados
└── Ver Dashboard
```

### Matriz de Permisos

| Caso de Uso | Admin | Vendedor | Cajero | Almacenero |
|-------------|-------|----------|--------|------------|
| Autenticación | ✓ | ✓ | ✓ | ✓ |
| Gestionar Usuarios | ✓ | ✗ | ✗ | ✗ |
| Gestionar Productos | ✓ | ✗ | ✗ | ✗ |
| Gestionar Categorías | ✓ | ✗ | ✗ | ✗ |
| Gestionar Proveedores | ✓ | ✗ | ✗ | ✗ |
| Registrar Ventas | ✓ | ✓ | ✗ | ✗ |
| Gestionar Inventario | ✓ | ✗ | ✗ | ✓ |
| Generar Reportes | ✓ | ✗ | ✗ | ✗ |
| Ver Dashboard | ✓ | ✓ | ✓ | ✓ |
