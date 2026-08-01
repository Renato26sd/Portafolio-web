<?php
/**
 * Modelo de Venta
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../../config/database.php';

class Venta {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todas las ventas
     */
    public function obtenerTodos($pagina = 1, $limite = ITEMS_PER_PAGE, $filtros = []) {
        $offset = ($pagina - 1) * $limite;
        
        $sql = "SELECT v.*, u.nombres, u.apellidos, c.nombres as cliente_nombres, c.apellidos as cliente_apellidos 
                FROM ventas v 
                INNER JOIN usuarios u ON v.id_usuario = u.id_usuario 
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(v.fecha) >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(v.fecha) <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND v.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['metodo_pago'])) {
            $sql .= " AND v.metodo_pago = :metodo_pago";
            $params['metodo_pago'] = $filtros['metodo_pago'];
        }
        
        $sql .= " ORDER BY v.fecha DESC LIMIT :limite OFFSET :offset";
        
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
     * Obtener venta por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT v.*, u.nombres, u.apellidos, c.nombres as cliente_nombres, c.apellidos as cliente_apellidos 
                FROM ventas v 
                INNER JOIN usuarios u ON v.id_usuario = u.id_usuario 
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente 
                WHERE v.id_venta = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener detalles de una venta
     */
    public function obtenerDetalles($idVenta) {
        $sql = "SELECT dv.*, p.nombre as producto_nombre, p.codigo 
                FROM detalle_ventas dv 
                INNER JOIN productos p ON dv.id_producto = p.id_producto 
                WHERE dv.id_venta = :id_venta";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_venta' => $idVenta]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Crear venta
     */
    public function crear($datos) {
        try {
            $this->db->beginTransaction();
            
            // Insertar venta
            $sql = "INSERT INTO ventas (id_usuario, id_cliente, fecha, subtotal, igv, total, descuento, metodo_pago, estado, observaciones) 
                    VALUES (:id_usuario, :id_cliente, :fecha, :subtotal, :igv, :total, :descuento, :metodo_pago, :estado, :observaciones)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'id_usuario' => $datos['id_usuario'],
                'id_cliente' => $datos['id_cliente'] ?? null,
                'fecha' => $datos['fecha'] ?? date('Y-m-d H:i:s'),
                'subtotal' => $datos['subtotal'],
                'igv' => $datos['igv'],
                'total' => $datos['total'],
                'descuento' => $datos['descuento'] ?? 0,
                'metodo_pago' => $datos['metodo_pago'],
                'estado' => $datos['estado'] ?? 'completada',
                'observaciones' => $datos['observaciones'] ?? null
            ]);
            
            $idVenta = $this->db->lastInsertId();
            
            // Insertar detalles
            $sqlDetalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) 
                           VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :subtotal)";
            
            $stmtDetalle = $this->db->prepare($sqlDetalle);
            
            foreach ($datos['detalles'] as $detalle) {
                $stmtDetalle->execute([
                    'id_venta' => $idVenta,
                    'id_producto' => $detalle['id_producto'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $detalle['subtotal']
                ]);
            }
            
            $this->db->commit();
            
            return $idVenta;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al crear venta: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Anular venta
     */
    public function anular($id) {
        $sql = "UPDATE ventas SET estado = 'anulada' WHERE id_venta = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Obtener ventas del día
     */
    public function obtenerVentasDia($fecha = null) {
        $fecha = $fecha ?? date('Y-m-d');
        
        $sql = "SELECT * FROM ventas 
                WHERE DATE(fecha) = :fecha 
                AND estado = 'completada' 
                ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['fecha' => $fecha]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener total ventas del día
     */
    public function obtenerTotalDia($fecha = null) {
        $fecha = $fecha ?? date('Y-m-d');
        
        $sql = "SELECT SUM(total) as total, COUNT(*) as cantidad 
                FROM ventas 
                WHERE DATE(fecha) = :fecha 
                AND estado = 'completada'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['fecha' => $fecha]);
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener ventas por rango de fechas
     */
    public function obtenerPorRangoFechas($fechaInicio, $fechaFin) {
        $sql = "SELECT * FROM ventas 
                WHERE DATE(fecha) BETWEEN :fecha_inicio AND :fecha_fin 
                AND estado = 'completada' 
                ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Contar total de ventas
     */
    public function contarTotal($filtros = []) {
        $sql = "SELECT COUNT(*) FROM ventas WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(fecha) >= :fecha_inicio";
            $params['fecha_inicio'] = $filtros['fecha_inicio'];
        }
        
        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(fecha) <= :fecha_fin";
            $params['fecha_fin'] = $filtros['fecha_fin'];
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = :estado";
            $params['estado'] = $filtros['estado'];
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
}
