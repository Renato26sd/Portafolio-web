"""
Sistema de Notas de Estudiantes
Gestión de calificaciones y promedios de estudiantes
"""

from datetime import datetime


class Estudiante:
    """Clase que representa un estudiante"""
    
    def __init__(self, id_estudiante, nombre, apellido, grado):
        self.id_estudiante = id_estudiante
        self.nombre = nombre
        self.apellido = apellido
        self.grado = grado
        self.notas = {}  # {asignatura: [nota1, nota2, ...]}
    
    @property
    def nombre_completo(self):
        return f"{self.nombre} {self.apellido}"
    
    def agregar_nota(self, asignatura, nota):
        """Agrega una nota a una asignatura"""
        if asignatura not in self.notas:
            self.notas[asignatura] = []
        if 0 <= nota <= 20:
            self.notas[asignatura].append(nota)
            return True
        return False
    
    def promedio_asignatura(self, asignatura):
        """Calcula el promedio de una asignatura"""
        if asignatura in self.notas and self.notas[asignatura]:
            return sum(self.notas[asignatura]) / len(self.notas[asignatura])
        return 0
    
    def promedio_general(self):
        """Calcula el promedio general del estudiante"""
        if not self.notas:
            return 0
        promedios = [self.promedio_asignatura(asig) for asig in self.notas]
        return sum(promedios) / len(promedios)
    
    def mejor_asignatura(self):
        """Retorna la asignatura con mejor promedio"""
        if not self.notas:
            return None
        return max(self.notas.keys(), key=lambda x: self.promedio_asignatura(x))
    
    def peor_asignatura(self):
        """Retorna la asignatura con peor promedio"""
        if not self.notas:
            return None
        return min(self.notas.keys(), key=lambda x: self.promedio_asignatura(x))
    
    def estado_academico(self):
        """Retorna el estado académico del estudiante"""
        promedio = self.promedio_general()
        if promedio >= 14:
            return "Excelente"
        elif promedio >= 11:
            return "Aprobado"
        else:
            return "Desaprobado"
    
    def __str__(self):
        return f"{self.id_estudiante} - {self.nombre_completo} - Grado: {self.grado} - Promedio: {self.promedio_general():.2f}"


class SistemaNotas:
    """Clase que gestiona el sistema de notas"""
    
    def __init__(self, nombre_institucion):
        self.nombre_institucion = nombre_institucion
        self.estudiantes = {}
        self.asignaturas = []
        self.contador_id = 1
    
    def agregar_asignatura(self, asignatura):
        """Agrega una asignatura al sistema"""
        if asignatura not in self.asignaturas:
            self.asignaturas.append(asignatura)
            return True
        return False
    
    def agregar_estudiante(self, nombre, apellido, grado):
        """Agrega un nuevo estudiante"""
        id_estudiante = f"EST-{str(self.contador_id).zfill(4)}"
        estudiante = Estudiante(id_estudiante, nombre, apellido, grado)
        self.estudiantes[id_estudiante] = estudiante
        self.contador_id += 1
        return estudiante
    
    def obtener_estudiante(self, id_estudiante):
        """Obtiene un estudiante por su ID"""
        return self.estudiantes.get(id_estudiante)
    
    def registrar_nota(self, id_estudiante, asignatura, nota):
        """Registra una nota para un estudiante"""
        estudiante = self.obtener_estudiante(id_estudiante)
        if estudiante:
            return estudiante.agregar_nota(asignatura, nota)
        return False
    
    def listar_estudiantes(self):
        """Lista todos los estudiantes"""
        print(f"\n=== Estudiantes de {self.nombre_institucion} ===")
        print("-" * 80)
        
        if not self.estudiantes:
            print("No hay estudiantes registrados.")
            return
        
        for estudiante in self.estudiantes.values():
            print(f"ID: {estudiante.id_estudiante}")
            print(f"Nombre: {estudiante.nombre_completo}")
            print(f"Grado: {estudiante.grado}")
            print(f"Promedio General: {estudiante.promedio_general():.2f}")
            print(f"Estado: {estudiante.estado_academico()}")
            print("-" * 80)
    
    def boleta_notas(self, id_estudiante):
        """Genera la boleta de notas de un estudiante"""
        estudiante = self.obtener_estudiante(id_estudiante)
        if not estudiante:
            print("Estudiante no encontrado.")
            return
        
        print("\n" + "=" * 60)
        print(f"BOLETA DE NOTAS - {self.nombre_institucion}")
        print("=" * 60)
        print(f"Estudiante: {estudiante.nombre_completo}")
        print(f"ID: {estudiante.id_estudiante}")
        print(f"Grado: {estudiante.grado}")
        print(f"Fecha: {datetime.now().strftime('%Y-%m-%d')}")
        print("-" * 60)
        print("ASIGNATURA\tNOTAS\t\tPROMEDIO")
        print("-" * 60)
        
        for asignatura in self.asignaturas:
            if asignatura in estudiante.notas:
                notas = estudiante.notas[asignatura]
                notas_str = ", ".join(str(n) for n in notas)
                promedio = estudiante.promedio_asignatura(asignatura)
                print(f"{asignatura}\t{notas_str}\t{promedio:.2f}")
        
        print("-" * 60)
        print(f"PROMEDIO GENERAL: {estudiante.promedio_general():.2f}")
        print(f"ESTADO: {estudiante.estado_academico()}")
        print(f"MEJOR ASIGNATURA: {estudiante.mejor_asignatura() or 'N/A'}")
        print(f"PEOR ASIGNATURA: {estudiante.peor_asignatura() or 'N/A'}")
        print("=" * 60)
    
    def ranking_estudiantes(self):
        """Muestra el ranking de estudiantes por promedio"""
        print(f"\n=== Ranking de Estudiantes - {self.nombre_institucion} ===")
        print("-" * 80)
        
        ranking = sorted(self.estudiantes.values(), 
                        key=lambda x: x.promedio_general(), 
                        reverse=True)
        
        for i, estudiante in enumerate(ranking, 1):
            print(f"{i}. {estudiante.nombre_completo} - Promedio: {estudiante.promedio_general():.2f}")
    
    def promedio_institucion(self):
        """Calcula el promedio general de la institución"""
        if not self.estudiantes:
            return 0
        return sum(e.promedio_general() for e in self.estudiantes.values()) / len(self.estudiantes)
    
    def estudiantes_por_estado(self):
        """Muestra la cantidad de estudiantes por estado académico"""
        estados = {"Excelente": 0, "Aprobado": 0, "Desaprobado": 0}
        
        for estudiante in self.estudiantes.values():
            estado = estudiante.estado_academico()
            estados[estado] += 1
        
        print("\n=== Estudiantes por Estado Académico ===")
        for estado, cantidad in estados.items():
            print(f"{estado}: {cantidad} estudiante(s)")


def limpiar_pantalla():
    """Limpia la pantalla de la consola"""
    import os
    os.system('cls' if os.name == 'nt' else 'clear')


def mostrar_menu():
    """Muestra el menú principal"""
    print("\n" + "=" * 60)
    print("   SISTEMA DE NOTAS DE ESTUDIANTES")
    print("=" * 60)
    print("1. Agregar asignatura")
    print("2. Agregar estudiante")
    print("3. Registrar nota")
    print("4. Ver boleta de notas")
    print("5. Listar estudiantes")
    print("6. Ranking de estudiantes")
    print("7. Promedio de la institución")
    print("8. Estudiantes por estado")
    print("0. Salir")
    print("=" * 60)


def main():
    """Función principal del sistema de notas"""
    print("=== SISTEMA DE NOTAS DE ESTUDIANTES ===")
    nombre = input("Nombre de la institución: ")
    sistema = SistemaNotas(nombre)
    
    # Agregar asignaturas por defecto
    asignaturas_default = ["Matemáticas", "Lenguaje", "Ciencias", "Historia", "Inglés"]
    for asig in asignaturas_default:
        sistema.agregar_asignatura(asig)
    
    print(f"\nInstitución '{nombre}' configurada.")
    print(f"Asignaturas agregadas: {', '.join(sistema.asignaturas)}")
    
    while True:
        mostrar_menu()
        
        try:
            opcion = int(input("\nSeleccione una opción (0-8): "))
            
            if opcion == 0:
                print("\n¡Gracias por usar el Sistema de Notas! ¡Hasta pronto!")
                break
            
            elif opcion == 1:
                # Agregar asignatura
                print("\n--- Agregar Asignatura ---")
                asignatura = input("Nombre de la asignatura: ")
                if sistema.agregar_asignatura(asignatura):
                    print("Asignatura agregada exitosamente.")
                else:
                    print("La asignatura ya existe.")
            
            elif opcion == 2:
                # Agregar estudiante
                print("\n--- Agregar Estudiante ---")
                nombre = input("Nombre: ")
                apellido = input("Apellido: ")
                grado = input("Grado: ")
                
                estudiante = sistema.agregar_estudiante(nombre, apellido, grado)
                print(f"\nEstudiante agregado exitosamente.")
                print(f"ID: {estudiante.id_estudiante}")
            
            elif opcion == 3:
                # Registrar nota
                print("\n--- Registrar Nota ---")
                id_estudiante = input("ID del estudiante: ")
                estudiante = sistema.obtener_estudiante(id_estudiante)
                
                if estudiante:
                    print("Asignaturas disponibles:")
                    for i, asig in enumerate(sistema.asignaturas, 1):
                        print(f"{i}. {asig}")
                    
                    try:
                        idx = int(input("Seleccione asignatura (número): ")) - 1
                        if 0 <= idx < len(sistema.asignaturas):
                            asignatura = sistema.asignaturas[idx]
                            nota = float(input("Nota (0-20): "))
                            
                            if sistema.registrar_nota(id_estudiante, asignatura, nota):
                                print("Nota registrada exitosamente.")
                            else:
                                print("Error: La nota debe estar entre 0 y 20.")
                        else:
                            print("Selección inválida.")
                    except ValueError:
                        print("Error: Ingrese un número válido.")
                else:
                    print("Estudiante no encontrado.")
            
            elif opcion == 4:
                # Ver boleta
                print("\n--- Boleta de Notas ---")
                id_estudiante = input("ID del estudiante: ")
                sistema.boleta_notas(id_estudiante)
            
            elif opcion == 5:
                # Listar estudiantes
                sistema.listar_estudiantes()
            
            elif opcion == 6:
                # Ranking
                sistema.ranking_estudiantes()
            
            elif opcion == 7:
                # Promedio institución
                promedio = sistema.promedio_institucion()
                print(f"\nPromedio general de {sistema.nombre_institucion}: {promedio:.2f}")
            
            elif opcion == 8:
                # Estudiantes por estado
                sistema.estudiantes_por_estado()
            
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
