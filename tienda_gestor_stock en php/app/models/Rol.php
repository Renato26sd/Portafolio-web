<?php
/**
 * Modelo de Rol
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../../config/database.php';

class Rol {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los roles
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM roles WHERE estado = 'activo' ORDER BY nombre_rol";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener rol por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM roles WHERE id_rol = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Crear rol
     */
    public function crear($datos) {
        $sql = "INSERT INTO roles (nombre_rol, descripcion, estado) 
                VALUES (:nombre_rol, :descripcion, :estado)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nombre_rol' => $datos['nombre_rol'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'] ?? 'activo'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Actualizar rol
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE roles SET 
                nombre_rol = :nombre_rol,
                descripcion = :descripcion,
                estado = :estado
                WHERE id_rol = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nombre_rol' => $datos['nombre_rol'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'],
            'id' => $id
        ]);
    }
    
    /**
     * Eliminar rol
     */
    public function eliminar($id) {
        $sql = "DELETE FROM roles WHERE id_rol = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Obtener permisos de un rol
     */
    public function obtenerPermisos($idRol) {
        $sql = "SELECT p.* 
                FROM permisos p 
                INNER JOIN rol_permiso rp ON p.id_permiso = rp.id_permiso 
                WHERE rp.id_rol = :id_rol
                ORDER BY p.nombre_permiso";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_rol' => $idRol]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Asignar permisos a un rol
     */
    public function asignarPermisos($idRol, $permisos) {
        // Eliminar permisos actuales
        $sql = "DELETE FROM rol_permiso WHERE id_rol = :id_rol";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_rol' => $idRol]);
        
        // Asignar nuevos permisos
        if (!empty($permisos)) {
            $sql = "INSERT INTO rol_permiso (id_rol, id_permiso) VALUES (:id_rol, :id_permiso)";
            $stmt = $this->db->prepare($sql);
            
            foreach ($permisos as $idPermiso) {
                $stmt->execute([
                    'id_rol' => $idRol,
                    'id_permiso' => $idPermiso
                ]);
            }
        }
        
        return true;
    }
    
    /**
     * Obtener todos los permisos disponibles
     */
    public function obtenerTodosPermisos() {
        $sql = "SELECT * FROM permisos ORDER BY nombre_permiso";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
