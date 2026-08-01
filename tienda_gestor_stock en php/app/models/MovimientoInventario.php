<?php
/**
 * Modelo de Movimiento de Inventario
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../../config/database.php';

class MovimientoInventario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los movimientos
     */
    public function obtenerTodos($pagina = 1, $limite = ITEMS_PER_PAGE, $filtros = []) {
        $offset = ($pagina - 1) * $limite;
        
        $sql = "SELECT mi.*, p.nombre as producto_nombre, p.codigo, u.nombres, u.apellidos 
                FROM movimientos_inventario mi 
                INNER JOIN productos p ON mi.id_producto = p.id_producto 
                INNER JOIN usuarios u ON mi.id_usuario = u.id_usuario 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['tipo_movimiento'])) {
            $sql .= " AND mi.tipo_movimiento = :tipo_movimiento";
            $params['tipo_movimiento'] = $filtros['tipo_movimiento'];
        }
        
        if (!empty($filtros['producto'])) {
            $sql .= " AND mi.id_producto = :producto";
            $params['producto'] = $filtros['producto'];
        }
        
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(mi.fecha) >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(mi.fecha) <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        $sql .= " ORDER BY mi.fecha DESC LIMIT :limite OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener movimiento por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT mi.*, p.nombre as producto_nombre, p.codigo, u.nombres, u.apellidos 
                FROM movimientos_inventario mi 
                INNER JOIN productos p ON mi.id_producto = p.id_producto 
                INNER JOIN usuarios u ON mi.id_usuario = u.id_usuario 
                WHERE mi.id_movimiento = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Crear movimiento
     */
    public function crear($datos) {
        $sql = "INSERT INTO movimientos_inventario (id_producto, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, observacion, id_usuario, referencia) 
                VALUES (:id_producto, :tipo_movimiento, :cantidad, :stock_anterior, :stock_nuevo, :observacion, :id_usuario, :referencia)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_producto' => $datos['id_producto'],
            'tipo_movimiento' => $datos['tipo_movimiento'],
            'cantidad' => $datos['cantidad'],
            'stock_anterior' => $datos['stock_anterior'],
            'stock_nuevo' => $datos['stock_nuevo'],
            'observacion' => $datos['observacion'] ?? null,
            'id_usuario' => $datos['id_usuario'],
            'referencia' => $datos['referencia'] ?? null
        ]);
    }
    
    /**
     * Obtener movimientos por producto
     */
    public function obtenerPorProducto($idProducto, $limite = 50) {
        $sql = "SELECT mi.*, u.nombres, u.apellidos 
                FROM movimientos_inventario mi 
                INNER JOIN usuarios u ON mi.id_usuario = u.id_usuario 
                WHERE mi.id_producto = :id_producto 
                ORDER BY mi.fecha DESC 
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_producto', $idProducto);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener kardex de producto
     */
    public function obtenerKardex($idProducto) {
        $sql = "SELECT mi.*, 
                CASE WHEN mi.tipo_movimiento = 'entrada' THEN mi.cantidad ELSE 0 END as entradas,
                CASE WHEN mi.tipo_movimiento = 'salida' THEN mi.cantidad ELSE 0 END as salidas,
                mi.stock_nuevo as saldo
                FROM movimientos_inventario mi 
                WHERE mi.id_producto = :id_producto 
                ORDER BY mi.fecha ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_producto' => $idProducto]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Contar total de movimientos
     */
    public function contarTotal($filtros = []) {
        $sql = "SELECT COUNT(*) FROM movimientos_inventario WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['tipo_movimiento'])) {
            $sql .= " AND tipo_movimiento = :tipo_movimiento";
            $params['tipo_movimiento'] = $filtros['tipo_movimiento'];
        }
        
        if (!empty($filtros['producto'])) {
            $sql .= " AND id_producto = :producto";
            $params['producto'] = $filtros['producto'];
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
}
