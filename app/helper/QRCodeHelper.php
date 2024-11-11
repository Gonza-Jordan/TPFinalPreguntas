<?php

class QRCodeHelper
{
    public static function generateQRCode(string $url): string
    {
        // Verificar si la URL cumple con el patrón esperado
        $pattern = '/^http:\/\/localhost:8080\/perfil\/id\/(\d+)$/';
        if (!preg_match($pattern, $url, $matches)) {
            throw new \InvalidArgumentException('La URL proporcionada no cumple con el patrón esperado.');
        }

        // Obtener el ID del usuario a partir de la URL
        $userId = $matches[1];

        // Generar el código QR
        $qrCode = \QRCode::text("http://localhost:8080/perfil/$userId")
            ->setSize(5)
            ->setMargin(2)
            ->setOutfile('qr-code.png')
            ->save();

        return $qrCode;
    }
}