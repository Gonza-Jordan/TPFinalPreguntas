<?php
require_once __DIR__ . '/../helper/PDFHelper.php';

class AdminController
{
    private $presenter;
    private $usuarioModel;
    private $partidaModel;
    private $preguntaModel;

    public function __construct($presenter, $usuarioModel, $partidaModel, $preguntaModel)
    {
        $this->presenter = $presenter;
        $this->usuarioModel = $usuarioModel;
        $this->partidaModel = $partidaModel;
        $this->preguntaModel = $preguntaModel;

        // Inicializar PDFHelper con la ruta de las plantillas
        PDFHelper::init(__DIR__ . '/../templates'); // Ruta donde están las vistas
    }

    public function showDashboard($filtro_tiempo = null, $usuarioId)
    {
        $this->usuarioModel->esAdministrador($usuarioId);
        $rangoFechas = $this->obtenerRangoFechas($filtro_tiempo);

        $datos = [
            'cantidad_usuarios' => $this->usuarioModel->getCantidadUsuarios(),
            'cantidad_partidas' => $this->partidaModel->getCantidadPartidas(),
            'cantidad_preguntas' => $this->preguntaModel->getCantidadPreguntas(),
            'preguntas_creadas' => $this->preguntaModel->getPreguntasPorRango($rangoFechas),
            'cantidad_usuarios_nuevos' => $this->usuarioModel->getCantidadUsuariosNuevos(),
            'usuarios_por_pais' => $this->usuarioModel->getUsuariosPorPais(),
            'usuarios_por_sexo' => $this->usuarioModel->getUsuariosPorSexo(),
            'usuarios_por_grupo_edad' => $this->usuarioModel->getUsuariosPorGrupoEdad(),
            'porcentaje_respuestas_correctas' => $this->partidaModel->getPorcentajeRespuestasPorUsuario(),
            'filtro_tiempo' => $filtro_tiempo ?? 'todo'
            ];

        // Convertir algunos datos a JSON para gráficos
        $datos['usuarios_por_pais_json'] = json_encode($datos['usuarios_por_pais']);
        $datos['usuarios_por_sexo_json'] = json_encode($datos['usuarios_por_sexo']);

        $this->presenter->show("admin_dashboard", $datos);
    }
    public function printReport($filtro_tiempo = null, $usuarioId)
    {

        $this->usuarioModel->esAdministrador($usuarioId);
        $rangoFechas = $this->obtenerRangoFechas($filtro_tiempo);


        $datos = [
            'cantidad_usuarios' => $this->usuarioModel->getCantidadUsuarios(),
            'cantidad_partidas' => $this->partidaModel->getCantidadPartidas(),
            'cantidad_preguntas' => $this->preguntaModel->getCantidadPreguntas(),
            'preguntas_creadas' => $this->preguntaModel->getPreguntasPorRango($rangoFechas),
            'cantidad_usuarios_nuevos' => $this->usuarioModel->getCantidadUsuariosPorRango($rangoFechas),
            'usuarios_por_pais' => $this->usuarioModel->getUsuariosPorPais(),
            'usuarios_por_sexo' => $this->usuarioModel->getUsuariosPorSexo(),
            'usuarios_por_grupo_edad' => $this->usuarioModel->getUsuariosPorGrupoEdad(),
            'porcentaje_respuestas_correctas' => $this->partidaModel->getPorcentajeRespuestasPorUsuario(),
        ];


        $this->presenter->show("admin_reporte_imprimible", $datos);
    }


    public function printPDF($filtro_tiempo = null, $usuarioId)
    {
        $this->usuarioModel->esAdministrador($usuarioId);
        $rangoFechas = $this->obtenerRangoFechas($filtro_tiempo);

        $datos = [
            'fecha_generado' => date('Y-m-d H:i:s'),
            'cantidad_usuarios' => $this->usuarioModel->getCantidadUsuarios(),
            'cantidad_partidas' => $this->partidaModel->getCantidadPartidas(),
            'cantidad_preguntas' => $this->preguntaModel->getCantidadPreguntas(),
            'preguntas_creadas' => $this->preguntaModel->getPreguntasPorRango($rangoFechas),
            'cantidad_usuarios_nuevos' => $this->usuarioModel->getCantidadUsuariosPorRango($rangoFechas),
            'usuarios_por_pais' => $this->usuarioModel->getUsuariosPorPais(),
            'usuarios_por_sexo' => $this->usuarioModel->getUsuariosPorSexo(),
            'porcentaje_respuestas_correctas' => $this->partidaModel->getPorcentajeRespuestasPorUsuario(),
        ];

        // Generar el reporte PDF utilizando la sintaxis estática
        PDFHelper::stream('admin_reporte', $datos, [
            'format' => 'A4',
            'orientation' => 'portrait'
        ]);
    }

       /**
     * Genera reporte estadístico de usuarios
     */
    public function generarReporteUsuarios($rangoFechas = null) {
        // Si no se proporciona rango de fechas, usar último mes
        if (!$rangoFechas) {
            $rangoFechas = [
                'inicio' => date('Y-m-d', strtotime('-1 month')),
                'fin' => date('Y-m-d')
            ];
        }
        
        // Recopilar todos los datos usando los métodos existentes
        $datosReporte = [
            'fecha_generacion' => date('Y-m-d H:i:s'),
            'rango_fechas' => $rangoFechas,
            'estadisticas' => [
                'total_usuarios' => $this->usuarioModel->getCantidadUsuarios(),
                'usuarios_periodo' => $this->usuarioModel->getCantidadUsuariosPorRango($rangoFechas),
                'distribucion_paises' => $this->usuarioModel->getUsuariosPorPais(),
                'distribucion_sexo' => $this->usuarioModel->getUsuariosPorSexo(),
                'distribucion_edad' => $this->usuarioModel->getUsuariosPorGrupoEdad()
            ]
        ];
        
        // Calcular algunos porcentajes adicionales
        $datosReporte['estadisticas']['porcentajes'] = $this->calcularPorcentajes($datosReporte['estadisticas']);
        
        $template = __DIR__ . '/../Views/reports/estadisticas_usuarios.mustache';
        PDFHelper::generateReport(
            $template,
            $datosReporte,
            'reporte_usuarios_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Calcula porcentajes adicionales para el reporte
     */
    private function calcularPorcentajes($estadisticas) {
        $porcentajes = [];
        
        // Porcentajes por país
        $total = array_sum(array_column($estadisticas['distribucion_paises'], 'cantidad'));
        $porcentajes['paises'] = array_map(function($pais) use ($total) {
            return [
                'pais' => $pais['pais'],
                'cantidad' => $pais['cantidad'],
                'porcentaje' => round(($pais['cantidad'] / $total) * 100, 2)
            ];
        }, $estadisticas['distribucion_paises']);
        
        // Porcentajes por grupo de edad
        $totalEdad = array_sum(array_column($estadisticas['distribucion_edad'], 'cantidad'));
        $porcentajes['grupos_edad'] = array_map(function($grupo) use ($totalEdad) {
            return [
                'grupo' => $grupo['grupo'],
                'cantidad' => $grupo['cantidad'],
                'porcentaje' => round(($grupo['cantidad'] / $totalEdad) * 100, 2)
            ];
        }, $estadisticas['distribucion_edad']);
        
        return $porcentajes;
    }
}

    private function obtenerRangoFechas($filtro_tiempo)
    {
        $hoy = date('Y-m-d');
        switch ($filtro_tiempo) {
            case 'dia':
                return ['inicio' => $hoy . ' 00:00:00', 'fin' => $hoy . ' 23:59:59'];
            case 'semana':
                return [
                    'inicio' => date('Y-m-d 00:00:00', strtotime('monday this week')),
                    'fin' => date('Y-m-d 23:59:59', strtotime('sunday this week')),
                ];
            case 'mes':
                return [
                    'inicio' => date('Y-m-01 00:00:00'),
                    'fin' => date('Y-m-t 23:59:59'),
                ];
            case 'anio':
                return [
                    'inicio' => date('Y-01-01 00:00:00'),
                    'fin' => date('Y-12-31 23:59:59'),
                ];
            default:
                return ['inicio' => '2000-01-01 00:00:00', 'fin' => $hoy . ' 23:59:59'];
        }
    }

}
