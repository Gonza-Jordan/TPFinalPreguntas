<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRCodeHelper {
    public static function generateQRCode(string $url): string {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 5,
        ]);

        $qrcode = new QRCode($options);
        $outputDir = __DIR__ . '/../../public/qrcodes';
        $outputPath = $outputDir . '/qr-code.png';

        // Verifica si la carpeta existe, si no, la crea
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        // Genera el código QR
        $qrcode->render($url, $outputPath);

        return '/TPFinalPreguntas/public/qrcodes/qr-code.png';
    }
}
