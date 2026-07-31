# Tienda Virtual

Una aplicación web de comercio electrónico completa con catálogo de productos, carrito de compras y panel de administración.

## Características

- **Catálogo de productos**: Visualización de productos con imágenes, precios y stock
- **Carrito de compras**: Agregar, eliminar y modificar cantidad de productos
- **Sistema de pedidos**: Procesamiento de pedidos con información del cliente
- **Dashboard administrativo**: 
  - Estadísticas en tiempo real (productos, pedidos, ingresos)
  - Gestión completa de productos (crear, editar, eliminar)
  - Gestión de pedidos (ver detalles, cambiar estado)
- **Base de datos SQLite**: Almacenamiento persistente de datos

## Tecnologías

- **Backend**: Flask (Python)
- **Frontend**: HTML, JavaScript, TailwindCSS
- **Base de datos**: SQLite con SQLAlchemy
- **API**: REST API con CORS habilitado

## Instalación

1. Crear un entorno virtual (opcional pero recomendado):
```bash
python -m venv venv
source venv/bin/activate  # En Mac/Linux
# o
venv\Scripts\activate  # En Windows
```

2. Instalar las dependencias:
```bash
pip install -r requirements.txt
```

## Uso

1. Iniciar el servidor backend:
```bash
python app.py
```

El servidor se iniciará en `http://localhost:5000`

2. Abrir la tienda en el navegador:
- **Tienda**: Abre `frontend/index.html` en tu navegador
- **Dashboard**: Abre `frontend/dashboard.html` en tu navegador

## Estructura del proyecto

```
tienda_virtual/
├── app.py                 # Servidor Flask con API REST
├── tienda.db              # Base de datos SQLite (se crea automáticamente)
├── requirements.txt       # Dependencias de Python
├── README.md             # Este archivo
└── frontend/
    ├── index.html        # Página principal de la tienda
    ├── app.js            # Lógica de la tienda
    ├── dashboard.html    # Panel de administración
    └── dashboard.js      # Lógica del dashboard
```

## API Endpoints

### Productos
- `GET /api/products` - Obtener todos los productos
- `GET /api/products/<id>` - Obtener un producto específico
- `POST /api/products` - Crear nuevo producto
- `PUT /api/products/<id>` - Actualizar producto
- `DELETE /api/products/<id>` - Eliminar producto

### Pedidos
- `GET /api/orders` - Obtener todos los pedidos
- `GET /api/orders/<id>` - Obtener un pedido específico
- `POST /api/orders` - Crear nuevo pedido
- `PUT /api/orders/<id>/status` - Actualizar estado del pedido

### Estadísticas
- `GET /api/stats` - Obtener estadísticas generales

## Productos de ejemplo

La aplicación incluye automáticamente 8 productos de ejemplo:
- Laptop HP
- Mouse Inalámbrico
- Teclado Mecánico
- Monitor 24"
- Auriculares Bluetooth
- Cámara Web HD
- Disco SSD 1TB
- Memoria RAM 16GB

## Estados de pedidos

- `pending` - Pendiente
- `processing` - Procesando
- `completed` - Completado
- `cancelled` - Cancelado

## Personalización

Puedes personalizar la aplicación modificando:
- **Colores y estilos**: Editar las clases de TailwindCSS en los archivos HTML
- **Productos**: Agregar o modificar productos desde el dashboard
- **Categorías**: Las categorías se gestionan desde el dashboard al crear/editar productos

## Notas

- La base de datos se crea automáticamente al iniciar la aplicación
- Los productos de ejemplo se agregan solo si no existen productos en la base de datos
- La aplicación usa CORS para permitir comunicación entre el frontend y backend
