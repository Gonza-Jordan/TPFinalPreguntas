<?php
require_once __DIR__ . '/../helper/PDFlHelper.php';

class ReporteController {
    private $pdfHelper;
    
    public function __construct() {
        $this->pdfHelper = new PDFHelper();
    }
    
    public function generateReport() {
        // Ejemplo de datos para el reporte
        $data = [
            'title' => 'Reporte Mensual',
            'tables' => [
                'ventas' => [
                    ['producto' => 'A', 'cantidad' => 100],
                    ['producto' => 'B', 'cantidad' => 150]
                ]
            ]
        ];
        
        $template = __DIR__ . '/../Views/reports/template.php';
        $this->pdfHelper->generatePDF($template, $data, 'reporte_mensual.pdf');
    }
    
    public function generatePieChart() {
        $chartData = [
            'Ventas' => 30,
            'Marketing' => 25,
            'Desarrollo' => 45
        ];
        
        $this->pdfHelper->generateChartPDF($chartData, 'Distribución de Presupuesto');
    }
}
