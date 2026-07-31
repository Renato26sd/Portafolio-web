"""
Sistema de Gestión de Empleados
CRUD completo para gestionar empleados de una empresa
"""

from datetime import datetime


class Empleado:
    """Clase que representa un empleado"""
    
    def __init__(self, id_empleado, nombre, apellido, departamento, cargo, salario, fecha_contratacion=None):
        self.id_empleado = id_empleado
        self.nombre = nombre
        self.apellido = apellido
        self.departamento = departamento
        self.cargo = cargo
        self.salario = salario
        self.fecha_contratacion = fecha_contratacion or datetime.now()
        self.activo = True
    
    @property
    def nombre_completo(self):
        return f"{self.nombre} {self.apellido}"
    
    @property
    def antiguedad_anios(self):
        """Calcula la antigüedad en años"""
        hoy = datetime.now()
        antiguedad = hoy - self.fecha_contratacion
        return int(antiguedad.days / 365)
    
    def aumentar_salario(self, porcentaje):
        """Aumenta el salario por un porcentaje"""
        self.salario *= (1 + porcentaje / 100)
    
    def __str__(self):
        return f"{self.id_empleado} - {self.nombre_completo} - {self.cargo} - {self.departamento} - S/ {self.salario:.2f}"


class SistemaGestionEmpleados:
    """Clase que gestiona el sistema de empleados"""
    
    def __init__(self, nombre_empresa):
        self.nombre_empresa = nombre_empresa
        self.empleados = {}
        self.contador_id = 1
    
    def agregar_empleado(self, nombre, apellido, departamento, cargo, salario):
        """Agrega un nuevo empleado"""
        id_empleado = f"EMP-{str(self.contador_id).zfill(4)}"
        empleado = Empleado(id_empleado, nombre, apellido, departamento, cargo, salario)
        self.empleados[id_empleado] = empleado
        self.contador_id += 1
        return empleado
    
    def obtener_empleado(self, id_empleado):
        """Obtiene un empleado por su ID"""
        return self.empleados.get(id_empleado)
    
    def actualizar_empleado(self, id_empleado, **kwargs):
        """Actualiza los datos de un empleado"""
        empleado = self.obtener_empleado(id_empleado)
        if empleado:
            for key, value in kwargs.items():
                if hasattr(empleado, key):
                    setattr(empleado, key, value)
            return True
        return False
    
    def eliminar_empleado(self, id_empleado):
        """Elimina (desactiva) un empleado"""
        empleado = self.obtener_empleado(id_empleado)
        if empleado:
            empleado.activo = False
            return True
        return False
    
    def listar_empleados(self, solo_activos=True):
        """Lista todos los empleados"""
        print(f"\n=== Empleados de {self.nombre_empresa} ===")
        print("-" * 80)
        
        empleados_filtrados = [e for e in self.empleados.values() if e.activo or not solo_activos]
        
        if not empleados_filtrados:
            print("No hay empleados registrados.")
            return
        
        for empleado in empleados_filtrados:
            estado = "Activo" if empleado.activo else "Inactivo"
            print(f"ID: {empleado.id_empleado}")
            print(f"Nombre: {empleado.nombre_completo}")
            print(f"Departamento: {empleado.departamento}")
            print(f"Cargo: {empleado.cargo}")
            print(f"Salario: S/ {empleado.salario:.2f}")
            print(f"Antigüedad: {empleado.antiguedad_anios} años")
            print(f"Estado: {estado}")
            print("-" * 80)
    
    def buscar_por_departamento(self, departamento):
        """Busca empleados por departamento"""
        return [e for e in self.empleados.values() if e.departamento.lower() == departamento.lower() and e.activo]
    
    def buscar_por_cargo(self, cargo):
        """Busca empleados por cargo"""
        return [e for e in self.empleados.values() if e.cargo.lower() == cargo.lower() and e.activo]
    
    def calcular_nomina_total(self):
        """Calcula la nómina total de empleados activos"""
        return sum(e.salario for e in self.empleados.values() if e.activo)
    
    def empleados_por_departamento(self):
        """Muestra la cantidad de empleados por departamento"""
        departamentos = {}
        for empleado in self.empleados.values():
            if empleado.activo:
                departamentos[empleado.departamento] = departamentos.get(empleado.departamento, 0) + 1
        
        print("\n=== Empleados por Departamento ===")
        for dept, cantidad in departamentos.items():
            print(f"{dept}: {cantidad} empleado(s)")


def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')


def mostrar_menu():
    """Muestra el menú principal"""
    print("\n" + "=" * 60)
    print("   GESTIÓN DE EMPLEADOS")
    print("=" * 60)
    print("1. Agregar empleado")
    print("2. Listar todos los empleados")
    print("3. Buscar empleado por ID")
    print("4. Actualizar empleado")
    print("5. Eliminar empleado")
    print("6. Buscar por departamento")
    print("7. Buscar por cargo")
    print("8. Calcular nómina total")
    print("9. Empleados por departamento")
    print("10. Aumentar salario")
    print("0. Salir")
    print("=" * 60)


def main():
    """Función principal del sistema de gestión de empleados"""
    sistema = SistemaGestionEmpleados("Tech Solutions S.A.C.")
    
    print("=== SISTEMA DE GESTIÓN DE EMPLEADOS ===")
    print(f"Bienvenido al sistema de {sistema.nombre_empresa}")
    
    while True:
        mostrar_menu()
        
        try:
            opcion = int(input("\nSeleccione una opción (0-10): "))
            
            if opcion == 0:
                print("\n¡Gracias por usar el Sistema de Gestión de Empleados! ¡Hasta pronto!")
                break
            
            elif opcion == 1:
                # Agregar empleado
                print("\n--- Agregar Nuevo Empleado ---")
                nombre = input("Nombre: ")
                apellido = input("Apellido: ")
                departamento = input("Departamento: ")
                cargo = input("Cargo: ")
                salario = float(input("Salario: "))
                
                empleado = sistema.agregar_empleado(nombre, apellido, departamento, cargo, salario)
                print(f"\nEmpleado agregado exitosamente.")
                print(f"ID asignado: {empleado.id_empleado}")
            
            elif opcion == 2:
                # Listar empleados
                sistema.listar_empleados()
            
            elif opcion == 3:
                # Buscar por ID
                print("\n--- Buscar Empleado por ID ---")
                id_empleado = input("ID del empleado: ")
                empleado = sistema.obtener_empleado(id_empleado)
                
                if empleado:
                    print("\n--- Datos del Empleado ---")
                    print(f"ID: {empleado.id_empleado}")
                    print(f"Nombre: {empleado.nombre_completo}")
                    print(f"Departamento: {empleado.departamento}")
                    print(f"Cargo: {empleado.cargo}")
                    print(f"Salario: S/ {empleado.salario:.2f}")
                    print(f"Fecha de contratación: {empleado.fecha_contratacion.strftime('%Y-%m-%d')}")
                    print(f"Antigüedad: {empleado.antiguedad_anios} años")
                    print(f"Estado: {'Activo' if empleado.activo else 'Inactivo'}")
                else:
                    print("Empleado no encontrado.")
            
            elif opcion == 4:
                # Actualizar empleado
                print("\n--- Actualizar Empleado ---")
                id_empleado = input("ID del empleado: ")
                empleado = sistema.obtener_empleado(id_empleado)
                
                if empleado:
                    print("Deje en blanco para mantener el valor actual")
                    nombre = input(f"Nombre [{empleado.nombre}]: ") or empleado.nombre
                    apellido = input(f"Apellido [{empleado.apellido}]: ") or empleado.apellido
                    departamento = input(f"Departamento [{empleado.departamento}]: ") or empleado.departamento
                    cargo = input(f"Cargo [{empleado.cargo}]: ") or empleado.cargo
                    
                    salario_input = input(f"Salario [{empleado.salario}]: ")
                    salario = float(salario_input) if salario_input else empleado.salario
                    
                    sistema.actualizar_empleado(id_empleado, nombre=nombre, apellido=apellido, 
                                                departamento=departamento, cargo=cargo, salario=salario)
                    print("Empleado actualizado exitosamente.")
                else:
                    print("Empleado no encontrado.")
            
            elif opcion == 5:
                # Eliminar empleado
                print("\n--- Eliminar Empleado ---")
                id_empleado = input("ID del empleado: ")
                empleado = sistema.obtener_empleado(id_empleado)
                
                if empleado:
                    confirmacion = input(f"¿Está seguro de eliminar a {empleado.nombre_completo}? (s/n): ")
                    if confirmacion.lower() == 's':
                        if sistema.eliminar_empleado(id_empleado):
                            print("Empleado eliminado exitosamente.")
                        else:
                            print("Error al eliminar el empleado.")
                    else:
                        print("Operación cancelada.")
                else:
                    print("Empleado no encontrado.")
            
            elif opcion == 6:
                # Buscar por departamento
                print("\n--- Buscar por Departamento ---")
                departamento = input("Departamento: ")
                empleados = sistema.buscar_por_departamento(departamento)
                
                if empleados:
                    print(f"\nEmpleados en {departamento}:")
                    for emp in empleados:
                        print(f"  {emp}")
                else:
                    print(f"No hay empleados en el departamento {departamento}.")
            
            elif opcion == 7:
                # Buscar por cargo
                print("\n--- Buscar por Cargo ---")
                cargo = input("Cargo: ")
                empleados = sistema.buscar_por_cargo(cargo)
                
                if empleados:
                    print(f"\nEmpleados con cargo {cargo}:")
                    for emp in empleados:
                        print(f"  {emp}")
                else:
                    print(f"No hay empleados con el cargo {cargo}.")
            
            elif opcion == 8:
                # Calcular nómina
                nomina = sistema.calcular_nomina_total()
                print(f"\nNómina total de empleados activos: S/ {nomina:.2f}")
            
            elif opcion == 9:
                # Empleados por departamento
                sistema.empleados_por_departamento()
            
            elif opcion == 10:
                # Aumentar salario
                print("\n--- Aumentar Salario ---")
                id_empleado = input("ID del empleado: ")
                empleado = sistema.obtener_empleado(id_empleado)
                
                if empleado:
                    porcentaje = float(input("Porcentaje de aumento: "))
                    empleado.aumentar_salario(porcentaje)
                    print(f"Salario actualizado: S/ {empleado.salario:.2f}")
                else:
                    print("Empleado no encontrado.")
            
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
