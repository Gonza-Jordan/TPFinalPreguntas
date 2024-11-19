<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';
require_once __DIR__ . '/../helper/MustachePresenter.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFHelper {
    private static $templateEngine;
    private static $templatePath;
    private static $defaultOptions = [
        'format' => 'A4',
        'orientation' => 'portrait',
        'margin' => [
            'top' => '10mm',
            'right' => '10mm',
            'bottom' => '10mm',
            'left' => '10mm'
        ]
    ];

    /**
     * Inicializa el helper con la ruta de las plantillas.
     * Este método debe ser llamado antes de usar los métodos estáticos.
     *
     * @param string $templatePath Ruta donde están las plantillas HTML
     */
    public static function init($templatePath) {
        self::$templatePath = $templatePath;
        // Inicializamos nuestro motor de plantillas personalizado
        self::$templateEngine = new TemplateEngine();
    }

    /**
     * Genera un PDF a partir de una plantilla y datos
     * 
     * @param string $template Nombre de la plantilla
     * @param array $data Datos para la plantilla
     * @param array $options Opciones de configuración del PDF
     * @return string Ruta del archivo PDF generado
     */
    public static function generate($template, $data = [], $options = []) {
        // Combinar opciones predeterminadas con las proporcionadas
        $options = array_merge(self::$defaultOptions, $options);

        // Ruta completa a la plantilla
        $templateFile = self::$templatePath . '/' . $template . '.html';

        try {
            // Usar nuestro motor de plantillas personalizado
            $htmlContent = self::$templateEngine->render($templateFile, $data);

            // Configurar Dompdf
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->setPaper($options['format'], $options['orientation']);
            $dompdf->loadHtml($htmlContent);

            // Renderizar PDF
            $dompdf->render();

            // Generar nombre único para el archivo
            $outputPath = sys_get_temp_dir() . '/' . uniqid('pdf_') . '.pdf';

            // Guardar el PDF
            file_put_contents($outputPath, $dompdf->output());

            return $outputPath;

        } catch (\Exception $e) {
            throw new \Exception("Error generando PDF: " . $e->getMessage());
        }
    }

    /**
     * Descarga directamente el PDF generado
     * 
     * @param string $template Nombre de la plantilla
     * @param array $data Datos para la plantilla
     * @param string $filename Nombre del archivo para la descarga
     * @param array $options Opciones de configuración del PDF
     */
    public static function download($template, $data = [], $filename = 'document.pdf', $options = []) {
        $pdfPath = self::generate($template, $data, $options);

        if (file_exists($pdfPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($pdfPath);
            unlink($pdfPath); // Eliminar archivo temporal
            exit;
        }

        throw new \Exception("No se pudo generar el archivo PDF");
    }

    /**
     * Muestra el PDF en el navegador
     * 
     * @param string $template Nombre de la plantilla
     * @param array $data Datos para la plantilla
     * @param array $options Opciones de configuración del PDF
     */
    public static function stream($template, $data = [], $options = []) {
        $pdfPath = self::generate($template, $data, $options);

        if (file_exists($pdfPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($pdfPath) . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($pdfPath);
            unlink($pdfPath); // Eliminar archivo temporal
            exit;
        }

        throw new \Exception("No se pudo generar el archivo PDF");
    }
}
