<?php
require_once __DIR__ . '/../DataBase.php';

class Read {
    private $conn;

    public function __construct() {
        $db = new DataBase();
        $this->conn = $db->getConexion();
    }

    public function obtenerReportes() {
        try {
            $stmt = $this->conn->query("SELECT * FROM reportes ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerResumenDashboard()
{
    $queryTotal = "SELECT COUNT(*) AS total FROM reportes";
    $queryTipo = "SELECT tipo_reporte, COUNT(*) as c FROM reportes GROUP BY tipo_reporte ORDER BY c DESC";
    $queryEstado = "SELECT ubicacion, COUNT(*) as c FROM reportes GROUP BY ubicacion ORDER BY c DESC";

    $stmtTotal = $this->conn->prepare($queryTotal);
    $stmtTotal->execute();
    $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

    $stmtTipo = $this->conn->prepare($queryTipo);
    $stmtTipo->execute();
    $tipos = $stmtTipo->fetchAll(PDO::FETCH_ASSOC);

    $stmtEstado = $this->conn->prepare($queryEstado);
    $stmtEstado->execute();
    $estados = $stmtEstado->fetchAll(PDO::FETCH_ASSOC);

    return [
        'total' => $total,
        'tipo_mas_comun' => $tipos[0]['tipo_reporte'] ?? 'N/A',
        'estado_top' => $estados[0]['ubicacion'] ?? 'N/A',
        'por_tipo' => [
            'labels' => array_column($tipos, 'tipo_reporte'),
            'valores' => array_column($tipos, 'c')
        ],
        'por_estado' => [
            'labels' => array_column($estados, 'ubicacion'),
            'valores' => array_column($estados, 'c')
        ]
    ];
}

public function obtenerReportesPorMes()
{
    $query = "
        SELECT 
            DATE_FORMAT(fecha_incidente, '%Y-%m') AS mes,
            COUNT(*) AS total
        FROM reportes
        GROUP BY mes
        ORDER BY mes ASC
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'labels' => array_column($resultados, 'mes'),
        'valores' => array_column($resultados, 'total')
    ];
}


}
