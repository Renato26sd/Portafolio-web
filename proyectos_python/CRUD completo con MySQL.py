"""
CRUD Completo con MySQL
Sistema de gestión de productos con base de datos MySQL
"""

import mysql.connector
from mysql.connector import Error
from datetime import datetime


class DatabaseManager:
    """Clase que gestiona la conexión a la base de datos MySQL"""
    
    def __init__(self, host='localhost', database='crud_db', user='root', password=''):
        self.host = host
        self.database = database
        self.user = user
        self.password = password
        self.connection = None
    
    def conectar(self):
        """Establece conexión con la base de datos"""
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                database=self.database,
                user=self.user,
                password=self.password
            )
            if self.connection.is_connected():
                print("Conexión exitosa a MySQL")
                return True
        except Error as e:
            print(f"Error al conectar a MySQL: {e}")
            return False
    
    def desconectar(self):
        """Cierra la conexión con la base de datos"""
        if self.connection and self.connection.is_connected():
            self.connection.close()
            print("Conexión cerrada")
    
    def crear_base_datos(self):
        """Crea la base de datos si no existe"""
        try:
            connection = mysql.connector.connect(
                host=self.host,
                user=self.user,
                password=self.password
            )
            cursor = connection.cursor()
            cursor.execute(f"CREATE DATABASE IF NOT EXISTS {self.database}")
            cursor.execute(f"USE {self.database}")
            connection.commit()
            cursor.close()
            connection.close()
            print(f"Base de datos '{self.database}' creada o verificada")
            return True
        except Error as e:
            print(f"Error al crear base de datos: {e}")
            return False
    
    def crear_tabla_productos(self):
        """Crea la tabla de productos si no existe"""
        try:
            cursor = self.connection.cursor()
            cursor.execute("""
                CREATE TABLE IF NOT EXISTS productos (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nombre VARCHAR(100) NOT NULL,
                    descripcion TEXT,
                    precio DECIMAL(10, 2) NOT NULL,
                    stock INT DEFAULT 0,
                    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            """)
            self.connection.commit()
            cursor.close()
            print("Tabla 'productos' creada o verificada")
            return True
        except Error as e:
            print(f"Error al crear tabla: {e}")
            return False


class ProductoCRUD:
    """Clase que implementa operaciones CRUD para productos"""
    
    def __init__(self, db_manager):
        self.db = db_manager
    
    def crear(self, nombre, descripcion, precio, stock):
        """Crea un nuevo producto (CREATE)"""
        try:
            cursor = self.db.connection.cursor()
            query = "INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (%s, %s, %s, %s)"
            values = (nombre, descripcion, precio, stock)
            cursor.execute(query, values)
            self.db.connection.commit()
            producto_id = cursor.lastrowid
            cursor.close()
            print(f"Producto creado con ID: {producto_id}")
            return producto_id
        except Error as e:
            print(f"Error al crear producto: {e}")
            return None
    
    def leer_todos(self):
        """Lee todos los productos (READ - All)"""
        try:
            cursor = self.db.connection.cursor(dictionary=True)
            cursor.execute("SELECT * FROM productos ORDER BY id")
            productos = cursor.fetchall()
            cursor.close()
            return productos
        except Error as e:
            print(f"Error al leer productos: {e}")
            return []
    
    def leer_por_id(self, producto_id):
        """Lee un producto por ID (READ - One)"""
        try:
            cursor = self.db.connection.cursor(dictionary=True)
            query = "SELECT * FROM productos WHERE id = %s"
            cursor.execute(query, (producto_id,))
            producto = cursor.fetchone()
            cursor.close()
            return producto
        except Error as e:
            print(f"Error al leer producto: {e}")
            return None
    
    def actualizar(self, producto_id, nombre=None, descripcion=None, precio=None, stock=None):
        """Actualiza un producto existente (UPDATE)"""
        try:
            cursor = self.db.connection.cursor()
            
            # Construir query dinámico
            updates = []
            values = []
            
            if nombre is not None:
                updates.append("nombre = %s")
                values.append(nombre)
            if descripcion is not None:
                updates.append("descripcion = %s")
                values.append(descripcion)
            if precio is not None:
                updates.append("precio = %s")
                values.append(precio)
            if stock is not None:
                updates.append("stock = %s")
                values.append(stock)
            
            if not updates:
                print("No se proporcionaron campos para actualizar")
                return False
            
            values.append(producto_id)
            query = f"UPDATE productos SET {', '.join(updates)} WHERE id = %s"
            
            cursor.execute(query, values)
            self.db.connection.commit()
            filas_afectadas = cursor.rowcount
            cursor.close()
            
            if filas_afectadas > 0:
                print(f"Producto ID {producto_id} actualizado exitosamente")
                return True
            else:
                print(f"No se encontró producto con ID {producto_id}")
                return False
        except Error as e:
            print(f"Error al actualizar producto: {e}")
            return False
    
    def eliminar(self, producto_id):
        """Elimina un producto (DELETE)"""
        try:
            cursor = self.db.connection.cursor()
            query = "DELETE FROM productos WHERE id = %s"
            cursor.execute(query, (producto_id,))
            self.db.connection.commit()
            filas_afectadas = cursor.rowcount
            cursor.close()
            
            if filas_afectadas > 0:
                print(f"Producto ID {producto_id} eliminado exitosamente")
                return True
            else:
                print(f"No se encontró producto con ID {producto_id}")
                return False
        except Error as e:
            print(f"Error al eliminar producto: {e}")
            return False
    
    def buscar_por_nombre(self, nombre):
        """Busca productos por nombre"""
        try:
            cursor = self.db.connection.cursor(dictionary=True)
            query = "SELECT * FROM productos WHERE nombre LIKE %s"
            cursor.execute(query, (f"%{nombre}%",))
            productos = cursor.fetchall()
            cursor.close()
            return productos
        except Error as e:
            print(f"Error al buscar productos: {e}")
            return []
    
    def buscar_por_precio(self, precio_min, precio_max):
        """Busca productos en un rango de precios"""
        try:
            cursor = self.db.connection.cursor(dictionary=True)
            query = "SELECT * FROM productos WHERE precio BETWEEN %s AND %s ORDER BY precio"
            cursor.execute(query, (precio_min, precio_max))
            productos = cursor.fetchall()
            cursor.close()
            return productos
        except Error as e:
            print(f"Error al buscar productos: {e}")
            return []


def mostrar_producto(producto):
    """Muestra un producto de forma formateada"""
    if producto:
        print(f"ID: {producto['id']}")
        print(f"Nombre: {producto['nombre']}")
        print(f"Descripción: {producto['descripcion'] or 'N/A'}")
        print(f"Precio: S/ {producto['precio']:.2f}")
        print(f"Stock: {producto['stock']}")
        print(f"Fecha creación: {producto['fecha_creacion']}")
        print("-" * 40)


def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')


def mostrar_menu():
    """Muestra el menú principal"""
    print("\n" + "=" * 60)
    print("   CRUD DE PRODUCTOS CON MYSQL")
    print("=" * 60)
    print("1. Crear producto")
    print("2. Leer todos los productos")
    print("3. Leer producto por ID")
    print("4. Actualizar producto")
    print("5. Eliminar producto")
    print("6. Buscar por nombre")
    print("7. Buscar por rango de precio")
    print("0. Salir")
    print("=" * 60)


def main():
    """Función principal del sistema CRUD"""
    print("=== SISTEMA CRUD CON MYSQL ===")
    
    # Configuración de la base de datos
    print("\nConfiguración de la base de datos:")
    host = input("Host (default: localhost): ") or "localhost"
    user = input("Usuario MySQL (default: root): ") or "root"
    password = input("Contraseña MySQL (presione Enter si no tiene): ")
    database = input("Nombre de la base de datos (default: crud_db): ") or "crud_db"
    
    # Crear gestor de base de datos
    db = DatabaseManager(host, database, user, password)
    
    # Crear base de datos y tabla
    print("\nInicializando base de datos...")
    db.crear_base_datos()
    
    if not db.conectar():
        print("No se pudo conectar a la base de datos. Verifique sus credenciales.")
        return
    
    if not db.crear_tabla_productos():
        print("Error al crear la tabla de productos.")
        db.desconectar()
        return
    
    # Crear objeto CRUD
    crud = ProductoCRUD(db)
    
    while True:
        mostrar_menu()
        
        try:
            opcion = int(input("\nSeleccione una opción (0-7): "))
            
            if opcion == 0:
                print("\n¡Gracias por usar el Sistema CRUD! ¡Hasta pronto!")
                break
            
            elif opcion == 1:
                # Crear producto
                print("\n--- Crear Nuevo Producto ---")
                nombre = input("Nombre: ")
                descripcion = input("Descripción: ")
                precio = float(input("Precio: "))
                stock = int(input("Stock: "))
                
                producto_id = crud.crear(nombre, descripcion, precio, stock)
                if producto_id:
                    print("Producto creado exitosamente.")
            
            elif opcion == 2:
                # Leer todos
                print("\n--- Todos los Productos ---")
                productos = crud.leer_todos()
                if productos:
                    for producto in productos:
                        mostrar_producto(producto)
                else:
                    print("No hay productos registrados.")
            
            elif opcion == 3:
                # Leer por ID
                print("\n--- Leer Producto por ID ---")
                producto_id = int(input("ID del producto: "))
                producto = crud.leer_por_id(producto_id)
                if producto:
                    mostrar_producto(producto)
                else:
                    print("Producto no encontrado.")
            
            elif opcion == 4:
                # Actualizar
                print("\n--- Actualizar Producto ---")
                producto_id = int(input("ID del producto a actualizar: "))
                
                print("Deje en blanco para mantener el valor actual")
                nombre = input(f"Nombre: ") or None
                descripcion = input(f"Descripción: ") or None
                
                precio_input = input("Precio: ")
                precio = float(precio_input) if precio_input else None
                
                stock_input = input("Stock: ")
                stock = int(stock_input) if stock_input else None
                
                if crud.actualizar(producto_id, nombre, descripcion, precio, stock):
                    print("Producto actualizado exitosamente.")
            
            elif opcion == 5:
                # Eliminar
                print("\n--- Eliminar Producto ---")
                producto_id = int(input("ID del producto a eliminar: "))
                confirmacion = input("¿Está seguro? (s/n): ")
                if confirmacion.lower() == 's':
                    if crud.eliminar(producto_id):
                        print("Producto eliminado exitosamente.")
                    else:
                        print("No se pudo eliminar el producto.")
                else:
                    print("Operación cancelada.")
            
            elif opcion == 6:
                # Buscar por nombre
                print("\n--- Buscar por Nombre ---")
                nombre = input("Nombre a buscar: ")
                productos = crud.buscar_por_nombre(nombre)
                if productos:
                    for producto in productos:
                        mostrar_producto(producto)
                else:
                    print("No se encontraron productos.")
            
            elif opcion == 7:
                # Buscar por precio
                print("\n--- Buscar por Rango de Precio ---")
                precio_min = float(input("Precio mínimo: "))
                precio_max = float(input("Precio máximo: "))
                productos = crud.buscar_por_precio(precio_min, precio_max)
                if productos:
                    for producto in productos:
                        mostrar_producto(producto)
                else:
                    print("No se encontraron productos en ese rango.")
            
            else:
                print("\nOpción no válida. Por favor seleccione una opción entre 0 y 7.")
            
            input("\nPresione Enter para continuar...")
            limpiar_pantalla()
        
        except ValueError:
            print("\nError: Por favor ingrese un número válido.")
            input("Presione Enter para continuar...")
            limpiar_pantalla()
    
    # Cerrar conexión al salir
    db.desconectar()


if __name__ == "__main__":
    main()
