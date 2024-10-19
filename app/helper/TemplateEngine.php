<?php

class TemplateEngine {

    // Función para cargar una plantilla y reemplazar las variables
    public static function render($templatePath, $variables = []) {
        // Verificar si el archivo de plantilla existe
        if (!file_exists($templatePath)) {
            return "Error: plantilla no encontrada.";
        }

        // Cargar el contenido de la plantilla
        $templateContent = file_get_contents($templatePath);

        // Procesar bloques condicionales
        foreach ($variables as $key => $value) {
            if (is_bool($value) || empty($value)) {
                // Si la variable es booleana y true, renderizamos el bloque
                if ($value) {
                    $templateContent = preg_replace('/{{#' . $key . '}}(.*?){{\/' . $key . '}}/s', '$1', $templateContent);
                } else {
                    // Si es false o está vacío, eliminamos el bloque
                    $templateContent = preg_replace('/{{#' . $key . '}}(.*?){{\/' . $key . '}}/s', '', $templateContent);
                }
            }
        }

        // Reemplazar las variables en la plantilla
        foreach ($variables as $key => $value) {
            if (!is_bool($value)) {
                // Reemplazar {{variable}} con el valor correspondiente
                $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
            }
        }

        // Devolver la plantilla renderizada
        return $templateContent;
    }
}
