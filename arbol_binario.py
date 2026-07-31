"""
Árbol Binario de Búsqueda en Python
Implementación de un árbol binario con operaciones básicas
"""

class Nodo:
    """Clase que representa un nodo del árbol binario"""
    def __init__(self, dato):
        self.dato = dato
        self.izquierdo = None
        self.derecho = None
    
    def __str__(self):
        return str(self.dato)


class ArbolBinarioBusqueda:
    """Clase que implementa el árbol binario de búsqueda"""
    
    def __init__(self):
        self.raiz = None
    
    def insertar(self, dato):
        """Insertar un nuevo valor en el árbol"""
        self.raiz = self._insertar_recursivo(self.raiz, dato)
    
    def _insertar_recursivo(self, nodo, dato):
        if nodo is None:
            return Nodo(dato)
        
        if dato < nodo.dato:
            nodo.izquierdo = self._insertar_recursivo(nodo.izquierdo, dato)
        elif dato > nodo.dato:
            nodo.derecho = self._insertar_recursivo(nodo.derecho, dato)
        
        return nodo
    
    def buscar(self, dato):
        """Buscar un valor en el árbol"""
        return self._buscar_recursivo(self.raiz, dato)
    
    def _buscar_recursivo(self, nodo, dato):
        if nodo is None:
            return False
        
        if dato == nodo.dato:
            return True
        elif dato < nodo.dato:
            return self._buscar_recursivo(nodo.izquierdo, dato)
        else:
            return self._buscar_recursivo(nodo.derecho, dato)
    
    def recorrido_en_orden(self):
        """Recorrido en orden (in-order): Izquierdo - Raíz - Derecho"""
        resultado = []
        self._recorrido_en_orden_recursivo(self.raiz, resultado)
        return resultado
    
    def _recorrido_en_orden_recursivo(self, nodo, resultado):
        if nodo is not None:
            self._recorrido_en_orden_recursivo(nodo.izquierdo, resultado)
            resultado.append(nodo.dato)
            self._recorrido_en_orden_recursivo(nodo.derecho, resultado)
    
    def recorrido_pre_orden(self):
        """Recorrido pre-order: Raíz - Izquierdo - Derecho"""
        resultado = []
        self._recorrido_pre_orden_recursivo(self.raiz, resultado)
        return resultado
    
    def _recorrido_pre_orden_recursivo(self, nodo, resultado):
        if nodo is not None:
            resultado.append(nodo.dato)
            self._recorrido_pre_orden_recursivo(nodo.izquierdo, resultado)
            self._recorrido_pre_orden_recursivo(nodo.derecho, resultado)
    
    def recorrido_post_orden(self):
        """Recorrido post-order: Izquierdo - Derecho - Raíz"""
        resultado = []
        self._recorrido_post_orden_recursivo(self.raiz, resultado)
        return resultado
    
    def _recorrido_post_orden_recursivo(self, nodo, resultado):
        if nodo is not None:
            self._recorrido_post_orden_recursivo(nodo.izquierdo, resultado)
            self._recorrido_post_orden_recursivo(nodo.derecho, resultado)
            resultado.append(nodo.dato)
    
    def altura(self):
        """Calcular la altura del árbol"""
        return self._altura_recursiva(self.raiz)
    
    def _altura_recursiva(self, nodo):
        if nodo is None:
            return 0
        
        altura_izquierda = self._altura_recursiva(nodo.izquierdo)
        altura_derecha = self._altura_recursiva(nodo.derecho)
        
        return max(altura_izquierda, altura_derecha) + 1
    
    def contar_nodos(self):
        """Contar el número de nodos"""
        return self._contar_nodos_recursivo(self.raiz)
    
    def _contar_nodos_recursivo(self, nodo):
        if nodo is None:
            return 0
        
        return 1 + self._contar_nodos_recursivo(nodo.izquierdo) + self._contar_nodos_recursivo(nodo.derecho)
    
    def eliminar(self, dato):
        """Eliminar un valor del árbol"""
        self.raiz = self._eliminar_recursivo(self.raiz, dato)
    
    def _eliminar_recursivo(self, nodo, dato):
        if nodo is None:
            return None
        
        if dato < nodo.dato:
            nodo.izquierdo = self._eliminar_recursivo(nodo.izquierdo, dato)
        elif dato > nodo.dato:
            nodo.derecho = self._eliminar_recursivo(nodo.derecho, dato)
        else:
            # Nodo encontrado
            
            # Caso 1: Nodo sin hijos
            if nodo.izquierdo is None and nodo.derecho is None:
                return None
            
            # Caso 2: Nodo con un hijo
            if nodo.izquierdo is None:
                return nodo.derecho
            if nodo.derecho is None:
                return nodo.izquierdo
            
            # Caso 3: Nodo con dos hijos
            # Encontrar el sucesor (mínimo del subárbol derecho)
            sucesor = self._encontrar_minimo(nodo.derecho)
            nodo.dato = sucesor.dato
            nodo.derecho = self._eliminar_recursivo(nodo.derecho, sucesor.dato)
        
        return nodo
    
    def _encontrar_minimo(self, nodo):
        actual = nodo
        while actual.izquierdo is not None:
            actual = actual.izquierdo
        return actual
    
    def imprimir_arbol(self):
        """Imprimir el árbol de forma visual"""
        self._imprimir_arbol_recursivo(self.raiz, 0)
    
    def _imprimir_arbol_recursivo(self, nodo, nivel):
        if nodo is not None:
            self._imprimir_arbol_recursivo(nodo.derecho, nivel + 1)
            print("  " * nivel + str(nodo.dato))
            self._imprimir_arbol_recursivo(nodo.izquierdo, nivel + 1)


# Programa principal para demostrar el uso del árbol binario
def main():
    print("=== Árbol Binario de Búsqueda en Python ===\n")
    
    arbol = ArbolBinarioBusqueda()
    
    # Insertar valores
    valores = [50, 30, 70, 20, 40, 60, 80]
    print(f"Insertando valores: {valores}")
    
    for valor in valores:
        arbol.insertar(valor)
    
    # Mostrar recorridos
    print("\nRecorrido en orden (In-order):")
    print(arbol.recorrido_en_orden())
    
    print("Recorrido pre-order:")
    print(arbol.recorrido_pre_orden())
    
    print("Recorrido post-order:")
    print(arbol.recorrido_post_orden())
    
    # Buscar valores
    print("\nBúsqueda de valores:")
    print(f"¿Existe el valor 40? {arbol.buscar(40)}")
    print(f"¿Existe el valor 100? {arbol.buscar(100)}")
    
    # Mostrar estadísticas
    print("\nEstadísticas del árbol:")
    print(f"Altura del árbol: {arbol.altura()}")
    print(f"Número de nodos: {arbol.contar_nodos()}")
    
    # Imprimir árbol visualmente
    print("\nRepresentación visual del árbol:")
    arbol.imprimir_arbol()
    
    # Eliminar un nodo
    print("\nEliminando el valor 30...")
    arbol.eliminar(30)
    print("Recorrido en orden después de eliminar:")
    print(arbol.recorrido_en_orden())


if __name__ == "__main__":
    main()
