<?php
/**
 * Modelo de Cliente
 * Sistema Web de Ventas e Inventario - Bodega Armando Tornero
 */

require_once __DIR__ . '/../../config/database.php';

class Cliente {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtener todos los clientes
     */
    public function obtenerTodos($pagina = 1, $limite = ITEMS_PER_PAGE) {
        $offset = ($pagina - 1) * $limite;
        
        $sql = "SELECT * FROM clientes WHERE estado = 'activo' ORDER BY nombres ASC LIMIT :limite OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener cliente por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM clientes WHERE id_cliente = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->fetch();
    }
    
    /**
     * Buscar cliente por DNI
     */
    public function buscarPorDni($dni) {
        $sql = "SELECT * FROM clientes WHERE dni = :dni AND estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['dni' => $dni]);
        
        return $stmt->fetch();
    }
    
    /**
     * Buscar clientes
     */
    public function buscar($termino) {
        $sql = "SELECT * FROM clientes 
                WHERE estado = 'activo' 
                AND (nombres LIKE :termino OR apellidos LIKE :termino OR dni LIKE :termino)
                ORDER BY nombres ASC
                LIMIT 20";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['termino' => "%{$termino}%"]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Crear cliente
     */
    public function crear($datos) {
        $sql = "INSERT INTO clientes (dni, nombres, apellidos, telefono, direccion, correo, estado) 
                VALUES (:dni, :nombres, :apellidos, :telefono, :direccion, :correo, :estado)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'dni' => $datos['dni'] ?? null,
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'correo' => $datos['correo'] ?? null,
            'estado' => $datos['estado'] ?? 'activo'
        ]);
    }
    
    /**
     * Actualizar cliente
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE clientes SET 
                dni = :dni,
                nombres = :nombres,
                apellidos = :apellidos,
                telefono = :telefono,
                direccion = :direccion,
                correo = :correo,
                estado = :estado
                WHERE id_cliente = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'dni' => $datos['dni'] ?? null,
            'nombres' => $datos['nombres'],
            'apellidos' => $datos['apellidos'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'correo' => $datos['correo'] ?? null,
            'estado' => $datos['estado'],
            'id' => $id
        ]);
    }
    
    /**
     * Eliminar cliente
     */
    public function eliminar($id) {
        $sql = "DELETE FROM clientes WHERE id_cliente = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Verificar si DNI existe
     */
    public function existeDni($dni, $excluirId = null) {
        $sql = "SELECT COUNT(*) FROM clientes WHERE dni = :dni";
        $params = ['dni' => $dni];
        
        if ($excluirId) {
            $sql .= " AND id_cliente != :id";
            $params['id'] = $excluirId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar total de clientes
     */
    public function contarTotal() {
        $sql = "SELECT COUNT(*) FROM clientes WHERE estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
}
