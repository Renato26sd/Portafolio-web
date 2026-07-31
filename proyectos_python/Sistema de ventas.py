"""
Sistema de Ventas e Inventario
Gestión de productos, ventas e inventario
"""

from datetime import datetime


class Producto:
    """Clase que representa un producto en inventario"""
    
    def __init__(self, codigo, nombre, precio_compra, precio_venta, stock, stock_minimo=10):
        self.codigo = codigo
        self.nombre = nombre
        self.precio_compra = precio_compra
        self.precio_venta = precio_venta
        self.stock = stock
        self.stock_minimo = stock_minimo
    
    @property
    def ganancia_unitaria(self):
        """Calcula la ganancia por unidad"""
        return self.precio_venta - self.precio_compra
    
    @property
    def margen_ganancia(self):
        """Calcula el margen de ganancia en porcentaje"""
        if self.precio_compra > 0:
            return (self.ganancia_unitaria / self.precio_compra) * 100
        return 0
    
    def necesita_restock(self):
        """Verifica si el producto necesita reabastecimiento"""
        return self.stock <= self.stock_minimo
    
    def actualizar_stock(self, cantidad):
        """Actualiza el stock del producto"""
        self.stock += cantidad
    
    def __str__(self):
        return f"{self.codigo} - {self.nombre} - S/ {self.precio_venta:.2f} (Stock: {self.stock})"


class Venta:
    """Clase que representa una venta"""
    
    def __init__(self, numero, cliente):
        self.numero = numero
        self.cliente = cliente
        self.fecha = datetime.now()
        self.items = []
        self.total = 0
    
    def agregar_item(self, producto, cantidad):
        """Agrega un item a la venta"""
        if producto.stock >= cantidad:
            item = {
                "producto": producto,
                "cantidad": cantidad,
                "precio_unitario": producto.precio_venta,
                "subtotal": producto.precio_venta * cantidad
            }
            self.items.append(item)
            self.total += item["subtotal"]
            producto.actualizar_stock(-cantidad)
            return True
        return False
    
    def mostrar_detalle(self):
        """Muestra el detalle de la venta"""
        print("\n" + "=" * 60)
        print(f"VENTA N° {self.numero}")
        print("=" * 60)
        print(f"Cliente: {self.cliente}")
        print(f"Fecha: {self.fecha.strftime('%Y-%m-%d %H:%M:%S')}")
        print("-" * 60)
        print("ITEM\t\tCANT\tPRECIO\tSUBTOTAL")
        print("-" * 60)
        
        for item in self.items:
            prod = item["producto"]
            print(f"{prod.nombre[:15]}\t{item['cantidad']}\tS/ {item['precio_unitario']:.2f}\tS/ {item['subtotal']:.2f}")
        
        print("-" * 60)
        print(f"TOTAL: S/ {self.total:.2f}")
        print("=" * 60)


class SistemaVentas:
    """Clase que gestiona el sistema de ventas e inventario"""
    
    def __init__(self, nombre_tienda):
        self.nombre_tienda = nombre_tienda
        self.productos = {}
        self.ventas = {}
        self.contador_ventas = 1
    
    def agregar_producto(self, codigo, nombre, precio_compra, precio_venta, stock, stock_minimo=10):
        """Agrega un producto al inventario"""
        if codigo not in self.productos:
            producto = Producto(codigo, nombre, precio_compra, precio_venta, stock, stock_minimo)
            self.productos[codigo] = producto
            return True
        return False
    
    def obtener_producto(self, codigo):
        """Obtiene un producto por su código"""
        return self.productos.get(codigo)
    
    def actualizar_producto(self, codigo, **kwargs):
        """Actualiza los datos de un producto"""
        producto = self.obtener_producto(codigo)
        if producto:
            for key, value in kwargs.items():
                if hasattr(producto, key):
                    setattr(producto, key, value)
            return True
        return False
    
    def eliminar_producto(self, codigo):
        """Elimina un producto del inventario"""
        if codigo in self.productos:
            del self.productos[codigo]
            return True
        return False
    
    def listar_productos(self):
        """Lista todos los productos"""
        print(f"\n=== Inventario de {self.nombre_tienda} ===")
        print("-" * 80)
        
        if not self.productos:
            print("No hay productos en el inventario.")
            return
        
        for producto in self.productos.values():
            estado = "⚠️ Stock bajo" if producto.necesita_restock() else "✓"
            print(f"{producto} [{estado}]")
            print(f"   Precio compra: S/ {producto.precio_compra:.2f} | Ganancia: S/ {producto.ganancia_unitaria:.2f} ({producto.margen_ganancia:.1f}%)")
            print("-" * 80)
    
    def productos_bajo_stock(self):
        """Lista productos con stock bajo"""
        print("\n=== Productos con Stock Bajo ===")
        productos_bajos = [p for p in self.productos.values() if p.necesita_restock()]
        
        if not productos_bajos:
            print("Todos los productos tienen stock suficiente.")
            return
        
        for producto in productos_bajos:
            print(f"{producto.codigo} - {producto.nombre} - Stock: {producto.stock} (Mínimo: {producto.stock_minimo})")
    
    def crear_venta(self, cliente):
        """Crea una nueva venta"""
        numero = f"V-{str(self.contador_ventas).zfill(6)}"
        venta = Venta(numero, cliente)
        self.ventas[numero] = venta
        self.contador_ventas += 1
        return venta
    
    def obtener_venta(self, numero):
        """Obtiene una venta por su número"""
        return self.ventas.get(numero)
    
    def listar_ventas(self):
        """Lista todas las ventas"""
        print(f"\n=== Ventas de {self.nombre_tienda} ===")
        print("-" * 80)
        
        if not self.ventas:
            print("No hay ventas registradas.")
            return
        
        for venta in self.ventas.values():
            print(f"{venta.numero} - {venta.cliente} - {venta.fecha.strftime('%Y-%m-%d')} - Total: S/ {venta.total:.2f}")
    
    def reporte_ventas_dia(self):
        """Genera un reporte de ventas del día"""
        hoy = datetime.now().date()
        ventas_hoy = [v for v in self.ventas.values() if v.fecha.date() == hoy]
        
        print(f"\n=== Reporte de Ventas - {hoy} ===")
        print(f"Cantidad de ventas: {len(ventas_hoy)}")
        
        if ventas_hoy:
            total = sum(v.total for v in ventas_hoy)
            print(f"Total vendido: S/ {total:.2f}")
            print(f"Promedio por venta: S/ {total / len(ventas_hoy):.2f}")
    
    def valor_inventario(self):
        """Calcula el valor total del inventario"""
        valor_compra = sum(p.precio_compra * p.stock for p in self.productos.values())
        valor_venta = sum(p.precio_venta * p.stock for p in self.productos.values())
        ganancia_potencial = valor_venta - valor_compra
        
        print(f"\n=== Valor del Inventario ===")
        print(f"Valor de compra: S/ {valor_compra:.2f}")
        print(f"Valor de venta: S/ {valor_venta:.2f}")
        print(f"Ganancia potencial: S/ {ganancia_potencial:.2f}")


def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')


def mostrar_menu():
    """Muestra el menú principal"""
    print("\n" + "=" * 60)
    print("   SISTEMA DE VENTAS E INVENTARIO")
    print("=" * 60)
    print("1. Agregar producto")
    print("2. Listar productos")
    print("3. Actualizar producto")
    print("4. Eliminar producto")
    print("5. Productos bajo stock")
    print("6. Crear venta")
    print("7. Ver venta")
    print("8. Listar ventas")
    print("9. Reporte del día")
    print("10. Valor del inventario")
    print("0. Salir")
    print("=" * 60)


def main():
    """Función principal del sistema de ventas"""
    print("=== SISTEMA DE VENTAS E INVENTARIO ===")
    nombre = input("Nombre de la tienda: ")
    sistema = SistemaVentas(nombre)
    
    # Agregar productos de ejemplo
    sistema.agregar_producto("P001", "Laptop HP", 1800, 2500, 10, 5)
    sistema.agregar_producto("P002", "Mouse Logitech", 30, 50, 50, 10)
    sistema.agregar_producto("P003", "Teclado RGB", 80, 120, 30, 8)
    sistema.agregar_producto("P004", "Monitor 24\"", 250, 400, 15, 5)
    sistema.agregar_producto("P005", "Auriculares", 40, 70, 25, 10)
    
    print(f"\nTienda '{nombre}' configurada.")
    print("Productos de ejemplo agregados.")
    
    while True:
        mostrar_menu()
        
        try:
            opcion = int(input("\nSeleccione una opción (0-10): "))
            
            if opcion == 0:
                print("\n¡Gracias por usar el Sistema de Ventas! ¡Hasta pronto!")
                break
            
            elif opcion == 1:
                # Agregar producto
                print("\n--- Agregar Producto ---")
                codigo = input("Código: ")
                if codigo in sistema.productos:
                    print("Error: El código ya existe.")
                else:
                    nombre = input("Nombre: ")
                    precio_compra = float(input("Precio de compra: "))
                    precio_venta = float(input("Precio de venta: "))
                    stock = int(input("Stock inicial: "))
                    stock_minimo = int(input("Stock mínimo (default 10): ") or "10")
                    
                    if sistema.agregar_producto(codigo, nombre, precio_compra, precio_venta, stock, stock_minimo):
                        print("Producto agregado exitosamente.")
            
            elif opcion == 2:
                # Listar productos
                sistema.listar_productos()
            
            elif opcion == 3:
                # Actualizar producto
                print("\n--- Actualizar Producto ---")
                codigo = input("Código del producto: ")
                producto = sistema.obtener_producto(codigo)
                
                if producto:
                    print("Deje en blanco para mantener el valor actual")
                    nombre = input(f"Nombre [{producto.nombre}]: ") or producto.nombre
                    
                    precio_c = input(f"Precio compra [{producto.precio_compra}]: ")
                    precio_compra = float(precio_c) if precio_c else producto.precio_compra
                    
                    precio_v = input(f"Precio venta [{producto.precio_venta}]: ")
                    precio_venta = float(precio_v) if precio_v else producto.precio_venta
                    
                    stock = input(f"Stock [{producto.stock}]: ")
                    stock = int(stock) if stock else producto.stock
                    
                    stock_m = input(f"Stock mínimo [{producto.stock_minimo}]: ")
                    stock_minimo = int(stock_m) if stock_m else producto.stock_minimo
                    
                    sistema.actualizar_producto(codigo, nombre=nombre, precio_compra=precio_compra,
                                              precio_venta=precio_venta, stock=stock, stock_minimo=stock_minimo)
                    print("Producto actualizado exitosamente.")
                else:
                    print("Producto no encontrado.")
            
            elif opcion == 4:
                # Eliminar producto
                print("\n--- Eliminar Producto ---")
                codigo = input("Código del producto: ")
                producto = sistema.obtener_producto(codigo)
                
                if producto:
                    confirmacion = input(f"¿Eliminar {producto.nombre}? (s/n): ")
                    if confirmacion.lower() == 's':
                        if sistema.eliminar_producto(codigo):
                            print("Producto eliminado.")
                else:
                    print("Producto no encontrado.")
            
            elif opcion == 5:
                # Productos bajo stock
                sistema.productos_bajo_stock()
            
            elif opcion == 6:
                # Crear venta
                print("\n--- Nueva Venta ---")
                cliente = input("Cliente: ")
                venta = sistema.crear_venta(cliente)
                
                print(f"\nVenta {venta.numero} creada.")
                sistema.listar_productos()
                
                while True:
                    codigo = input("\nCódigo del producto (o 'fin' para terminar): ")
                    if codigo.lower() == 'fin':
                        break
                    
                    producto = sistema.obtener_producto(codigo)
                    if producto:
                        cantidad = int(input(f"Cantidad (Stock: {producto.stock}): "))
                        if venta.agregar_item(producto, cantidad):
                            print(f"Item agregado: {producto.nombre} x{cantidad}")
                        else:
                            print("Error: Stock insuficiente.")
                    else:
                        print("Producto no encontrado.")
                
                if venta.items:
                    venta.mostrar_detalle()
                else:
                    print("Venta vacía. No se guardará.")
                    # Restaurar stock
                    for item in venta.items:
                        item["producto"].actualizar_stock(item["cantidad"])
                    del sistema.ventas[venta.numero]
            
            elif opcion == 7:
                # Ver venta
                print("\n--- Ver Venta ---")
                numero = input("Número de venta: ")
                venta = sistema.obtener_venta(numero)
                
                if venta:
                    venta.mostrar_detalle()
                else:
                    print("Venta no encontrada.")
            
            elif opcion == 8:
                # Listar ventas
                sistema.listar_ventas()
            
            elif opcion == 9:
                # Reporte del día
                sistema.reporte_ventas_dia()
            
            elif opcion == 10:
                # Valor inventario
                sistema.valor_inventario()
            
            else:
                print("\nOpción no válida. Por favor seleccione una opción entre 0 y 10.")
            
            input("\nPresione Enter para continuar...")
            limpiar_pantalla()
        
        except ValueError:
            print("\nError: Por favor ingrese un número válido.")
            input("Presione Enter para continuar...")
            limpiar_pantalla()


if __name__ == "__main__":
    main()
