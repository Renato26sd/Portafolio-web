"""
Calculadora Básica en Python
Implementa operaciones matemáticas fundamentales con interfaz de línea de comandos
"""

import math


class Calculadora:
    """Clase que implementa una calculadora básica"""
    
    @staticmethod
    def sumar(a, b):
        """Suma dos números"""
        return a + b
    
    @staticmethod
    def restar(a, b):
        """Resta dos números"""
        return a - b
    
    @staticmethod
    def multiplicar(a, b):
        """Multiplica dos números"""
        return a * b
    
    @staticmethod
    def dividir(a, b):
        """Divide dos números"""
        if b == 0:
            raise ValueError("No se puede dividir por cero")
        return a / b
    
    @staticmethod
    def potencia(base, exponente):
        """Calcula la potencia de un número"""
        return base ** exponente
    
    @staticmethod
    def raiz_cuadrada(numero):
        """Calcula la raíz cuadrada de un número"""
        if numero < 0:
            raise ValueError("No se puede calcular la raíz cuadrada de un número negativo")
        return math.sqrt(numero)
    
    @staticmethod
    def porcentaje(valor, porcentaje):
        """Calcula el porcentaje de un valor"""
        return (valor * porcentaje) / 100
    
    @staticmethod
    def factorial(numero):
        """Calcula el factorial de un número"""
        if numero < 0:
            raise ValueError("El factorial no está definido para números negativos")
        if numero == 0 or numero == 1:
            return 1
        return math.factorial(numero)
    
    @staticmethod
    def logaritmo(numero, base=10):
        """Calcula el logaritmo de un número"""
        if numero <= 0:
            raise ValueError("El logaritmo no está definido para números menores o iguales a cero")
        if base == 10:
            return math.log10(numero)
        elif base == math.e:
            return math.log(numero)
        else:
            return math.log(numero, base)
    
    @staticmethod
    def seno(angulo_grados):
        """Calcula el seno de un ángulo en grados"""
        return math.sin(math.radians(angulo_grados))
    
    @staticmethod
    def coseno(angulo_grados):
        """Calcula el coseno de un ángulo en grados"""
        return math.cos(math.radians(angulo_grados))
    
    @staticmethod
    def tangente(angulo_grados):
        """Calcula la tangente de un ángulo en grados"""
        return math.tan(math.radians(angulo_grados))


def obtener_numero(mensaje):
    """Función auxiliar para obtener un número del usuario"""
    while True:
        try:
            valor = float(input(mensaje))
            return valor
        except ValueError:
            print("Error: Por favor ingrese un número válido.")


def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')


def mostrar_menu():
    """Muestra el menú de operaciones disponibles"""
    print("\n" + "=" * 50)
    print("           CALCULADORA BÁSICA")
    print("=" * 50)
    print("1. Suma")
    print("2. Resta")
    print("3. Multiplicación")
    print("4. División")
    print("5. Potencia")
    print("6. Raíz Cuadrada")
    print("7. Porcentaje")
    print("8. Factorial")
    print("9. Logaritmo")
    print("10. Seno")
    print("11. Coseno")
    print("12. Tangente")
    print("0. Salir")
    print("=" * 50)


def main():
    """Función principal de la calculadora"""
    calc = Calculadora()
    
    print("=== CALCULADORA BÁSICA EN PYTHON ===")
    
    while True:
        mostrar_menu()
        
        try:
            opcion = int(input("\nSeleccione una operación (0-12): "))
            
            if opcion == 0:
                print("\n¡Gracias por usar la calculadora! ¡Hasta pronto!")
                break
            
            elif opcion == 1:
                # Suma
                a = obtener_numero("Ingrese el primer número: ")
                b = obtener_numero("Ingrese el segundo número: ")
                resultado = calc.sumar(a, b)
                print(f"\nResultado: {a} + {b} = {resultado}")
            
            elif opcion == 2:
                # Resta
                a = obtener_numero("Ingrese el primer número: ")
                b = obtener_numero("Ingrese el segundo número: ")
                resultado = calc.restar(a, b)
                print(f"\nResultado: {a} - {b} = {resultado}")
            
            elif opcion == 3:
                # Multiplicación
                a = obtener_numero("Ingrese el primer número: ")
                b = obtener_numero("Ingrese el segundo número: ")
                resultado = calc.multiplicar(a, b)
                print(f"\nResultado: {a} × {b} = {resultado}")
            
            elif opcion == 4:
                # División
                a = obtener_numero("Ingrese el numerador: ")
                b = obtener_numero("Ingrese el denominador: ")
                try:
                    resultado = calc.dividir(a, b)
                    print(f"\nResultado: {a} ÷ {b} = {resultado}")
                except ValueError as e:
                    print(f"\nError: {e}")
            
            elif opcion == 5:
                # Potencia
                base = obtener_numero("Ingrese la base: ")
                exponente = obtener_numero("Ingrese el exponente: ")
                resultado = calc.potencia(base, exponente)
                print(f"\nResultado: {base}^{exponente} = {resultado}")
            
            elif opcion == 6:
                # Raíz Cuadrada
                numero = obtener_numero("Ingrese el número: ")
                try:
                    resultado = calc.raiz_cuadrada(numero)
                    print(f"\nResultado: √{numero} = {resultado:.4f}")
                except ValueError as e:
                    print(f"\nError: {e}")
            
            elif opcion == 7:
                # Porcentaje
                valor = obtener_numero("Ingrese el valor: ")
                porcentaje = obtener_numero("Ingrese el porcentaje a calcular: ")
                resultado = calc.porcentaje(valor, porcentaje)
                print(f"\nResultado: {porcentaje}% de {valor} = {resultado}")
            
            elif opcion == 8:
                # Factorial
                numero = int(obtener_numero("Ingrese un número entero positivo: "))
                try:
                    resultado = calc.factorial(numero)
                    print(f"\nResultado: {numero}! = {resultado}")
                except ValueError as e:
                    print(f"\nError: {e}")
            
            elif opcion == 9:
                # Logaritmo
                numero = obtener_numero("Ingrese el número: ")
                print("Seleccione la base del logaritmo:")
                print("1. Base 10")
                print("2. Base e (natural)")
                print("3. Otra base")
                base_opcion = int(input("Opción: "))
                
                try:
                    if base_opcion == 1:
                        resultado = calc.logaritmo(numero, 10)
                        print(f"\nResultado: log₁₀({numero}) = {resultado:.4f}")
                    elif base_opcion == 2:
                        resultado = calc.logaritmo(numero, math.e)
                        print(f"\nResultado: ln({numero}) = {resultado:.4f}")
                    elif base_opcion == 3:
                        base = obtener_numero("Ingrese la base: ")
                        resultado = calc.logaritmo(numero, base)
                        print(f"\nResultado: log_{base}({numero}) = {resultado:.4f}")
                    else:
                        print("Opción no válida")
                except ValueError as e:
                    print(f"\nError: {e}")
            
            elif opcion == 10:
                # Seno
                angulo = obtener_numero("Ingrese el ángulo en grados: ")
                resultado = calc.seno(angulo)
                print(f"\nResultado: sin({angulo}°) = {resultado:.4f}")
            
            elif opcion == 11:
                # Coseno
                angulo = obtener_numero("Ingrese el ángulo en grados: ")
                resultado = calc.coseno(angulo)
                print(f"\nResultado: cos({angulo}°) = {resultado:.4f}")
            
            elif opcion == 12:
                # Tangente
                angulo = obtener_numero("Ingrese el ángulo en grados: ")
                resultado = calc.tangente(angulo)
                print(f"\nResultado: tan({angulo}°) = {resultado:.4f}")
            
            else:
                print("\nOpción no válida. Por favor seleccione una opción entre 0 y 12.")
            
            input("\nPresione Enter para continuar...")
            limpiar_pantalla()
        
        except ValueError:
            print("\nError: Por favor ingrese un número válido.")
            input("Presione Enter para continuar...")
            limpiar_pantalla()


if __name__ == "__main__":
    main()
