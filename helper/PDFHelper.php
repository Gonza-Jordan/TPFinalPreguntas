<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFHelper {
    private $dompdf;
    private $options;
    
    public function __construct() {
        // Configuración inicial de DOMPDF
        $this->options = new Options();
        $this->options->set('isHtml5ParserEnabled', true);
        $this->options->set('isPhpEnabled', true);
        $this->options->set('isRemoteEnabled', true);
        
        $this->dompdf = new Dompdf($this->options);
    }
    
    /**
     * Genera un PDF a partir de datos y una plantilla
     * @param string $template Ruta de la plantilla
     * @param array $data Datos para la plantilla
     * @param string $filename Nombre del archivo a generar
     * @return void
     */
    public function generatePDF($template, $data, $filename = 'report.pdf') {
        // Obtener el contenido de la plantilla
        ob_start();
        extract($data);
        include $template;
        $html = ob_get_clean();
        
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        // Salida del PDF
        $this->dompdf->stream($filename, array("Attachment" => true));
    }
    
    /**
     * Genera un PDF con gráfico de torta
     * @param array $chartData Datos para el gráfico
     * @param string $title Título del gráfico
     * @return void
     */
    public function generateChartPDF($chartData, $title) {
        // Convertir datos del gráfico a HTML/CSS
        $html = '<html><head><style>';
        $html .= '.chart-container { width: 500px; margin: 20px auto; }';
        $html .= '</style></head><body>';
        $html .= "<h1>$title</h1>";
        $html .= '<div class="chart-container">';
        // Aquí irían los datos del gráfico convertidos a HTML/CSS
        $html .= '</div></body></html>';
        
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("chart.pdf", array("Attachment" => true));
    }
}
