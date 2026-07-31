"""
Sistema de Facturación Básica
Permite crear y gestionar facturas de ventas
"""

from datetime import datetime


class Producto:
    """Clase que representa un producto"""
    
    def __init__(self, codigo, nombre, precio, stock=0):
        self.codigo = codigo
        self.nombre = nombre
        self.precio = precio
        self.stock = stock
    
    def __str__(self):
        return f"{self.codigo} - {self.nombre} - S/ {self.precio:.2f} (Stock: {self.stock})"


class ItemFactura:
    """Clase que representa un item en una factura"""
    
    def __init__(self, producto, cantidad):
        self.producto = producto
        self.cantidad = cantidad
    
    @property
    def subtotal(self):
        return self.producto.precio * self.cantidad
    
    def __str__(self):
        return f"{self.producto.nombre} x{self.cantidad} = S/ {self.subtotal:.2f}"


class Factura:
    """Clase que representa una factura"""
    
    def __init__(self, numero, cliente):
        self.numero = numero
        self.cliente = cliente
        self.fecha = datetime.now()
        self.items = []
        self.igv_porcentaje = 0.18  # 18% IGV en Perú
    
    def agregar_item(self, producto, cantidad):
        """Agrega un item a la factura"""
        if producto.stock >= cantidad:
            item = ItemFactura(producto, cantidad)
            self.items.append(item)
            producto.stock -= cantidad
            return True
        return False
    
    @property
    def subtotal(self):
        """Calcula el subtotal de la factura"""
        return sum(item.subtotal for item in self.items)
    
    @property
    def igv(self):
        """Calcula el IGV de la factura"""
        return self.subtotal * self.igv_porcentaje
    
    @property
    def total(self):
        """Calcula el total de la factura"""
        return self.subtotal + self.igv
    
    def mostrar_factura(self):
        """Muestra el detalle de la factura"""
        print("\n" + "=" * 60)
        print(f"FACTURA N° {self.numero}")
        print("=" * 60)
        print(f"Fecha: {self.fecha.strftime('%Y-%m-%d %H:%M:%S')}")
        print(f"Cliente: {self.cliente}")
        print("-" * 60)
        print("ITEMS:")
        for item in self.items:
            print(f"  {item}")
        print("-" * 60)
        print(f"Subtotal: S/ {self.subtotal:.2f}")
        print(f"IGV (18%): S/ {self.igv:.2f}")
        print(f"TOTAL: S/ {self.total:.2f}")
        print("=" * 60)


class SistemaFacturacion:
    """Clase que gestiona el sistema de facturación"""
    
    def __init__(self):
        self.productos = {}
        self.facturas = {}
        self.contador_facturas = 1
    
    def agregar_producto(self, codigo, nombre, precio, stock):
        """Agrega un producto al catálogo"""
        if codigo not in self.productos:
            self.productos[codigo] = Producto(codigo, nombre, precio, stock)
            return True
        return False
    
    def obtener_producto(self, codigo):
        """Obtiene un producto por su código"""
        return self.productos.get(codigo)
    
    def listar_productos(self):
        """Lista todos los productos disponibles"""
        print("\n=== Catálogo de Productos ===")
        for producto in self.productos.values():
            print(producto)
    
    def crear_factura(self, cliente):
        """Crea una nueva factura"""
        numero = f"F-{str(self.contador_facturas).zfill(6)}"
        factura = Factura(numero, cliente)
        self.facturas[numero] = factura
        self.contador_facturas += 1
        return factura
    
    def obtener_factura(self, numero):
        """Obtiene una factura por su número"""
        return self.facturas.get(numero)


def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')


def mostrar_menu():
    """Muestra el menú principal"""
    print("\n" + "=" * 60)
    print("      SISTEMA DE FACTURACIÓN")
    print("=" * 60)
    print("1. Agregar producto")
    print("2. Listar productos")
    print("3. Crear factura")
    print("4. Ver factura")
    print("5. Listar todas las facturas")
    print("0. Salir")
    print("=" * 60)


def main():
    """Función principal del sistema de facturación"""
    sistema = SistemaFacturacion()
    
    # Agregar algunos productos de ejemplo
    sistema.agregar_producto("P001", "Laptop HP", 2500, 10)
    sistema.agregar_producto("P002", "Mouse Inalámbrico", 50, 50)
    sistema.agregar_producto("P003", "Teclado Mecánico", 150, 25)
    sistema.agregar_producto("P004", "Monitor 24\"", 400, 15)
    sistema.agregar_producto("P005", "Impresora Epson", 350, 8)
    
    print("=== SISTEMA DE FACTURACIÓN ===")
    print("Bienvenido al Sistema de Facturación")
    
    while True:
        mostrar_menu()
        
        try:
            opcion = int(input("\nSeleccione una opción (0-5): "))
            
            if opcion == 0:
                print("\n¡Gracias por usar el Sistema de Facturación! ¡Hasta pronto!")
                break
            
            elif opcion == 1:
                # Agregar producto
                print("\n--- Agregar Producto ---")
                codigo = input("Código del producto: ")
                if codigo in sistema.productos:
                    print("Error: Ya existe un producto con ese código.")
                else:
                    nombre = input("Nombre del producto: ")
                    precio = float(input("Precio: "))
                    stock = int(input("Stock inicial: "))
                    
                    if sistema.agregar_producto(codigo, nombre, precio, stock):
                        print("Producto agregado exitosamente.")
                    else:
                        print("Error al agregar el producto.")
            
            elif opcion == 2:
                # Listar productos
                sistema.listar_productos()
            
            elif opcion == 3:
                # Crear factura
                print("\n--- Crear Nueva Factura ---")
                cliente = input("Nombre del cliente: ")
                factura = sistema.crear_factura(cliente)
                
                print(f"\nFactura creada: {factura.numero}")
                print("Productos disponibles:")
                sistema.listar_productos()
                
                while True:
                    codigo = input("\nCódigo del producto (o 'fin' para terminar): ")
                    if codigo.lower() == 'fin':
                        break
                    
                    producto = sistema.obtener_producto(codigo)
                    if producto:
                        cantidad = int(input(f"Cantidad (Stock: {producto.stock}): "))
                        if factura.agregar_item(producto, cantidad):
                            print(f"Item agregado: {producto.nombre} x{cantidad}")
                        else:
                            print("Error: Stock insuficiente.")
                    else:
                        print("Producto no encontrado.")
                
                if factura.items:
                    factura.mostrar_factura()
                else:
                    print("Factura vacía. No se guardará.")
                    del sistema.facturas[factura.numero]
            
            elif opcion == 4:
                # Ver factura
                print("\n--- Ver Factura ---")
                numero = input("Número de factura: ")
                factura = sistema.obtener_factura(numero)
                
                if factura:
                    factura.mostrar_factura()
                else:
                    print("Factura no encontrada.")
            
            elif opcion == 5:
                # Listar facturas
                print("\n=== Lista de Facturas ===")
                for factura in sistema.facturas.values():
                    print(f"{factura.numero} - {factura.cliente} - {factura.fecha.strftime('%Y-%m-%d')} - Total: S/ {factura.total:.2f}")
            
            else:
                print("\nOpción no válida. Por favor seleccione una opción entre 0 y 5.")
            
            input("\nPresione Enter para continuar...")
            limpiar_pantalla()
        
        except ValueError:
            print("\nError: Por favor ingrese un número válido.")
            input("Presione Enter para continuar...")
            limpiar_pantalla()


if __name__ == "__main__":
    main()
