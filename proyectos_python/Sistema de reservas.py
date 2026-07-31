"""
Sistema de Reservas
Gestión de reservas para diferentes servicios (citas, eventos, etc.)
"""

from datetime import datetime, timedelta
from enum import Enum


class EstadoReserva(Enum):
    """Estados posibles de una reserva"""
    PENDIENTE = "Pendiente"
    CONFIRMADA = "Confirmada"
    CANCELADA = "Cancelada"
    COMPLETADA = "Completada"


class TipoServicio(Enum):
    """Tipos de servicios disponibles"""
    CONSULTA = "Consulta"
    EVENTO = "Evento"
    REUNION = "Reunión"
    SERVICIO = "Servicio"


class Reserva:
    """Clase que representa una reserva"""
    
    def __init__(self, id_reserva, cliente, servicio, fecha, hora, duracion=1):
        self.id_reserva = id_reserva
        self.cliente = cliente
        self.servicio = servicio
        self.fecha = fecha
        self.hora = hora
        self.duracion = duracion  # en horas
        self.estado = EstadoReserva.PENDIENTE
        self.fecha_creacion = datetime.now()
        self.notas = ""
    
    @property
    def fecha_hora_completa(self):
        """Retorna la fecha y hora completa de la reserva"""
        return datetime.combine(self.fecha, self.hora)
    
    @property
    def fecha_fin(self):
        """Calcula la fecha y hora de fin de la reserva"""
        return self.fecha_hora_completa + timedelta(hours=self.duracion)
    
    def confirmar(self):
        """Confirma la reserva"""
        self.estado = EstadoReserva.CONFIRMADA
    
    def cancelar(self):
        """Cancela la reserva"""
        self.estado = EstadoReserva.CANCELADA
    
    def completar(self):
        """Marca la reserva como completada"""
        self.estado = EstadoReserva.COMPLETADA
    
    def es_pasada(self):
        """Verifica si la reserva ya pasó"""
        return datetime.now() > self.fecha_hora_completa
    
    def __str__(self):
        return f"{self.id_reserva} - {self.cliente} - {self.fecha} {self.hora} - {self.estado.value}"


class SistemaReservas:
    """Clase que gestiona el sistema de reservas"""
    
    def __init__(self, nombre_negocio):
        self.nombre_negocio = nombre_negocio
        self.reservas = {}
        self.contador_id = 1
        self.horarios_disponibles = self._generar_horarios()
    
    def _generar_horarios(self):
        """Genera horarios disponibles (cada hora de 8:00 a 20:00)"""
        horarios = []
        for hora in range(8, 21):
            horarios.append(datetime.strptime(f"{hora:02d}:00", "%H:%M").time())
        return horarios
    
    def crear_reserva(self, cliente, servicio, fecha, hora, duracion=1, notas=""):
        """Crea una nueva reserva"""
        # Validar disponibilidad
        if not self._verificar_disponibilidad(fecha, hora, duracion):
            print("El horario no está disponible.")
            return None
        
        id_reserva = f"RES-{str(self.contador_id).zfill(6)}"
        reserva = Reserva(id_reserva, cliente, servicio, fecha, hora, duracion)
        reserva.notas = notas
        
        self.reservas[id_reserva] = reserva
        self.contador_id += 1
        
        return reserva
    
    def _verificar_disponibilidad(self, fecha, hora, duracion):
        """Verifica si hay disponibilidad para el horario solicitado"""
        hora_inicio = datetime.combine(fecha, hora)
        hora_fin = hora_inicio + timedelta(hours=duracion)
        
        for reserva in self.reservas.values():
            if (reserva.estado == EstadoReserva.CANCELADA or 
                reserva.estado == EstadoReserva.COMPLETADA):
                continue
            
            if reserva.fecha != fecha:
                continue
            
            reserva_inicio = reserva.fecha_hora_completa
            reserva_fin = reserva.fecha_fin
            
            # Verificar solapamiento
            if (hora_inicio < reserva_fin and hora_fin > reserva_inicio):
                return False
        
        return True
    
    def obtener_reserva(self, id_reserva):
        """Obtiene una reserva por su ID"""
        return self.reservas.get(id_reserva)
    
    def actualizar_reserva(self, id_reserva, **kwargs):
        """Actualiza los datos de una reserva"""
        reserva = self.obtener_reserva(id_reserva)
        if not reserva:
            return False
        
        # Si se cambia fecha u hora, verificar disponibilidad
        if 'fecha' in kwargs or 'hora' in kwargs:
            nueva_fecha = kwargs.get('fecha', reserva.fecha)
            nueva_hora = kwargs.get('hora', reserva.hora)
            nueva_duracion = kwargs.get('duracion', reserva.duracion)
            
            if not self._verificar_disponibilidad(nueva_fecha, nueva_hora, nueva_duracion):
                print("El nuevo horario no está disponible.")
                return False
        
        for key, value in kwargs.items():
            if hasattr(reserva, key):
                setattr(reserva, key, value)
        
        return True
    
    def cancelar_reserva(self, id_reserva):
        """Cancela una reserva"""
        reserva = self.obtener_reserva(id_reserva)
        if reserva:
            reserva.cancelar()
            return True
        return False
    
    def confirmar_reserva(self, id_reserva):
        """Confirma una reserva"""
        reserva = self.obtener_reserva(id_reserva)
        if reserva:
            reserva.confirmar()
            return True
        return False
    
    def listar_reservas(self, fecha=None, estado=None):
        """Lista reservas con filtros opcionales"""
        reservas_filtradas = list(self.reservas.values())
        
        if fecha:
            reservas_filtradas = [r for r in reservas_filtradas if r.fecha == fecha]
        
        if estado:
            reservas_filtradas = [r for r in reservas_filtradas if r.estado == estado]
        
        return sorted(reservas_filtradas, key=lambda x: x.fecha_hora_completa)
    
    def listar_reservas_cliente(self, cliente):
        """Lista todas las reservas de un cliente"""
        return [r for r in self.reservas.values() if r.cliente.lower() == cliente.lower()]
    
    def obtener_horarios_disponibles(self, fecha):
        """Obtiene horarios disponibles para una fecha específica"""
        reservas_fecha = self.listar_reservas(fecha=fecha)
        horarios_ocupados = set()
        
        for reserva in reservas_fecha:
            if reserva.estado not in [EstadoReserva.CANCELADA, EstadoReserva.COMPLETADA]:
                hora = reserva.hora
                for i in range(reserva.duracion):
                    hora_ocupada = (datetime.combine(fecha, hora) + timedelta(hours=i)).time()
                    horarios_ocupados.add(hora_ocupada)
        
        horarios_disponibles = [h for h in self.horarios_disponibles if h not in horarios_ocupados]
        return horarios_disponibles
    
    def reservas_proximas(self, dias=7):
        """Obtiene reservas para los próximos días"""
        hoy = datetime.now().date()
        fecha_limite = hoy + timedelta(days=dias)
        
        reservas_proximas = []
        for reserva in self.reservas.values():
            if (hoy <= reserva.fecha <= fecha_limite and 
                reserva.estado not in [EstadoReserva.CANCELADA, EstadoReserva.COMPLETADA]):
                reservas_proximas.append(reserva)
        
        return sorted(reservas_proximas, key=lambda x: x.fecha_hora_completa)
    
    def estadisticas(self):
        """Genera estadísticas del sistema"""
        total = len(self.reservas)
        por_estado = {}
        
        for estado in EstadoReserva:
            por_estado[estado.value] = sum(1 for r in self.reservas.values() if r.estado == estado)
        
        return {
            'total_reservas': total,
            'por_estado': por_estado,
            'reservas_activas': por_estado.get(EstadoReserva.CONFIRMADA.value, 0) + 
                             por_estado.get(EstadoReserva.PENDIENTE.value, 0)
        }


def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')


def mostrar_menu():
    """Muestra el menú principal"""
    print("\n" + "=" * 60)
    print("   SISTEMA DE RESERVAS")
    print("=" * 60)
    print("1. Crear reserva")
    print("2. Ver reserva por ID")
    print("3. Listar todas las reservas")
    print("4. Listar reservas por fecha")
    print("5. Listar reservas por cliente")
    print("6. Ver horarios disponibles")
    print("7. Ver reservas próximas")
    print("8. Confirmar reserva")
    print("9. Cancelar reserva")
    print("10. Actualizar reserva")
    print("11. Ver estadísticas")
    print("0. Salir")
    print("=" * 60)


def main():
    """Función principal del sistema de reservas"""
    print("=== SISTEMA DE RESERVAS ===")
    nombre = input("Nombre del negocio: ")
    sistema = SistemaReservas(nombre)
    
    print(f"\nSistema de reservas '{nombre}' configurado.")
    
    while True:
        mostrar_menu()
        
        try:
            opcion = int(input("\nSeleccione una opción (0-11): "))
            
            if opcion == 0:
                print("\n¡Gracias por usar el Sistema de Reservas! ¡Hasta pronto!")
                break
            
            elif opcion == 1:
                # Crear reserva
                print("\n--- Crear Nueva Reserva ---")
                cliente = input("Nombre del cliente: ")
                servicio = input("Tipo de servicio (Consulta/Evento/Reunión/Servicio): ")
                
                fecha_str = input("Fecha (YYYY-MM-DD): ")
                fecha = datetime.strptime(fecha_str, "%Y-%m-%d").date()
                
                # Mostrar horarios disponibles
                horarios = sistema.obtener_horarios_disponibles(fecha)
                if horarios:
                    print("\nHorarios disponibles:")
                    for i, hora in enumerate(horarios, 1):
                        print(f"{i}. {hora.strftime('%H:%M')}")
                    
                    idx = int(input("Seleccione horario (número): ")) - 1
                    if 0 <= idx < len(horarios):
                        hora = horarios[idx]
                    else:
                        print("Selección inválida.")
                        continue
                else:
                    print("No hay horarios disponibles para esa fecha.")
                    continue
                
                duracion = int(input("Duración en horas (default 1): ") or "1")
                notas = input("Notas adicionales (opcional): ")
                
                reserva = sistema.crear_reserva(cliente, servicio, fecha, hora, duracion, notas)
                if reserva:
                    print(f"\n¡Reserva creada exitosamente!")
                    print(f"ID: {reserva.id_reserva}")
                    print(f"Cliente: {reserva.cliente}")
                    print(f"Servicio: {reserva.servicio}")
                    print(f"Fecha: {reserva.fecha} a las {reserva.hora.strftime('%H:%M')}")
                    print(f"Duración: {reserva.duracion} hora(s)")
                    print(f"Estado: {reserva.estado.value}")
            
            elif opcion == 2:
                # Ver por ID
                print("\n--- Ver Reserva por ID ---")
                id_reserva = input("ID de la reserva: ")
                reserva = sistema.obtener_reserva(id_reserva)
                
                if reserva:
                    print(f"\nID: {reserva.id_reserva}")
                    print(f"Cliente: {reserva.cliente}")
                    print(f"Servicio: {reserva.servicio}")
                    print(f"Fecha: {reserva.fecha}")
                    print(f"Hora: {reserva.hora.strftime('%H:%M')}")
                    print(f"Duración: {reserva.duracion} hora(s)")
                    print(f"Estado: {reserva.estado.value}")
                    print(f"Notas: {reserva.notas or 'N/A'}")
                    print(f"Fecha de creación: {reserva.fecha_creacion.strftime('%Y-%m-%d %H:%M:%S')}")
                else:
                    print("Reserva no encontrada.")
            
            elif opcion == 3:
                # Listar todas
                print("\n--- Todas las Reservas ---")
                reservas = sistema.listar_reservas()
                if reservas:
                    for reserva in reservas:
                        print(f"{reserva.id_reserva} - {reserva.cliente} - {reserva.fecha} {reserva.hora.strftime('%H:%M')} - {reserva.estado.value}")
                else:
                    print("No hay reservas registradas.")
            
            elif opcion == 4:
                # Listar por fecha
                print("\n--- Reservas por Fecha ---")
                fecha_str = input("Fecha (YYYY-MM-DD): ")
                fecha = datetime.strptime(fecha_str, "%Y-%m-%d").date()
                
                reservas = sistema.listar_reservas(fecha=fecha)
                if reservas:
                    for reserva in reservas:
                        print(f"{reserva.id_reserva} - {reserva.cliente} - {reserva.hora.strftime('%H:%M')} - {reserva.servicio} - {reserva.estado.value}")
                else:
                    print("No hay reservas para esa fecha.")
            
            elif opcion == 5:
                # Listar por cliente
                print("\n--- Reservas por Cliente ---")
                cliente = input("Nombre del cliente: ")
                reservas = sistema.listar_reservas_cliente(cliente)
                
                if reservas:
                    for reserva in reservas:
                        print(f"{reserva.id_reserva} - {reserva.fecha} {reserva.hora.strftime('%H:%M')} - {reserva.servicio} - {reserva.estado.value}")
                else:
                    print(f"No hay reservas para {cliente}.")
            
            elif opcion == 6:
                # Ver horarios disponibles
                print("\n--- Horarios Disponibles ---")
                fecha_str = input("Fecha (YYYY-MM-DD): ")
                fecha = datetime.strptime(fecha_str, "%Y-%m-%d").date()
                
                horarios = sistema.obtener_horarios_disponibles(fecha)
                if horarios:
                    print(f"Horarios disponibles para {fecha}:")
                    for hora in horarios:
                        print(f"  - {hora.strftime('%H:%M')}")
                else:
                    print("No hay horarios disponibles para esa fecha.")
            
            elif opcion == 7:
                # Reservas próximas
                print("\n--- Reservas Próximas (7 días) ---")
                reservas = sistema.reservas_proximas()
                if reservas:
                    for reserva in reservas:
                        print(f"{reserva.id_reserva} - {reserva.cliente} - {reserva.fecha} {reserva.hora.strftime('%H:%M')} - {reserva.servicio}")
                else:
                    print("No hay reservas próximas.")
            
            elif opcion == 8:
                # Confirmar reserva
                print("\n--- Confirmar Reserva ---")
                id_reserva = input("ID de la reserva: ")
                if sistema.confirmar_reserva(id_reserva):
                    print("Reserva confirmada exitosamente.")
                else:
                    print("No se pudo confirmar la reserva.")
            
            elif opcion == 9:
                # Cancelar reserva
                print("\n--- Cancelar Reserva ---")
                id_reserva = input("ID de la reserva: ")
                confirmacion = input("¿Está seguro de cancelar esta reserva? (s/n): ")
                if confirmacion.lower() == 's':
                    if sistema.cancelar_reserva(id_reserva):
                        print("Reserva cancelada exitosamente.")
                    else:
                        print("No se pudo cancelar la reserva.")
                else:
                    print("Operación cancelada.")
            
            elif opcion == 10:
                # Actualizar reserva
                print("\n--- Actualizar Reserva ---")
                id_reserva = input("ID de la reserva: ")
                reserva = sistema.obtener_reserva(id_reserva)
                
                if reserva:
                    print("Deje en blanco para mantener el valor actual")
                    cliente = input(f"Cliente [{reserva.cliente}]: ") or None
                    servicio = input(f"Servicio [{reserva.servicio}]: ") or None
                    
                    fecha_input = input(f"Fecha [{reserva.fecha}]: ")
                    fecha = datetime.strptime(fecha_input, "%Y-%m-%d").date() if fecha_input else None
                    
                    hora_input = input(f"Hora [{reserva.hora.strftime('%H:%M')}]: ")
                    hora = datetime.strptime(hora_input, "%H:%M").time() if hora_input else None
                    
                    duracion_input = input(f"Duración [{reserva.duracion}]: ")
                    duracion = int(duracion_input) if duracion_input else None
                    
                    notas = input(f"Notas [{reserva.notas}]: ") or None
                    
                    kwargs = {}
                    if cliente: kwargs['cliente'] = cliente
                    if servicio: kwargs['servicio'] = servicio
                    if fecha: kwargs['fecha'] = fecha
                    if hora: kwargs['hora'] = hora
                    if duracion: kwargs['duracion'] = duracion
                    if notas is not None: kwargs['notas'] = notas
                    
                    if sistema.actualizar_reserva(id_reserva, **kwargs):
                        print("Reserva actualizada exitosamente.")
                else:
                    print("Reserva no encontrada.")
            
            elif opcion == 11:
                # Estadísticas
                stats = sistema.estadisticas()
                print("\n--- Estadísticas ---")
                print(f"Total de reservas: {stats['total_reservas']}")
                print(f"Reservas activas: {stats['reservas_activas']}")
                print("\nPor estado:")
                for estado, cantidad in stats['por_estado'].items():
                    print(f"  {estado}: {cantidad}")
            
            else:
                print("\nOpción no válida. Por favor seleccione una opción entre 0 y 11.")
            
            input("\nPresione Enter para continuar...")
            limpiar_pantalla()
        
        except ValueError:
            print("\nError: Por favor ingrese un formato válido.")
            input("Presione Enter para continuar...")
            limpiar_pantalla()


if __name__ == "__main__":
    main()
