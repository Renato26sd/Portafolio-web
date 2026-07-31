"""
Sistema de Gestión de Estacionamiento
Control de entrada y salida de vehículos con cálculo de tarifas
"""

from datetime import datetime, timedelta


class Vehiculo:
    """Clase que representa un vehículo"""
    
    def __init__(self, placa, marca, modelo, color):
        self.placa = placa
        self.marca = marca
        self.modelo = modelo
        self.color = color
    
    def __str__(self):
        return f"{self.placa} - {self.marca} {self.modelo} ({self.color})"


class Ticket:
    """Clase que representa un ticket de estacionamiento"""
    
    def __init__(self, vehiculo, numero):
        self.vehiculo = vehiculo
        self.numero = numero
        self.hora_entrada = datetime.now()
        self.hora_salida = None
        self.pagado = False
    
    def registrar_salida(self):
        """Registra la hora de salida"""
        self.hora_salida = datetime.now()
    
    @property
    def tiempo_estacionado(self):
        """Calcula el tiempo estacionado en minutos"""
        if self.hora_salida:
            return (self.hora_salida - self.hora_entrada).total_seconds() / 60
        return (datetime.now() - self.hora_entrada).total_seconds() / 60
    
    @property
    def horas_estacionado(self):
        """Calcula las horas estacionadas (redondeando hacia arriba)"""
        minutos = self.tiempo_estacionado
        return int(minutos / 60) + (1 if minutos % 60 > 0 else 0)
    
    def __str__(self):
        estado = "Pagado" if self.pagado else "Pendiente"
        return f"Ticket {self.numero} - {self.vehiculo.placa} - {estado}"


class Estacionamiento:
    """Clase que gestiona el estacionamiento"""
    
    def __init__(self, nombre, capacidad, tarifa_hora):
        self.nombre = nombre
        self.capacidad = capacidad
        self.tarifa_hora = tarifa_hora
        self.espacios_ocupados = 0
        self.tickets = {}
        self.contador_tickets = 1
        self.vehiculos_estacionados = {}
    
    def entrada_vehiculo(self, placa, marca, modelo, color):
        """Registra la entrada de un vehículo"""
        if self.espacios_ocupados >= self.capacidad:
            print("Estacionamiento lleno.")
            return None
        
        if placa in self.vehiculos_estacionados:
            print("El vehículo ya está estacionado.")
            return None
        
        vehiculo = Vehiculo(placa, marca, modelo, color)
        numero_ticket = f"T-{str(self.contador_tickets).zfill(4)}"
        ticket = Ticket(vehiculo, numero_ticket)
        
        self.tickets[numero_ticket] = ticket
        self.vehiculos_estacionados[placa] = ticket
        self.espacios_ocupados += 1
        self.contador_tickets += 1
        
        return ticket
    
    def salida_vehiculo(self, placa):
        """Registra la salida de un vehículo y calcula el pago"""
        if placa not in self.vehiculos_estacionados:
            print("Vehículo no encontrado en el estacionamiento.")
            return None
        
        ticket = self.vehiculos_estacionados[placa]
        ticket.registrar_salida()
        
        monto = self.calcular_pago(ticket)
        ticket.pagado = True
        
        del self.vehiculos_estacionados[placa]
        self.espacios_ocupados -= 1
        
        return ticket, monto
    
    def calcular_pago(self, ticket):
        """Calcula el monto a pagar"""
        horas = ticket.horas_estacionado
        return horas * self.tarifa_hora
    
    def obtener_ticket(self, numero_ticket):
        """Obtiene un ticket por su número"""
        return self.tickets.get(numero_ticket)
    
    def vehiculo_estacionado(self, placa):
        """Verifica si un vehículo está estacionado"""
        return placa in self.vehiculos_estacionados
    
    def listar_vehiculos_estacionados(self):
        """Lista todos los vehículos estacionados"""
        print(f"\n=== Vehículos Estacionados en {self.nombre} ===")
        print(f"Capacidad: {self.espacios_ocupados}/{self.capacidad}")
        print("-" * 60)
        
        if not self.vehiculos_estacionados:
            print("No hay vehículos estacionados.")
            return
        
        for ticket in self.vehiculos_estacionados.values():
            print(f"Ticket: {ticket.numero}")
            print(f"Vehículo: {ticket.vehiculo}")
            print(f"Hora entrada: {ticket.hora_entrada.strftime('%Y-%m-%d %H:%M:%S')}")
            print(f"Tiempo estacionado: {int(ticket.tiempo_estacionado)} minutos")
            print("-" * 60)
    
    def listar_historial_tickets(self):
        """Lista el historial de todos los tickets"""
        print(f"\n=== Historial de Tickets - {self.nombre} ===")
        print("-" * 60)
        
        for ticket in self.tickets.values():
            estado = "Pagado" if ticket.pagado else "En estacionamiento"
            print(f"Ticket: {ticket.numero}")
            print(f"Vehículo: {ticket.vehiculo.placa}")
            print(f"Entrada: {ticket.hora_entrada.strftime('%Y-%m-%d %H:%M:%S')}")
            if ticket.hora_salida:
                print(f"Salida: {ticket.hora_salida.strftime('%Y-%m-%d %H:%M:%S')}")
                print(f"Tiempo: {ticket.horas_estacionado} hora(s)")
            print(f"Estado: {estado}")
            print("-" * 60)
    
    @property
    def espacios_disponibles(self):
        """Retorna la cantidad de espacios disponibles"""
        return self.capacidad - self.espacios_ocupados


def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')


def mostrar_menu():
    """Muestra el menú principal"""
    print("\n" + "=" * 60)
    print("   GESTIÓN DE ESTACIONAMIENTO")
    print("=" * 60)
    print("1. Entrada de vehículo")
    print("2. Salida de vehículo")
    print("3. Ver vehículos estacionados")
    print("4. Ver historial de tickets")
    print("5. Buscar vehículo por placa")
    print("6. Ver disponibilidad")
    print("0. Salir")
    print("=" * 60)


def main():
    """Función principal del sistema de estacionamiento"""
    print("=== SISTEMA DE GESTIÓN DE ESTACIONAMIENTO ===")
    
    nombre = input("Nombre del estacionamiento: ")
    capacidad = int(input("Capacidad (número de espacios): "))
    tarifa = float(input("Tarifa por hora (S/): "))
    
    estacionamiento = Estacionamiento(nombre, capacidad, tarifa)
    
    print(f"\nEstacionamiento '{nombre}' configurado exitosamente.")
    print(f"Capacidad: {capacidad} vehículos")
    print(f"Tarifa: S/ {tarifa:.2f} por hora")
    
    while True:
        mostrar_menu()
        
        try:
            opcion = int(input("\nSeleccione una opción (0-6): "))
            
            if opcion == 0:
                print("\n¡Gracias por usar el Sistema de Estacionamiento! ¡Hasta pronto!")
                break
            
            elif opcion == 1:
                # Entrada de vehículo
                print("\n--- Entrada de Vehículo ---")
                if estacionamiento.espacios_disponibles == 0:
                    print("Estacionamiento lleno. No hay espacios disponibles.")
                else:
                    placa = input("Placa: ").upper()
                    marca = input("Marca: ")
                    modelo = input("Modelo: ")
                    color = input("Color: ")
                    
                    ticket = estacionamiento.entrada_vehiculo(placa, marca, modelo, color)
                    if ticket:
                        print(f"\n¡Entrada registrada exitosamente!")
                        print(f"Ticket N°: {ticket.numero}")
                        print(f"Hora de entrada: {ticket.hora_entrada.strftime('%Y-%m-%d %H:%M:%S')}")
                        print(f"Espacios disponibles: {estacionamiento.espacios_disponibles}")
            
            elif opcion == 2:
                # Salida de vehículo
                print("\n--- Salida de Vehículo ---")
                placa = input("Placa del vehículo: ").upper()
                
                resultado = estacionamiento.salida_vehiculo(placa)
                if resultado:
                    ticket, monto = resultado
                    print(f"\n--- Ticket de Pago ---")
                    print(f"Ticket N°: {ticket.numero}")
                    print(f"Vehículo: {ticket.vehiculo}")
                    print(f"Hora entrada: {ticket.hora_entrada.strftime('%Y-%m-%d %H:%M:%S')}")
                    print(f"Hora salida: {ticket.hora_salida.strftime('%Y-%m-%d %H:%M:%S')}")
                    print(f"Tiempo estacionado: {ticket.horas_estacionado} hora(s)")
                    print(f"Tarifa por hora: S/ {estacionamiento.tarifa_hora:.2f}")
                    print(f"TOTAL A PAGAR: S/ {monto:.2f}")
                    print(f"Espacios disponibles: {estacionamiento.espacios_disponibles}")
            
            elif opcion == 3:
                # Ver vehículos estacionados
                estacionamiento.listar_vehiculos_estacionados()
            
            elif opcion == 4:
                # Ver historial
                estacionamiento.listar_historial_tickets()
            
            elif opcion == 5:
                # Buscar vehículo
                print("\n--- Buscar Vehículo ---")
                placa = input("Placa: ").upper()
                
                if estacionamiento.vehiculo_estacionado(placa):
                    ticket = estacionamiento.vehiculos_estacionados[placa]
                    print(f"\nVehículo encontrado:")
                    print(f"Ticket: {ticket.numero}")
                    print(f"Vehículo: {ticket.vehiculo}")
                    print(f"Hora entrada: {ticket.hora_entrada.strftime('%Y-%m-%d %H:%M:%S')}")
                    print(f"Tiempo estacionado: {int(ticket.tiempo_estacionado)} minutos")
                else:
                    print("El vehículo no está estacionado actualmente.")
            
            elif opcion == 6:
                # Ver disponibilidad
                print(f"\n--- Disponibilidad ---")
                print(f"Estacionamiento: {estacionamiento.nombre}")
                print(f"Capacidad total: {estacionamiento.capacidad}")
                print(f"Espacios ocupados: {estacionamiento.espacios_ocupados}")
                print(f"Espacios disponibles: {estacionamiento.espacios_disponibles}")
                print(f"Porcentaje ocupado: {(estacionamiento.espacios_ocupados / estacionamiento.capacidad * 100):.1f}%")
            
            else:
                print("\nOpción no válida. Por favor seleccione una opción entre 0 y 6.")
            
            input("\nPresione Enter para continuar...")
            limpiar_pantalla()
        
        except ValueError:
            print("\nError: Por favor ingrese un número válido.")
            input("Presione Enter para continuar...")
            limpiar_pantalla()


if __name__ == "__main__":
    main()
