"""Sistema Bancario Virtual Permite gestionar cuentas bancarias con operaciones básicas """
from datetime import datetime
import random

class CuentaBancaria:
    """Clase que representa una cuenta bancaria"""
    
    def __init__(self, numero_cuenta, titular, saldo_inicial=0):
        self.numero_cuenta = numero_cuenta
        self.titular = titular
        self.saldo = saldo_inicial
        self.pin = None
        self.historial = []
        self.activa = True

    def establecer_pin(self, pin):
        """Establece el PIN de la cuenta"""
        if len(pin) == 4 and pin.isdigit():
            self.pin = pin
            return True
        return False
    
    def verificar_pin(self, pin):
        """Verifica si el PIN es correcto"""
        return self.pin == pin
    
    def depositar(self, monto):
        """Deposita dinero en la cuenta"""
        if monto > 0:
            self.saldo += monto
            self._registrar_transaccion("Depósito", monto)
            return True
        return False
    
    def retirar(self, monto):
        """Retira dinero de la cuenta"""
        if monto > 0 and self.saldo >= monto:
            self.saldo -= monto
            self._registrar_transaccion("Retiro", monto)
            return True
        return False
    
    def transferir(self, cuenta_destino, monto):
        """Transfiere dinero a otra cuenta"""
        if monto > 0 and self.saldo >= monto:
            self.saldo -= monto
            cuenta_destino.saldo += monto
            self._registrar_transaccion(f"Transferencia a {cuenta_destino.numero_cuenta}", monto)
            cuenta_destino._registrar_transaccion(f"Transferencia de {self.numero_cuenta}", monto)
            return True
        return False
    
    def _registrar_transaccion(self, tipo, monto):
        """Registra una transacción en el historial"""
        fecha = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        self.historial.append({
            "fecha": fecha,
            "tipo": tipo,
            "monto": monto,
            "saldo_despues": self.saldo
        })
    
    def consultar_saldo(self):
        """Consulta el saldo actual"""
        return self.saldo
    
    def mostrar_historial(self):
        """Muestra el historial de transacciones"""
        print("\n=== Historial de Transacciones ===")
        print(f"Cuenta: {self.numero_cuenta} - Titular: {self.titular}")
        print("-" * 60)
        for trans in self.historial:
            print(f"Fecha: {trans['fecha']}")
            print(f"Tipo: {trans['tipo']}")
            print(f"Monto: S/ {trans['monto']:.2f}")
            print(f"Saldo después: S/ {trans['saldo_despues']:.2f}")
            print("-" * 60)
    
    def __str__(self):
        return f"Cuenta: {self.numero_cuenta} | Titular: {self.titular} | Saldo: S/ {self.saldo:.2f}"

class BancoVirtual:
    """Clase que gestiona el sistema bancario"""
    def __init__(self, nombre_banco):
        self.nombre_banco = nombre_banco
        self.cuentas = {}
    
    def crear_cuenta(self, titular, saldo_inicial=0):
        """Crea una nueva cuenta bancaria"""
        numero_cuenta = self._generar_numero_cuenta()
        nueva_cuenta = CuentaBancaria(numero_cuenta, titular, saldo_inicial)
        self.cuentas[numero_cuenta] = nueva_cuenta
        return nueva_cuenta
    
    def _generar_numero_cuenta(self):
        """Genera un número de cuenta único"""
        while True:
            numero = str(random.randint(1000000000, 9999999999))
            if numero not in self.cuentas:
                return numero
    
    def obtener_cuenta(self, numero_cuenta):
        """Obtiene una cuenta por su número"""
        return self.cuentas.get(numero_cuenta)
    
    def listar_cuentas(self):
        """Lista todas las cuentas del banco"""
        print(f"\n=== Cuentas en {self.nombre_banco} ===")
        for cuenta in self.cuentas.values():
            print(cuenta)
    
    def eliminar_cuenta(self, numero_cuenta):
        """Elimina una cuenta del banco"""
        if numero_cuenta in self.cuentas:
            del self.cuentas[numero_cuenta]
            return True
        return False

def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')

def mostrar_menu():
    """Muestra el menú principal"""
    print("\n" + "=" * 60)
    print("           BANCO VIRTUAL")
    print("=" * 60)
    print("1. Crear cuenta")
    print("2. Consultar saldo")
    print("3. Depositar dinero")
    print("4. Retirar dinero")
    print("5. Transferir dinero")
    print("6. Ver historial de transacciones")
    print("7. Listar todas las cuentas")
    print("8. Eliminar cuenta")
    print("0. Salir")
    print("=" * 60)

def main():
    """Función principal del sistema bancario"""
    banco = BancoVirtual("Banco Virtual Peru")
    
    print("=== SISTEMA BANCARIO VIRTUAL ===")
    print("Bienvenido al Banco Virtual Peru")
    
    while True:
        mostrar_menu() 
        try:
            opcion = int(input("\nSeleccione una opción (0-8): "))
            if opcion == 0:
                print("\n¡Gracias por usar el Banco Virtual Peru! ¡Hasta pronto!")
                break
            elif opcion == 1:
                # Crear cuenta
                print("\n--- Crear Nueva Cuenta ---")
                titular = input("Nombre del titular: ")
                saldo_inicial = float(input("Saldo inicial (opcional, presione Enter para 0): ") or "0")
                
                cuenta = banco.crear_cuenta(titular, saldo_inicial)
                print(f"\n¡Cuenta creada exitosamente!")
                print(f"Número de cuenta: {cuenta.numero_cuenta}")
                print(f"Titular: {cuenta.titular}")
                print(f"Saldo inicial: S/ {cuenta.saldo:.2f}")
                
                # Establecer PIN
                while True:
                    pin = input("Establezca un PIN de 4 dígitos: ")
                    if cuenta.establecer_pin(pin):
                        print("PIN establecido correctamente.")
                        break
                    else:
                        print("Error: El PIN debe tener exactamente 4 dígitos.")
            
            elif opcion == 2:
                # Consultar saldo
                print("\n--- Consultar Saldo ---")
                numero_cuenta = input("Número de cuenta: ")
                cuenta = banco.obtener_cuenta(numero_cuenta)
                
                if cuenta:
                    pin = input("Ingrese su PIN: ")
                    if cuenta.verificar_pin(pin):
                        print(f"\nSaldo actual: S/ {cuenta.consultar_saldo():.2f}")
                    else:
                        print("PIN incorrecto.")
                else:
                    print("Cuenta no encontrada.")
            
            elif opcion == 3:
                # Depositar
                print("\n--- Depositar Dinero ---")
                numero_cuenta = input("Número de cuenta: ")
                cuenta = banco.obtener_cuenta(numero_cuenta)
                
                if cuenta:
                    pin = input("Ingrese su PIN: ")
                    if cuenta.verificar_pin(pin):
                        monto = float(input("Monto a depositar: "))
                        if cuenta.depositar(monto):
                            print(f"\n¡Depósito exitoso!")
                            print(f"Nuevo saldo: S/ {cuenta.saldo:.2f}")
                        else:
                            print("Error: El monto debe ser positivo.")
                    else:
                        print("PIN incorrecto.")
                else:
                    print("Cuenta no encontrada.")
            
            elif opcion == 4:
                # Retirar
                print("\n--- Retirar Dinero ---")
                numero_cuenta = input("Número de cuenta: ")
                cuenta = banco.obtener_cuenta(numero_cuenta)
                
                if cuenta:
                    pin = input("Ingrese su PIN: ")
                    if cuenta.verificar_pin(pin):
                        monto = float(input("Monto a retirar: "))
                        if cuenta.retirar(monto):
                            print(f"\n¡Retiro exitoso!")
                            print(f"Nuevo saldo: S/ {cuenta.saldo:.2f}")
                        else:
                            print("Error: Saldo insuficiente o monto inválido.")
                    else:
                        print("PIN incorrecto.")
                else:
                    print("Cuenta no encontrada.")
            
            elif opcion == 5:
                # Transferir
                print("\n--- Transferir Dinero ---")
                numero_origen = input("Número de cuenta origen: ")
                cuenta_origen = banco.obtener_cuenta(numero_origen)
                
                if cuenta_origen:
                    pin = input("Ingrese su PIN: ")
                    if cuenta_origen.verificar_pin(pin):
                        numero_destino = input("Número de cuenta destino: ")
                        cuenta_destino = banco.obtener_cuenta(numero_destino)
                        
                        if cuenta_destino:
                            monto = float(input("Monto a transferir: "))
                            if cuenta_origen.transferir(cuenta_destino, monto):
                                print(f"\n¡Transferencia exitosa!")
                                print(f"Saldo origen: S/ {cuenta_origen.saldo:.2f}")
                                print(f"Saldo destino: S/ {cuenta_destino.saldo:.2f}")
                            else:
                                print("Error: Saldo insuficiente o monto inválido.")
                        else:
                            print("Cuenta destino no encontrada.")
                    else:
                        print("PIN incorrecto.")
                else:
                    print("Cuenta origen no encontrada.")
            
            elif opcion == 6:
                # Ver historial
                print("\n--- Historial de Transacciones ---")
                numero_cuenta = input("Número de cuenta: ")
                cuenta = banco.obtener_cuenta(numero_cuenta)
                
                if cuenta:
                    pin = input("Ingrese su PIN: ")
                    if cuenta.verificar_pin(pin):
                        cuenta.mostrar_historial()
                    else:
                        print("PIN incorrecto.")
                else:
                    print("Cuenta no encontrada.")
            
            elif opcion == 7:
                # Listar cuentas
                banco.listar_cuentas()
            
            elif opcion == 8:
                # Eliminar cuenta
                print("\n--- Eliminar Cuenta ---")
                numero_cuenta = input("Número de cuenta a eliminar: ")
                cuenta = banco.obtener_cuenta(numero_cuenta)
                
                if cuenta:
                    pin = input("Ingrese su PIN: ")
                    if cuenta.verificar_pin(pin):
                        confirmacion = input(f"¿Está seguro de eliminar la cuenta {numero_cuenta}? (s/n): ")
                        if confirmacion.lower() == 's':
                            if banco.eliminar_cuenta(numero_cuenta):
                                print("Cuenta eliminada exitosamente.")
                            else:
                                print("Error al eliminar la cuenta.")
                        else:
                            print("Operación cancelada.")
                    else:
                        print("PIN incorrecto.")
                else:
                    print("Cuenta no encontrada.")
            
            else:
                print("\nOpción no válida. Por favor seleccione una opción entre 0 y 8.")
            
            input("\nPresione Enter para continuar...")
            limpiar_pantalla()
        
        except ValueError:
            print("\nError: Por favor ingrese un número válido.")
            input("Presione Enter para continuar...")
            limpiar_pantalla()

if __name__ == "__main__":
    main()


