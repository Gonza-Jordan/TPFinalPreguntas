<?php

class TemplateEngine {

    public static function render($templatePath, $variables = []) {
        if (!file_exists($templatePath)) {
            return "Error: plantilla no encontrada.";
        }

        $templateContent = file_get_contents($templatePath);

        foreach ($variables as $key => $value) {
            if (is_bool($value) || empty($value)) {
                if ($value) {
                    $templateContent = preg_replace('/{{#' . $key . '}}(.*?){{\/' . $key . '}}/s', '$1', $templateContent);
                } else {
                    $templateContent = preg_replace('/{{#' . $key . '}}(.*?){{\/' . $key . '}}/s', '', $templateContent);
                }
            }
        }

        foreach ($variables as $key => $value) {
            if (!is_bool($value)) {
                $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
            }
        }

        return $templateContent;
    }
}
