<?php

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
    }

    public function showDashboard($filtro_tiempo = null)
    {
        $usuarioId = $_SESSION['user_id'] ?? null;
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
