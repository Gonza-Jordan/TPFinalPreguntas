<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../helper/TemplateEngine.php';
require_once __DIR__ . '/../config/MustachePresenter.php';

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
    public static function init($templatePath) {
        self::$templatePath = $templatePath;
        self::$templateEngine = new TemplateEngine();
    }

    public static function generate($template, $data = [], $options = []) {
        $options = array_merge(self::$defaultOptions, $options);

        $templateFile = self::$templatePath . '/' . $template . '.mustache';

        if (!file_exists($templateFile)) {
            error_log("Plantilla no encontrada: " . $templateFile);
            throw new \Exception("Plantilla no encontrada: " . $templateFile);
        }

        try {
            $htmlContent = self::$templateEngine->render($templateFile, $data);

            $dompdf = new \Dompdf\Dompdf();
            $dompdf->setPaper($options['format'], $options['orientation']);
            $dompdf->loadHtml($htmlContent);

            $dompdf->render();

            $outputPath = sys_get_temp_dir() . '/' . uniqid('pdf_') . '.pdf';
            file_put_contents($outputPath, $dompdf->output());

            return $outputPath;

        } catch (\Exception $e) {
            throw new \Exception("Error generando PDF: " . $e->getMessage());
        }
    }

    public static function download($template, $data = [], $filename = 'document.pdf', $options = []) {
        $pdfPath = self::generate($template, $data, $options);

        if (file_exists($pdfPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($pdfPath);
            unlink($pdfPath);
            exit;
        }

        throw new \Exception("No se pudo generar el archivo PDF");
    }

    public static function stream($template, $data = [], $options = []) {
        $pdfPath = self::generate($template, $data, $options);

        if (file_exists($pdfPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($pdfPath) . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($pdfPath);
            unlink($pdfPath);
            exit;
        }
        throw new \Exception("No se pudo generar el archivo PDF");
    }
}
