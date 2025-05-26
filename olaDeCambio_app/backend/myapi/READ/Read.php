<?php
namespace App\READ;

use App\DataBase;
use PDO;

class Read {
    private $conn;

    public function __construct() {
        $db = new DataBase();
        $this->conn = $db->getConexion();
    }

    public function obtenerReportes() {
        $stmt = $this->conn->query("SELECT * FROM reportes ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerResumenDashboard() {
        $response = [
            'total' => 0,
            'tipo_mas_comun' => null,
            'estado_top' => null,
            'por_tipo' => ['labels' => [], 'valores' => []],
            'por_estado' => ['labels' => [], 'valores' => []]
        ];

        // === Por tipo de reporte ===
        $stmt = $this->conn->query("
            SELECT tipo_reporte, COUNT(*) as total
            FROM reportes
            GROUP BY tipo_reporte
        ");
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        $mayorTipo = null;
        $maxTipoCantidad = 0;

        foreach ($tipos as $fila) {
            $tipo = $fila['tipo_reporte'];
            $cantidad = (int)$fila['total'];

            $response['por_tipo']['labels'][] = $tipo;
            $response['por_tipo']['valores'][] = $cantidad;
            $total += $cantidad;

            if ($cantidad > $maxTipoCantidad) {
                $maxTipoCantidad = $cantidad;
                $mayorTipo = $tipo;
            }
        }

        $response['total'] = $total;
        $response['tipo_mas_comun'] = $mayorTipo;

        // === Por estado ===
        $stmt2 = $this->conn->query("
            SELECT ubicacion, COUNT(*) as total
            FROM reportes
            GROUP BY ubicacion
        ");
        $estados = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $estadoMasReportes = null;
        $maxEstadoCantidad = 0;

        foreach ($estados as $fila) {
            $estado = $fila['ubicacion'];
            $cantidad = (int)$fila['total'];

            $response['por_estado']['labels'][] = $estado;
            $response['por_estado']['valores'][] = $cantidad;

            if ($cantidad > $maxEstadoCantidad) {
                $maxEstadoCantidad = $cantidad;
                $estadoMasReportes = $estado;
            }
        }

        $response['estado_top'] = $estadoMasReportes;

        return $response;
    }

    public function obtenerReportesPorMes() {
        $stmt = $this->conn->query("
            SELECT DATE_FORMAT(fecha_incidente, '%Y-%m') AS mes, COUNT(*) AS cantidad
            FROM reportes
            GROUP BY mes
            ORDER BY mes
        ");

        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $valores = [];

        foreach ($datos as $fila) {
            $labels[] = $fila['mes'];
            $valores[] = (int)$fila['cantidad'];
        }

        return [
            'labels' => $labels,
            'valores' => $valores
        ];
    }
}
