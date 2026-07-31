"""
Tienda Virtual - Aplicación Web de Comercio Electrónico

Este archivo ahora es una referencia a la aplicación web completa.
La aplicación web se encuentra en la carpeta tienda_virtual/

Para ejecutar la aplicación:
1. Navega a la carpeta tienda_virtual/
2. Instala las dependencias: pip install -r requirements.txt
3. Ejecuta el servidor: python app.py
4. Abre frontend/index.html en tu navegador

Características:
- Catálogo de productos interactivo
- Carrito de compras funcional
- Sistema de pedidos
- Dashboard administrativo completo
- Base de datos SQLite
- API REST con Flask

Documentación completa: tienda_virtual/README.md
"""

import subprocess
import os
import webbrowser

def launch_tienda_virtual():
    """Lanza la aplicación web de la tienda virtual"""
    # Cambiar al directorio de la tienda virtual
    tienda_dir = os.path.join(os.path.dirname(__file__), 'tienda_virtual')
    
    if not os.path.exists(tienda_dir):
        print("Error: No se encuentra el directorio tienda_virtual/")
        return
    
    os.chdir(tienda_dir)
    
    # Iniciar el servidor Flask
    print("Iniciando servidor Flask...")
    print("La tienda estará disponible en: http://localhost:5000")
    print("Abre frontend/index.html en tu navegador para ver la tienda")
    print("Abre frontend/dashboard.html en tu navegador para ver el dashboard")
    print("\nPresiona Ctrl+C para detener el servidor\n")
    
    try:
        subprocess.run(['python', 'app.py'])
    except KeyboardInterrupt:
        print("\nServidor detenido.")

if __name__ == '__main__':
    launch_tienda_virtual()
