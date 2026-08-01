<?php
/**
 * Modelo de Proveedor
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../../config/database.php';

class Proveedor {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los proveedores
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM proveedores WHERE estado = 'activo' ORDER BY razon_social";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener proveedor por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM proveedores WHERE id_proveedor = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Crear proveedor
     */
    public function crear($datos) {
        $sql = "INSERT INTO proveedores (ruc, razon_social, nombre_contacto, telefono, direccion, correo, estado) 
                VALUES (:ruc, :razon_social, :nombre_contacto, :telefono, :direccion, :correo, :estado)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'ruc' => $datos['ruc'],
            'razon_social' => $datos['razon_social'],
            'nombre_contacto' => $datos['nombre_contacto'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'correo' => $datos['correo'] ?? null,
            'estado' => $datos['estado'] ?? 'activo'
        ]);
    }
    
    /**
     * Actualizar proveedor
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE proveedores SET 
                ruc = :ruc,
                razon_social = :razon_social,
                nombre_contacto = :nombre_contacto,
                telefono = :telefono,
                direccion = :direccion,
                correo = :correo,
                estado = :estado
                WHERE id_proveedor = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'ruc' => $datos['ruc'],
            'razon_social' => $datos['razon_social'],
            'nombre_contacto' => $datos['nombre_contacto'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'correo' => $datos['correo'] ?? null,
            'estado' => $datos['estado'],
            'id' => $id
        ]);
    }
    
    /**
     * Eliminar proveedor
     */
    public function eliminar($id) {
        $sql = "DELETE FROM proveedores WHERE id_proveedor = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Verificar si RUC existe
     */
    public function existeRuc($ruc, $excluirId = null) {
        $sql = "SELECT COUNT(*) FROM proveedores WHERE ruc = :ruc";
        $params = ['ruc' => $ruc];
        
        if ($excluirId) {
            $sql .= " AND id_proveedor != :id";
            $params['id'] = $excluirId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar productos por proveedor
     */
    public function contarProductos($id) {
        $sql = "SELECT COUNT(*) FROM productos WHERE id_proveedor = :id AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetchColumn();
    }
}
