<?php

class TemplateEngine {

    public static function render($templatePath, $variables = []) {
        if (!file_exists($templatePath)) {
            error_log("Plantilla no encontrada en la ruta: " . $templatePath);
            throw new \Exception("Plantilla no encontrada: " . $templatePath);
        }

        $templateContent = file_get_contents($templatePath);

        // Manejo de bloques condicionales y listas
        foreach ($variables as $key => $value) {
            // Bloques de arrays
            if (is_array($value)) {
                // Encuentra el bloque {{#key}}...{{/key}}
                if (preg_match('/{{#' . $key . '}}(.*?){{\/' . $key . '}}/s', $templateContent, $matches)) {
                    $blockContent = '';
                    foreach ($value as $item) {
                        $itemContent = $matches[1];
                        // Reemplaza variables dentro del bloque
                        foreach ($item as $itemKey => $itemValue) {
                            $itemContent = str_replace('{{' . $itemKey . '}}', $itemValue, $itemContent);
                        }
                        $blockContent .= $itemContent;
                    }
                    // Reemplaza el bloque completo en el contenido de la plantilla
                    $templateContent = preg_replace('/{{#' . $key . '}}(.*?){{\/' . $key . '}}/s', $blockContent, $templateContent);
                }
            }
            // Bloques condicionales para booleanos y variables vacías
            elseif (is_bool($value) || empty($value)) {
                if ($value) {
                    $templateContent = preg_replace('/{{#' . $key . '}}(.*?){{\/' . $key . '}}/s', '$1', $templateContent);
                } else {
                    $templateContent = preg_replace('/{{#' . $key . '}}(.*?){{\/' . $key . '}}/s', '', $templateContent);
                }
            }
        }

        // Sustitución de variables simples
        foreach ($variables as $key => $value) {
            if (!is_array($value) && !is_bool($value)) {
                $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
            }
        }

        return $templateContent;
    }
}

