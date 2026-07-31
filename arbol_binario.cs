using System;

namespace ArbolBinario
{
    // Clase que representa un nodo del árbol binario
    public class Nodo<T>
    {
        public T Dato { get; set; }
        public Nodo<T> Izquierdo { get; set; }
        public Nodo<T> Derecho { get; set; }

        public Nodo(T dato)
        {
            Dato = dato;
            Izquierdo = null;
            Derecho = null;
        }
    }

    // Clase que implementa el árbol binario de búsqueda
    public class ArbolBinarioBusqueda<T> where T : IComparable<T>
    {
        public Nodo<T> Raiz { get; private set; }

        public ArbolBinarioBusqueda()
        {
            Raiz = null;
        }

        // Insertar un nuevo valor en el árbol
        public void Insertar(T dato)
        {
            Raiz = InsertarRecursivo(Raiz, dato);
        }

        private Nodo<T> InsertarRecursivo(Nodo<T> nodo, T dato)
        {
            if (nodo == null)
            {
                return new Nodo<T>(dato);
            }

            if (dato.CompareTo(nodo.Dato) < 0)
            {
                nodo.Izquierdo = InsertarRecursivo(nodo.Izquierdo, dato);
            }
            else if (dato.CompareTo(nodo.Dato) > 0)
            {
                nodo.Derecho = InsertarRecursivo(nodo.Derecho, dato);
            }

            return nodo;
        }

        // Buscar un valor en el árbol
        public bool Buscar(T dato)
        {
            return BuscarRecursivo(Raiz, dato);
        }

        private bool BuscarRecursivo(Nodo<T> nodo, T dato)
        {
            if (nodo == null)
            {
                return false;
            }

            if (dato.CompareTo(nodo.Dato) == 0)
            {
                return true;
            }

            if (dato.CompareTo(nodo.Dato) < 0)
            {
                return BuscarRecursivo(nodo.Izquierdo, dato);
            }
            else
            {
                return BuscarRecursivo(nodo.Derecho, dato);
            }
        }

        // Recorrido en orden (in-order): Izquierdo - Raíz - Derecho
        public void RecorridoEnOrden()
        {
            RecorridoEnOrdenRecursivo(Raiz);
            Console.WriteLine();
        }

        private void RecorridoEnOrdenRecursivo(Nodo<T> nodo)
        {
            if (nodo != null)
            {
                RecorridoEnOrdenRecursivo(nodo.Izquierdo);
                Console.Write(nodo.Dato + " ");
                RecorridoEnOrdenRecursivo(nodo.Derecho);
            }
        }

        // Recorrido pre-order: Raíz - Izquierdo - Derecho
        public void RecorridoPreOrden()
        {
            RecorridoPreOrdenRecursivo(Raiz);
            Console.WriteLine();
        }

        private void RecorridoPreOrdenRecursivo(Nodo<T> nodo)
        {
            if (nodo != null)
            {
                Console.Write(nodo.Dato + " ");
                RecorridoPreOrdenRecursivo(nodo.Izquierdo);
                RecorridoPreOrdenRecursivo(nodo.Derecho);
            }
        }

        // Recorrido post-order: Izquierdo - Derecho - Raíz
        public void RecorridoPostOrden()
        {
            RecorridoPostOrdenRecursivo(Raiz);
            Console.WriteLine();
        }

        private void RecorridoPostOrdenRecursivo(Nodo<T> nodo)
        {
            if (nodo != null)
            {
                RecorridoPostOrdenRecursivo(nodo.Izquierdo);
                RecorridoPostOrdenRecursivo(nodo.Derecho);
                Console.Write(nodo.Dato + " ");
            }
        }

        // Calcular la altura del árbol
        public int Altura()
        {
            return AlturaRecursiva(Raiz);
        }

        private int AlturaRecursiva(Nodo<T> nodo)
        {
            if (nodo == null)
            {
                return 0;
            }

            int alturaIzquierda = AlturaRecursiva(nodo.Izquierdo);
            int alturaDerecha = AlturaRecursiva(nodo.Derecho);

            return Math.Max(alturaIzquierda, alturaDerecha) + 1;
        }

        // Contar el número de nodos
        public int ContarNodos()
        {
            return ContarNodosRecursivo(Raiz);
        }

        private int ContarNodosRecursivo(Nodo<T> nodo)
        {
            if (nodo == null)
            {
                return 0;
            }

            return 1 + ContarNodosRecursivo(nodo.Izquierdo) + ContarNodosRecursivo(nodo.Derecho);
        }
    }

    // Programa principal para demostrar el uso del árbol binario
    class Program
    {
        static void Main(string[] args)
        {
            ArbolBinarioBusqueda<int> arbol = new ArbolBinarioBusqueda<int>();

            // Insertar valores
            Console.WriteLine("=== Árbol Binario de Búsqueda en C# ===\n");
            
            int[] valores = { 50, 30, 70, 20, 40, 60, 80 };
            Console.WriteLine("Insertando valores: " + string.Join(", ", valores));
            
            foreach (int valor in valores)
            {
                arbol.Insertar(valor);
            }

            // Mostrar recorridos
            Console.WriteLine("\nRecorrido en orden (In-order):");
            arbol.RecorridoEnOrden();

            Console.WriteLine("Recorrido pre-order:");
            arbol.RecorridoPreOrden();

            Console.WriteLine("Recorrido post-order:");
            arbol.RecorridoPostOrden();

            // Buscar valores
            Console.WriteLine("\nBúsqueda de valores:");
            Console.WriteLine("¿Existe el valor 40? " + arbol.Buscar(40));
            Console.WriteLine("¿Existe el valor 100? " + arbol.Buscar(100));

            // Mostrar estadísticas
            Console.WriteLine("\nEstadísticas del árbol:");
            Console.WriteLine("Altura del árbol: " + arbol.Altura());
            Console.WriteLine("Número de nodos: " + arbol.ContarNodos());
        }
    }
}
