<?php
/**
 * Modelo de Producto
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../../config/database.php';

class Producto {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los productos
     */
    public function obtenerTodos($pagina = 1, $limite = ITEMS_PER_PAGE, $filtros = []) {
        $offset = ($pagina - 1) * $limite;
        
        $sql = "SELECT p.*, c.nombre as categoria_nombre, pr.razon_social as proveedor_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (p.nombre LIKE :busqueda OR p.codigo LIKE :busqueda)";
            $params['busqueda'] = "%{$filtros['busqueda']}%";
        }
        
        if (!empty($filtros['categoria'])) {
            $sql .= " AND p.id_categoria = :categoria";
            $params['categoria'] = $filtros['categoria'];
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }
        
        if (!empty($filtros['stock_bajo'])) {
            $sql .= " AND p.stock <= p.stock_minimo";
        }
        
        $sql .= " ORDER BY p.nombre ASC LIMIT :limite OFFSET :offset";
        
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
     * Obtener producto por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre, pr.razon_social as proveedor_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor 
                WHERE p.id_producto = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener producto por código
     */
    public function obtenerPorCodigo($codigo) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE p.codigo = :codigo AND p.estado = 'activo'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['codigo' => $codigo]);
        
        return $stmt->fetch();
    }
    
    /**
     * Buscar productos para venta
     */
    public function buscarParaVenta($termino) {
        $sql = "SELECT p.id_producto, p.codigo, p.nombre, p.precio_venta, p.stock, p.imagen 
                FROM productos p 
                WHERE p.estado = 'activo' 
                AND p.stock > 0 
                AND (p.nombre LIKE :termino1 OR p.codigo LIKE :termino2 OR p.codigo_barras LIKE :termino3)
                ORDER BY p.nombre ASC
                LIMIT 20";
        
        $stmt = $this->db->prepare($sql);
        $terminoLike = "%{$termino}%";
        $stmt->execute([
            'termino1' => $terminoLike,
            'termino2' => $terminoLike,
            'termino3' => $terminoLike
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Crear producto
     */
    public function crear($datos) {
        $sql = "INSERT INTO productos (codigo, codigo_barras, nombre, descripcion, precio_compra, precio_venta, stock, stock_minimo, imagen, id_categoria, id_proveedor, estado) 
                VALUES (:codigo, :codigo_barras, :nombre, :descripcion, :precio_compra, :precio_venta, :stock, :stock_minimo, :imagen, :id_categoria, :id_proveedor, :estado)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'codigo' => $datos['codigo'],
            'codigo_barras' => $datos['codigo_barras'] ?? null,
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'precio_compra' => $datos['precio_compra'],
            'precio_venta' => $datos['precio_venta'],
            'stock' => $datos['stock'] ?? 0,
            'stock_minimo' => $datos['stock_minimo'] ?? 5,
            'imagen' => $datos['imagen'] ?? null,
            'id_categoria' => $datos['id_categoria'],
            'id_proveedor' => $datos['id_proveedor'] ?? null,
            'estado' => $datos['estado'] ?? 'activo'
        ]);
    }
    
    /**
     * Actualizar producto
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE productos SET 
                codigo = :codigo,
                codigo_barras = :codigo_barras,
                nombre = :nombre,
                descripcion = :descripcion,
                precio_compra = :precio_compra,
                precio_venta = :precio_venta,
                stock = :stock,
                stock_minimo = :stock_minimo,
                id_categoria = :id_categoria,
                id_proveedor = :id_proveedor,
                estado = :estado";
        
        if (!empty($datos['imagen'])) {
            $sql .= ", imagen = :imagen";
        }
        
        $sql .= " WHERE id_producto = :id";
        
        $params = [
            'codigo' => $datos['codigo'],
            'codigo_barras' => $datos['codigo_barras'] ?? null,
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'precio_compra' => $datos['precio_compra'],
            'precio_venta' => $datos['precio_venta'],
            'stock' => $datos['stock'],
            'stock_minimo' => $datos['stock_minimo'],
            'id_categoria' => $datos['id_categoria'],
            'id_proveedor' => $datos['id_proveedor'] ?? null,
            'estado' => $datos['estado'],
            'id' => $id
        ];
        
        if (!empty($datos['imagen'])) {
            $params['imagen'] = $datos['imagen'];
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Eliminar producto
     */
    public function eliminar($id) {
        $sql = "DELETE FROM productos WHERE id_producto = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Actualizar stock
     */
    public function actualizarStock($id, $cantidad, $tipo) {
        if ($tipo === 'entrada') {
            $sql = "UPDATE productos SET stock = stock + :cantidad WHERE id_producto = :id";
        } else {
            $sql = "UPDATE productos SET stock = stock - :cantidad WHERE id_producto = :id AND stock >= :cantidad";
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['cantidad' => $cantidad, 'id' => $id]);
    }
    
    /**
     * Obtener productos con bajo stock
     */
    public function obtenerBajoStock() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE p.stock <= p.stock_minimo AND p.estado = 'activo'
                ORDER BY p.stock ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener productos sin stock
     */
    public function obtenerSinStock() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                WHERE p.stock = 0 AND p.estado = 'activo'
                ORDER BY p.nombre ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Verificar si código existe
     */
    public function existeCodigo($codigo, $excluirId = null) {
        $sql = "SELECT COUNT(*) FROM productos WHERE codigo = :codigo";
        $params = ['codigo' => $codigo];
        
        if ($excluirId) {
            $sql .= " AND id_producto != :id";
            $params['id'] = $excluirId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar total de productos
     */
    public function contarTotal($filtros = []) {
        $sql = "SELECT COUNT(*) FROM productos WHERE 1=1";
        $params = [];
        
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (nombre LIKE :busqueda OR codigo LIKE :busqueda)";
            $params['busqueda'] = "%{$filtros['busqueda']}%";
        }
        
        if (!empty($filtros['categoria'])) {
            $sql .= " AND id_categoria = :categoria";
            $params['categoria'] = $filtros['categoria'];
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
    
    /**
     * Obtener productos más vendidos
     */
    public function obtenerMasVendidos($limite = 10) {
        $sql = "SELECT p.id_producto, p.codigo, p.nombre, p.imagen,
                SUM(dv.cantidad) as total_vendido,
                SUM(dv.subtotal) as total_ingresos
                FROM productos p
                INNER JOIN detalle_ventas dv ON p.id_producto = dv.id_producto
                INNER JOIN ventas v ON dv.id_venta = v.id_venta
                WHERE v.estado = 'completada'
                AND v.fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY p.id_producto, p.codigo, p.nombre, p.imagen
                ORDER BY total_vendido DESC
                LIMIT :limite";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
