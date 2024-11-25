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

        PDFHelper::init(__DIR__ . '/../view');
    }

    public function show()
    {

        $usuarioId = $_SESSION['user_id'] ?? null;
        $this->usuarioModel->esAdministrador($usuarioId);

        $filtro_tiempo = $_POST['filtro_tiempo'] ?? null;
        $rangoFechas = $this->obtenerRangoFechas($filtro_tiempo);

        $datos = [
            'cantidad_usuarios' => $this->usuarioModel->getCantidadUsuarios($rangoFechas),
            'cantidad_partidas' => $this->partidaModel->getCantidadPartidas($rangoFechas),
            'cantidad_preguntas' => $this->preguntaModel->getCantidadPreguntas($rangoFechas),
            'usuarios_por_pais' => $this->usuarioModel->getUsuariosPorPais($rangoFechas),
            'usuarios_por_sexo' => $this->usuarioModel->getUsuariosPorSexo($rangoFechas),
            'usuarios_por_grupo_edad' => $this->usuarioModel->getUsuariosPorGrupoEdad($rangoFechas),
            'porcentaje_respuestas_correctas' => $this->partidaModel->getPorcentajeRespuestasPorUsuario($rangoFechas),
            'filtro_tiempo' => $filtro_tiempo ?? 'todo'
            ];

        $datos['usuarios_por_pais_json'] = json_encode($datos['usuarios_por_pais']);
        $datos['usuarios_por_sexo_json'] = json_encode($datos['usuarios_por_sexo']);

        $datos['filtros'] = [
            ['valor' => 'todo', 'nombre' => '-', 'seleccionado' => $filtro_tiempo === 'todo'],
            ['valor' => 'dia', 'nombre' => 'Día', 'seleccionado' => $filtro_tiempo === 'dia'],
            ['valor' => 'semana', 'nombre' => 'Semana', 'seleccionado' => $filtro_tiempo === 'semana'],
            ['valor' => 'mes', 'nombre' => 'Mes', 'seleccionado' => $filtro_tiempo === 'mes'],
            ['valor' => 'anio', 'nombre' => 'Año', 'seleccionado' => $filtro_tiempo === 'anio']
        ];

        $this->presenter->show("admin_dashboard", $datos);
    }
    public function printReport($filtro_tiempo = null, $usuarioId)
    {

        $this->usuarioModel->esAdministrador($usuarioId);
        $rangoFechas = $this->obtenerRangoFechas($filtro_tiempo);


        $datos = [
            'cantidad_usuarios' => $this->usuarioModel->getCantidadUsuarios($rangoFechas),
            'cantidad_partidas' => $this->partidaModel->getCantidadPartidas($rangoFechas),
            'cantidad_preguntas' => $this->preguntaModel->getCantidadPreguntas($rangoFechas),
            'usuarios_por_pais' => $this->usuarioModel->getUsuariosPorPais($rangoFechas),
            'usuarios_por_sexo' => $this->usuarioModel->getUsuariosPorSexo($rangoFechas),
            'usuarios_por_grupo_edad' => $this->usuarioModel->getUsuariosPorGrupoEdad($rangoFechas),
            'porcentaje_respuestas_correctas' => $this->partidaModel->getPorcentajeRespuestasPorUsuario($rangoFechas),
        ];

        $datos['filtros'] = [
            ['valor' => 'todo', 'nombre' => '-', 'seleccionado' => $filtro_tiempo === 'todo'],
            ['valor' => 'dia', 'nombre' => 'Día', 'seleccionado' => $filtro_tiempo === 'dia'],
            ['valor' => 'semana', 'nombre' => 'Semana', 'seleccionado' => $filtro_tiempo === 'semana'],
            ['valor' => 'mes', 'nombre' => 'Mes', 'seleccionado' => $filtro_tiempo === 'mes'],
            ['valor' => 'anio', 'nombre' => 'Año', 'seleccionado' => $filtro_tiempo === 'anio']
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

        PDFHelper::stream('admin_dashboard', $datos, [
            'format' => 'A4',
            'orientation' => 'portrait'
        ]);
    }

    public function generarReporteUsuarios($rangoFechas = null) {
        if (!$rangoFechas) {
            $rangoFechas = [
                'inicio' => date('Y-m-d', strtotime('-1 month')),
                'fin' => date('Y-m-d')
            ];
        }

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

        $datosReporte['estadisticas']['porcentajes'] = $this->calcularPorcentajes($datosReporte['estadisticas']);

        PDFHelper::download('admin_dashboard', $datos, 'reporte_usuarios_' . date('Y-m-d') . '.pdf', [
            'format' => 'A4',
            'orientation' => 'portrait'
        ]);
    }

    private function calcularPorcentajes($estadisticas) {
        $porcentajes = [];

        $total = array_sum(array_column($estadisticas['distribucion_paises'], 'cantidad'));
        $porcentajes['paises'] = array_map(function($pais) use ($total) {
            return [
                'pais' => $pais['pais'],
                'cantidad' => $pais['cantidad'],
                'porcentaje' => round(($pais['cantidad'] / $total) * 100, 2)
            ];
        }, $estadisticas['distribucion_paises']);

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

    public function obtenerRangoFechas($filtro_tiempo)
    {
        $hoy = new DateTime();
        $inicio = clone $hoy;

        switch ($filtro_tiempo) {
            case 'dia':
                // Solo hoy
                break;
            case 'semana':
                $inicio->modify('-7 days');
                break;
            case 'mes':
                $inicio->modify('-1 month');
                break;
            case 'anio':
                $inicio->modify('-1 year');
                break;
            default:
                return null;
        }

        return [
            'inicio' => $inicio->format('Y-m-d H:i:s'),
            'fin' => $hoy->format('Y-m-d H:i:s')
        ];
    }


}
