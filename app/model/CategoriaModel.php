<?php
class CategoriaModel {
    private $categorias = [
        'deportes' => [
            'color' => '#FF0000', // rojo
            'imagen' => 'deportes.png',
            'personaje' => 'Ira'
        ],
        'historia' => [
            'color' => '#0000FF', // azul
            'imagen' => 'historia.png',
            'personaje' => 'Tristeza'
        ],
        'ciencia' => [
            'color' => '#8A2BE2', // violeta
            'imagen' => 'ciencia.png',
            'personaje' => 'Miedo'
        ],
        'arte' => [
            'color' => '#006400', // verde oscuro
            'imagen' => 'arte.png',
            'personaje' => 'Desagrado'
        ],
        'geografia' => [
            'color' => '#FFFF00', // amarillo
            'imagen' => 'geografia.png',
            'personaje' => 'Alegría'
        ],
        'entretenimiento' => [
            'color' => '#FF00FF', // fucsia
            'imagen' => 'entretenimiento.png',
            'personaje' => 'BingBong'
        ]
    ];

    public function getCategoriaDatos($categoria) {
        return $this->categorias[strtolower($categoria)] ?? null;
    }

    public function getAllCategorias() {
        return $this->categorias;
    }
}
