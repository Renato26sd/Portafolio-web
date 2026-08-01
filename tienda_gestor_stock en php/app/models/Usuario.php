<?php
/**
 * Modelo de Usuario
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Autenticar usuario
     */
    public function autenticar($usuario, $contraseña) {
        $sql = "SELECT u.*, r.nombre_rol 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                WHERE u.usuario = :usuario AND u.estado = 'activo'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario' => $usuario]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($contraseña, $user['contraseña'])) {
            // Verificar si la cuenta está bloqueada por demasiados intentos
            if ($user['intentos_fallidos'] >= MAX_LOGIN_ATTEMPTS) {
                return ['error' => 'Cuenta bloqueada por demasiados intentos fallidos. Contacte al administrador.'];
            }
            
            // Resetear intentos fallidos
            $this->resetearIntentos($user['id_usuario']);
            
            // Actualizar último acceso
            $this->actualizarUltimoAcceso($user['id_usuario']);
            
            // Obtener permisos del usuario
            $permisos = $this->obtenerPermisos($user['id_rol']);
            
            return [
                'id_usuario' => $user['id_usuario'],
                'nombres' => $user['nombres'],
                'apellidos' => $user['apellidos'],
                'correo' => $user['correo'],
                'usuario' => $user['usuario'],
                'id_rol' => $user['id_rol'],
                'nombre_rol' => $user['nombre_rol'],
                'permisos' => $permisos
            ];
        }
        
        // Incrementar intentos fallidos
        $this->incrementarIntentos($usuario);
        
        return ['error' => 'Usuario o contraseña incorrectos'];
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos($pagina = 1, $limite = ITEMS_PER_PAGE) {
        $offset = ($pagina - 1) * $limite;
        
        $sql = "SELECT u.*, r.nombre_rol 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                ORDER BY u.fecha_registro DESC 
                LIMIT :limite OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT u.*, r.nombre_rol 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                WHERE u.id_usuario = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Crear usuario
     */
    public function crear($datos) {
        $sql = "INSERT INTO usuarios (nombres, apellidos, correo, usuario, contraseña, id_rol, telefono, direccion, estado)
                VALUES (:nombres, :apellidos, :correo, :usuario, :contrasena, :id_rol, :telefono, :direccion, :estado)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'correo' => $datos['correo'],
            'usuario' => $datos['usuario'],
            'contrasena' => password_hash($datos['contraseña'], PASSWORD_DEFAULT),
            'id_rol' => $datos['id_rol'],
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'estado' => $datos['estado'] ?? 'activo'
        ]);

        return $this->db->lastInsertId();
    }
    
    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE usuarios SET 
                nombres = :nombres,
                apellidos = :apellidos,
                correo = :correo,
                usuario = :usuario,
                id_rol = :id_rol,
                telefono = :telefono,
                direccion = :direccion,
                estado = :estado";
        
        if (!empty($datos['contraseña'])) {
            $sql .= ", contraseña = :contraseña";
        }
        
        $sql .= " WHERE id_usuario = :id";
        
        $params = [
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'correo' => $datos['correo'],
            'usuario' => $datos['usuario'],
            'id_rol' => $datos['id_rol'],
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'estado' => $datos['estado'],
            'id' => $id
        ];
        
        if (!empty($datos['contraseña'])) {
            $params['contraseña'] = password_hash($datos['contraseña'], PASSWORD_DEFAULT);
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Eliminar usuario
     */
    public function eliminar($id) {
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Cambiar estado de usuario
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE usuarios SET estado = :estado WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['estado' => $estado, 'id' => $id]);
    }
    
    /**
     * Verificar si usuario existe
     */
    public function existeUsuario($usuario, $excluirId = null) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE usuario = :usuario";
        $params = ['usuario' => $usuario];
        
        if ($excluirId) {
            $sql .= " AND id_usuario != :id";
            $params['id'] = $excluirId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Verificar si correo existe
     */
    public function existeCorreo($correo, $excluirId = null) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo";
        $params = ['correo' => $correo];
        
        if ($excluirId) {
            $sql .= " AND id_usuario != :id";
            $params['id'] = $excluirId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtener permisos de un rol
     */
    private function obtenerPermisos($idRol) {
        $sql = "SELECT p.nombre_permiso 
                FROM rol_permiso rp 
                INNER JOIN permisos p ON rp.id_permiso = p.id_permiso 
                WHERE rp.id_rol = :id_rol";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_rol' => $idRol]);
        
        $permisos = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $permisos ?: [];
    }
    
    /**
     * Incrementar intentos fallidos
     */
    private function incrementarIntentos($usuario) {
        $sql = "UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE usuario = :usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario' => $usuario]);
    }
    
    /**
     * Resetear intentos fallidos
     */
    private function resetearIntentos($idUsuario) {
        $sql = "UPDATE usuarios SET intentos_fallidos = 0 WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idUsuario]);
    }
    
    /**
     * Actualizar último acceso
     */
    private function actualizarUltimoAcceso($idUsuario) {
        $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $idUsuario]);
    }
    
    /**
     * Contar total de usuarios
     */
    public function contarTotal() {
        $sql = "SELECT COUNT(*) FROM usuarios";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    /**
     * Obtener usuarios activos
     */
    public function obtenerActivos() {
        $sql = "SELECT u.*, r.nombre_rol 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                WHERE u.estado = 'activo' 
                ORDER BY u.nombres";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
