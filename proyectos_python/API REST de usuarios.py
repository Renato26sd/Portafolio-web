"""
API REST de Usuarios con Flask
API RESTful para gestión de usuarios
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
from datetime import datetime
import uuid

app = Flask(__name__)
CORS(app)  # Habilitar CORS para todas las rutas


# Base de datos en memoria (simulada)
usuarios_db = {}


class Usuario:
    """Clase que representa un usuario"""
    
    def __init__(self, id, nombre, email, edad=None):
        self.id = id
        self.nombre = nombre
        self.email = email
        self.edad = edad
        self.fecha_creacion = datetime.now().isoformat()
        self.activo = True
    
    def a_diccionario(self):
        """Convierte el usuario a diccionario"""
        return {
            'id': self.id,
            'nombre': self.nombre,
            'email': self.email,
            'edad': self.edad,
            'fecha_creacion': self.fecha_creacion,
            'activo': self.activo
        }


# Rutas de la API REST

@app.route('/', methods=['GET'])
def home():
    """Ruta principal - Documentación de la API"""
    return jsonify({
        'mensaje': 'API REST de Usuarios',
        'version': '1.0',
        'endpoints': {
            'GET /': 'Documentación de la API',
            'GET /usuarios': 'Obtener todos los usuarios',
            'GET /usuarios/<id>': 'Obtener un usuario por ID',
            'POST /usuarios': 'Crear un nuevo usuario',
            'PUT /usuarios/<id>': 'Actualizar un usuario existente',
            'DELETE /usuarios/<id>': 'Eliminar un usuario',
            'GET /usuarios/buscar': 'Buscar usuarios por nombre o email'
        }
    })


@app.route('/usuarios', methods=['GET'])
def obtener_usuarios():
    """Obtener todos los usuarios (READ - All)"""
    usuarios = [usuario.a_diccionario() for usuario in usuarios_db.values()]
    return jsonify({
        'success': True,
        'cantidad': len(usuarios),
        'data': usuarios
    }), 200


@app.route('/usuarios/<usuario_id>', methods=['GET'])
def obtener_usuario(usuario_id):
    """Obtener un usuario por ID (READ - One)"""
    usuario = usuarios_db.get(usuario_id)
    
    if usuario:
        return jsonify({
            'success': True,
            'data': usuario.a_diccionario()
        }), 200
    else:
        return jsonify({
            'success': False,
            'mensaje': 'Usuario no encontrado'
        }), 404


@app.route('/usuarios', methods=['POST'])
def crear_usuario():
    """Crear un nuevo usuario (CREATE)"""
    datos = request.get_json()
    
    # Validar datos requeridos
    if not datos or 'nombre' not in datos or 'email' not in datos:
        return jsonify({
            'success': False,
            'mensaje': 'Nombre y email son requeridos'
        }), 400
    
    nombre = datos['nombre']
    email = datos['email']
    edad = datos.get('edad')
    
    # Verificar si el email ya existe
    for usuario in usuarios_db.values():
        if usuario.email == email:
            return jsonify({
                'success': False,
                'mensaje': 'El email ya está registrado'
            }), 400
    
    # Crear nuevo usuario
    usuario_id = str(uuid.uuid4())
    nuevo_usuario = Usuario(usuario_id, nombre, email, edad)
    usuarios_db[usuario_id] = nuevo_usuario
    
    return jsonify({
        'success': True,
        'mensaje': 'Usuario creado exitosamente',
        'data': nuevo_usuario.a_diccionario()
    }), 201


@app.route('/usuarios/<usuario_id>', methods=['PUT'])
def actualizar_usuario(usuario_id):
    """Actualizar un usuario existente (UPDATE)"""
    usuario = usuarios_db.get(usuario_id)
    
    if not usuario:
        return jsonify({
            'success': False,
            'mensaje': 'Usuario no encontrado'
        }), 404
    
    datos = request.get_json()
    
    if not datos:
        return jsonify({
            'success': False,
            'mensaje': 'No se proporcionaron datos para actualizar'
        }), 400
    
    # Actualizar campos proporcionados
    if 'nombre' in datos:
        usuario.nombre = datos['nombre']
    if 'email' in datos:
        # Verificar si el nuevo email ya existe
        for u in usuarios_db.values():
            if u.email == datos['email'] and u.id != usuario_id:
                return jsonify({
                    'success': False,
                    'mensaje': 'El email ya está registrado'
                }), 400
        usuario.email = datos['email']
    if 'edad' in datos:
        usuario.edad = datos['edad']
    if 'activo' in datos:
        usuario.activo = datos['activo']
    
    return jsonify({
        'success': True,
        'mensaje': 'Usuario actualizado exitosamente',
        'data': usuario.a_diccionario()
    }), 200


@app.route('/usuarios/<usuario_id>', methods=['DELETE'])
def eliminar_usuario(usuario_id):
    """Eliminar un usuario (DELETE)"""
    usuario = usuarios_db.get(usuario_id)
    
    if not usuario:
        return jsonify({
            'success': False,
            'mensaje': 'Usuario no encontrado'
        }), 404
    
    # Marcar como inactivo en lugar de eliminar físicamente
    usuario.activo = False
    
    return jsonify({
        'success': True,
        'mensaje': 'Usuario eliminado (marcado como inactivo)'
    }), 200


@app.route('/usuarios/buscar', methods=['GET'])
def buscar_usuarios():
    """Buscar usuarios por nombre o email"""
    query = request.args.get('q', '').lower()
    
    if not query:
        return jsonify({
            'success': False,
            'mensaje': 'Proporciona un parámetro de búsqueda (q)'
        }), 400
    
    resultados = []
    for usuario in usuarios_db.values():
        if (query in usuario.nombre.lower() or 
            query in usuario.email.lower()) and usuario.activo:
            resultados.append(usuario.a_diccionario())
    
    return jsonify({
        'success': True,
        'cantidad': len(resultados),
        'data': resultados
    }), 200


@app.route('/usuarios/estadisticas', methods=['GET'])
def estadisticas():
    """Obtener estadísticas de usuarios"""
    total = len(usuarios_db)
    activos = sum(1 for u in usuarios_db.values() if u.activo)
    inactivos = total - activos
    
    if total > 0:
        promedio_edad = sum(u.edad for u in usuarios_db.values() if u.edad) / sum(1 for u in usuarios_db.values() if u.edad)
    else:
        promedio_edad = 0
    
    return jsonify({
        'success': True,
        'data': {
            'total_usuarios': total,
            'usuarios_activos': activos,
            'usuarios_inactivos': inactivos,
            'promedio_edad': round(promedio_edad, 2) if promedio_edad else None
        }
    }), 200


# Manejadores de error

@app.errorhandler(404)
def no_encontrado(error):
    """Manejador de error 404"""
    return jsonify({
        'success': False,
        'mensaje': 'Recurso no encontrado'
    }), 404


@app.errorhandler(405)
def metodo_no_permitido(error):
    """Manejador de error 405"""
    return jsonify({
        'success': False,
        'mensaje': 'Método no permitido'
    }), 405


@app.errorhandler(500)
def error_interno(error):
    """Manejador de error 500"""
    return jsonify({
        'success': False,
        'mensaje': 'Error interno del servidor'
    }), 500


# Datos de ejemplo para pruebas
def inicializar_datos():
    """Inicializa datos de ejemplo"""
    usuarios_ejemplo = [
        Usuario(str(uuid.uuid4()), "Juan Pérez", "juan@example.com", 25),
        Usuario(str(uuid.uuid4()), "María García", "maria@example.com", 30),
        Usuario(str(uuid.uuid4()), "Carlos López", "carlos@example.com", 28)
    ]
    
    for usuario in usuarios_ejemplo:
        usuarios_db[usuario.id] = usuario


if __name__ == '__main__':
    # Inicializar datos de ejemplo
    inicializar_datos()
    
    print("=== API REST de Usuarios ===")
    print("Servidor iniciado en http://localhost:5000")
    print("Documentación disponible en http://localhost:5000/")
    print("\nEndpoints disponibles:")
    print("  GET    /                          - Documentación")
    print("  GET    /usuarios                  - Listar usuarios")
    print("  GET    /usuarios/<id>             - Obtener usuario")
    print("  POST   /usuarios                  - Crear usuario")
    print("  PUT    /usuarios/<id>             - Actualizar usuario")
    print("  DELETE /usuarios/<id>             - Eliminar usuario")
    print("  GET    /usuarios/buscar?q=<query> - Buscar usuarios")
    print("  GET    /usuarios/estadisticas     - Estadísticas")
    
    app.run(debug=True, host='0.0.0.0', port=5000)
