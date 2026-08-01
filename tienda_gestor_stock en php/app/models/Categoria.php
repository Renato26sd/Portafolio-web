<?php

require_once __DIR__ . '/../../config/database.php';

class Categoria {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /*** Obtener todas las categorías */
    public function obtenerTodos() {
        $sql = "SELECT * FROM categorias WHERE estado = 'activo' ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener categoría por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM categorias WHERE id_categoria = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Crear categoría
     */
    public function crear($datos) {
        $sql = "INSERT INTO categorias (nombre, descripcion, estado) 
                VALUES (:nombre, :descripcion, :estado)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'] ?? 'activo'
        ]);
    }
    
    /**
     * Actualizar categoría
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE categorias SET 
                nombre = :nombre,
                descripcion = :descripcion,
                estado = :estado
                WHERE id_categoria = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'],
            'id' => $id
        ]);
    }
    
    /**
     * Eliminar categoría
     */
    public function eliminar($id) {
        $sql = "DELETE FROM categorias WHERE id_categoria = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Verificar si nombre existe
     */
    public function existeNombre($nombre, $excluirId = null) {
        $sql = "SELECT COUNT(*) FROM categorias WHERE nombre = :nombre";
        $params = ['nombre' => $nombre];
        
        if ($excluirId) {
            $sql .= " AND id_categoria != :id";
            $params['id'] = $excluirId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar productos por categoría
     */
    public function contarProductos($id) {
        $sql = "SELECT COUNT(*) FROM productos WHERE id_categoria = :id AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetchColumn();
    }
}
