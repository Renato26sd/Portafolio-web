<?php
/**
 * Modelo de Reporte
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../../config/database.php';

class Reporte {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Reporte de ventas por rango de fechas
     */
    public function ventasPorFecha($fechaInicio, $fechaFin) {
        $sql = "SELECT 
                DATE(v.fecha) as fecha,
                COUNT(*) as total_ventas,
                SUM(v.total) as total_monto,
                SUM(v.subtotal) as total_subtotal,
                SUM(v.igv) as total_igv
                FROM ventas v
                WHERE v.estado = 'completada'
                AND DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY DATE(v.fecha)
                ORDER BY fecha DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Reporte de productos más vendidos
     */
    public function productosMasVendidos($fechaInicio = null, $fechaFin = null, $limite = 10) {
        $sql = "SELECT 
                p.id_producto,
                p.codigo,
                p.nombre,
                SUM(dv.cantidad) as total_vendido,
                SUM(dv.subtotal) as total_ingresos
                FROM productos p
                INNER JOIN detalle_ventas dv ON p.id_producto = dv.id_producto
                INNER JOIN ventas v ON dv.id_venta = v.id_venta
                WHERE v.estado = 'completada'";
        
        $params = [];
        
        if ($fechaInicio && $fechaFin) {
            $sql .= " AND DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin";
            $params['fecha_inicio'] = $fechaInicio;
            $params['fecha_fin'] = $fechaFin;
        }
        
        $sql .= " GROUP BY p.id_producto, p.codigo, p.nombre
                ORDER BY total_vendido DESC
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Reporte de productos sin stock
     */
    public function productosSinStock() {
        $sql = "SELECT 
                p.id_producto,
                p.codigo,
                p.nombre,
                p.stock,
                p.stock_minimo,
                c.nombre as categoria
                FROM productos p
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.stock = 0 AND p.estado = 'activo'
                ORDER BY p.nombre";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Reporte de productos con bajo stock
     */
    public function productosBajoStock() {
        $sql = "SELECT 
                p.id_producto,
                p.codigo,
                p.nombre,
                p.stock,
                p.stock_minimo,
                c.nombre as categoria
                FROM productos p
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.stock <= p.stock_minimo AND p.estado = 'activo'
                ORDER BY p.stock ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Reporte de ganancias
     */
    public function ganancias($fechaInicio, $fechaFin) {
        $sql = "SELECT 
                SUM(dv.subtotal) as total_ventas,
                SUM(dv.cantidad * (dv.precio_unitario - p.precio_compra)) as total_ganancia
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.id_venta = v.id_venta
                INNER JOIN productos p ON dv.id_producto = p.id_producto
                WHERE v.estado = 'completada'
                AND DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
        
        return $stmt->fetch();
    }
    
    /**
     * Reporte de ventas por método de pago
     */
    public function ventasPorMetodoPago($fechaInicio, $fechaFin) {
        $sql = "SELECT 
                metodo_pago,
                COUNT(*) as cantidad_ventas,
                SUM(total) as total_monto
                FROM ventas
                WHERE estado = 'completada'
                AND DATE(fecha) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY metodo_pago
                ORDER BY total_monto DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Reporte de ventas por vendedor
     */
    public function ventasPorVendedor($fechaInicio, $fechaFin) {
        $sql = "SELECT 
                u.id_usuario,
                u.nombres,
                u.apellidos,
                COUNT(v.id_venta) as cantidad_ventas,
                SUM(v.total) as total_monto
                FROM ventas v
                INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.estado = 'completada'
                AND DATE(v.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY u.id_usuario, u.nombres, u.apellidos
                ORDER BY total_monto DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Reporte de movimientos de inventario
     */
    public function movimientosInventario($fechaInicio, $fechaFin) {
        $sql = "SELECT 
                mi.tipo_movimiento,
                COUNT(*) as cantidad_movimientos,
                SUM(mi.cantidad) as total_cantidad
                FROM movimientos_inventario mi
                WHERE DATE(mi.fecha) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY mi.tipo_movimiento";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Resumen del dashboard
     */
    public function resumenDashboard() {
        $hoy = date('Y-m-d');
        $mesActual = date('Y-m');
        
        $sql = "SELECT 
                (SELECT COUNT(*) FROM usuarios WHERE estado = 'activo') as total_usuarios,
                (SELECT COUNT(*) FROM productos WHERE estado = 'activo') as total_productos,
                (SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo AND estado = 'activo') as productos_bajo_stock,
                (SELECT COUNT(*) FROM productos WHERE stock = 0 AND estado = 'activo') as productos_sin_stock,
                (SELECT COUNT(*) FROM ventas WHERE DATE(fecha) = :hoy1 AND estado = 'completada') as ventas_hoy,
                (SELECT COALESCE(SUM(total), 0) FROM ventas WHERE DATE(fecha) = :hoy2 AND estado = 'completada') as total_ventas_hoy,
                (SELECT COUNT(*) FROM ventas WHERE DATE_FORMAT(fecha, '%Y-%m') = :mes1 AND estado = 'completada') as ventas_mes,
                (SELECT COALESCE(SUM(total), 0) FROM ventas WHERE DATE_FORMAT(fecha, '%Y-%m') = :mes2 AND estado = 'completada') as total_ventas_mes";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'hoy1' => $hoy,
            'hoy2' => $hoy,
            'mes1' => $mesActual,
            'mes2' => $mesActual
        ]);
        
        return $stmt->fetch();
    }
    
    /**
     * Últimas ventas para dashboard
     */
    public function ultimasVentas($limite = 5) {
        $sql = "SELECT v.*, u.nombres, u.apellidos 
                FROM ventas v 
                INNER JOIN usuarios u ON v.id_usuario = u.id_usuario 
                WHERE v.estado = 'completada' 
                ORDER BY v.fecha DESC 
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
