USE tpfinalpreguntas;
CREATE TABLE usuarios (
                          id_usuario INT AUTO_INCREMENT PRIMARY KEY,
                          nombre_usuario VARCHAR(50) NOT NULL,
                          contraseña VARCHAR(255) NOT NULL,
                          nombre_completo VARCHAR(100),
                          email VARCHAR(100) NOT NULL,
                          anio_nacimiento INT,
                          sexo VARCHAR(20),
                          pais VARCHAR(50),
                          ciudad VARCHAR(50),
                          foto_perfil VARCHAR(255),
                          puntaje_total INT DEFAULT 0,
                          trampitas INT DEFAULT 0,
                          tipo_usuario VARCHAR(20) DEFAULT 'jugador',
                          fecha_creacion DATE DEFAULT CURRENT_DATE,
                          validado BOOLEAN DEFAULT FALSE,
                          token_activacion VARCHAR(32)
);

INSERT INTO usuarios (nombre_usuario, contraseña, nombre_completo, email, anio_nacimiento, sexo, pais, ciudad, foto_perfil, puntaje_total, trampitas, tipo_usuario, validado)
VALUES ('jmartinez', '$2y$10$zhmzgVp2Ud/5VQ5DWBAnFugtpa2rkED1MG.w1BlcSaS.J6JTanZbq', 'Juan Martinez', 'juan.martinez@example.com', 1990, 'Masculino', 'Argentina', 'Buenos Aires', 'foto_1.jpg', 150, 3, 'jugador', TRUE);

INSERT INTO usuarios (nombre_usuario, contraseña, nombre_completo, email, anio_nacimiento, sexo, pais, ciudad, foto_perfil, puntaje_total, trampitas, tipo_usuario, validado)
VALUES ('mperez', '$2y$10$zhmzgVp2Ud/5VQ5DWBAnFugtpa2rkED1MG.w1BlcSaS.J6JTanZbq', 'Maria Perez', 'maria.perez@example.com', 1985, 'Femenino', 'Mexico', 'Guadalajara', 'foto_2.jpg', 200, 5, 'editor', FALSE);

INSERT INTO usuarios (nombre_usuario, contraseña, nombre_completo, email, anio_nacimiento, sexo, pais, ciudad, foto_perfil, puntaje_total, trampitas, tipo_usuario, validado)
VALUES ('lgomez', '$2y$10$zhmzgVp2Ud/5VQ5DWBAnFugtpa2rkED1MG.w1BlcSaS.J6JTanZbq', 'Luis Gomez', 'luis.gomez@example.com', 2000, 'Prefiero no cargarlo', 'España', 'Madrid', 'foto_3.jpg', 100, 1, 'jugador', TRUE);

--Creo la tabla preguntas
CREATE TABLE preguntas (
                           id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
                           contenido TEXT NOT NULL,
                           categoria VARCHAR(100) NOT NULL,
                           nivel_dificultad VARCHAR(50) NOT NULL,
                           opcion_a TEXT NOT NULL,
                           opcion_b TEXT NOT NULL,
                           opcion_c TEXT NOT NULL,
                           opcion_d TEXT NOT NULL,
                           respuesta_correcta CHAR(1) NOT NULL,
                           creada_por INT NOT NULL,
                           estado_aprobacion ENUM('Aprobada', 'No Aprobada', 'Rechazada', 'En Revisión') NOT NULL DEFAULT 'En Revisión',
                           veces_respondida INT DEFAULT 0,
                           veces_respondida_correctamente INT DEFAULT 0,
                           fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--Preguntas: Historia
INSERT INTO preguntas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, creada_por, estado_aprobacion)
VALUES
    ('¿En qué año comenzó la Primera Guerra Mundial?', 'Historia', 'Media', '1912', '1914', '1916', '1918', 'B', '1', 'Aprobada'),
    ('¿Quién fue el primer presidente de los Estados Unidos?', 'Historia', 'Fácil', 'Abraham Lincoln', 'John Adams', 'George Washington', 'Thomas Jefferson', 'C', '1', 'Aprobada'),
    ('¿Qué civilización construyó las pirámides de Giza?', 'Historia', 'Fácil', 'Griegos', 'Romanos', 'Egipcios', 'Persas', 'C', '1', 'Aprobada'),
    ('¿Quién fue conocido como el "Libertador de América"?', 'Historia', 'Media', 'San Martín', 'Simón Bolívar', 'Hidalgo', 'Francisco de Miranda', 'B', '1', 'Aprobada'),
    ('¿En qué año cayó el Imperio Romano de Occidente?', 'Historia', 'Difícil', '476 d.C.', '410 d.C.', '395 d.C.', '493 d.C.', 'A', '1', 'Aprobada'),
    ('¿Quién fue el emperador de Francia que conquistó gran parte de Europa a principios del siglo XIX?', 'Historia', 'Media', 'Luis XIV', 'Napoleón Bonaparte', 'Carlos X', 'Luis XVIII', 'B', '1', 'Aprobada'),
    ('¿Qué evento marcó el inicio de la Revolución Francesa?', 'Historia', 'Media', 'La ejecución de Luis XVI', 'La Toma de la Bastilla', 'La caída de Robespierre', 'El Congreso de Viena', 'B', '1', 'Aprobada'),
    ('¿Cuál fue la principal causa de la Guerra Civil Estadounidense?', 'Historia', 'Media', 'El control del gobierno', 'La esclavitud', 'El control del comercio', 'El expansionismo', 'B', '1', 'Aprobada'),
    ('¿Qué país lanzó la primera bomba atómica en guerra?', 'Historia', 'Media', 'Alemania', 'Japón', 'Estados Unidos', 'Reino Unido', 'C', '1', 'Aprobada'),
    ('¿En qué año llegó Cristóbal Colón a América?', 'Historia', 'Fácil', '1490', '1492', '1494', '1496', 'B', '1', 'Aprobada');

--Preguntas: Deportes
INSERT INTO preguntas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, creada_por, estado_aprobacion)
VALUES
    ('¿Quién ganó el Mundial de Fútbol 2018?', 'Deportes', 'Fácil', 'Alemania', 'Francia', 'Brasil', 'Argentina', 'B', '2', 'Aprobada'),
    ('¿En qué año se celebraron los primeros Juegos Olímpicos modernos?', 'Deportes', 'Media', '1892', '1896', '1900', '1904', 'B', '2', 'Aprobada'),
    ('¿Cuántos jugadores forman un equipo de baloncesto en cancha?', 'Deportes', 'Fácil', '5', '6', '7', '8', 'A', '2', 'Aprobada'),
    ('¿Qué tenista ha ganado más títulos de Grand Slam?', 'Deportes', 'Difícil', 'Roger Federer', 'Novak Djokovic', 'Rafael Nadal', 'Pete Sampras', 'C', '2', 'Aprobada'),
    ('¿Qué país ganó la Copa América 2021?', 'Deportes', 'Fácil', 'Brasil', 'Argentina', 'Uruguay', 'Chile', 'B', '2', 'Aprobada'),
    ('¿Cuál es la única nación que ha jugado todas las Copas del Mundo de fútbol?', 'Deportes', 'Fácil', 'Alemania', 'Argentina', 'Brasil', 'Italia', 'C', '2', 'Aprobada'),
    ('¿Qué deporte es conocido como el "rey de los deportes"?', 'Deportes', 'Fácil', 'Tenis', 'Fútbol', 'Béisbol', 'Baloncesto', 'B', '2', 'Aprobada'),
    ('¿Qué nadador ha ganado la mayor cantidad de medallas olímpicas?', 'Deportes', 'Media', 'Michael Phelps', 'Mark Spitz', 'Ian Thorpe', 'Ryan Lochte', 'A', '2', 'Aprobada'),
    ('¿Qué equipo ha ganado más Super Bowls en la historia de la NFL?', 'Deportes', 'Media', 'Dallas Cowboys', 'San Francisco 49ers', 'Pittsburgh Steelers', 'New England Patriots', 'C', '2', 'Aprobada'),
    ('¿Quién tiene el récord de puntos en la NBA?', 'Deportes', 'Difícil', 'Kobe Bryant', 'Michael Jordan', 'LeBron James', 'Kareem Abdul-Jabbar', 'D', '2', 'Aprobada');

--Preguntas: Geografía
INSERT INTO preguntas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, creada_por, estado_aprobacion)
VALUES
    ('¿Cuál es el país más grande del mundo por superficie?', 'Geografía', 'Fácil', 'Estados Unidos', 'Canadá', 'China', 'Rusia', 'D', '3', 'Aprobada'),
    ('¿Qué océano es el más grande del mundo?', 'Geografía', 'Fácil', 'Atlántico', 'Pacífico', 'Índico', 'Ártico', 'B', '3', 'Aprobada'),
    ('¿En qué continente se encuentra Egipto?', 'Geografía', 'Fácil', 'Asia', 'Europa', 'África', 'América', 'C', '3', 'Aprobada'),
    ('¿Cuál es la capital de Canadá?', 'Geografía', 'Fácil', 'Toronto', 'Ottawa', 'Vancouver', 'Montreal', 'B', '3', 'Aprobada'),
    ('¿Qué país tiene la mayor población del mundo?', 'Geografía', 'Fácil', 'India', 'China', 'Estados Unidos', 'Indonesia', 'B', '3', 'Aprobada'),
    ('¿Dónde se encuentra la montaña más alta del mundo?', 'Geografía', 'Media', 'China', 'India', 'Nepal', 'Pakistán', 'C', '3', 'Aprobada'),
    ('¿Cuál es el río más largo del mundo?', 'Geografía', 'Media', 'Amazonas', 'Yangtsé', 'Mississippi', 'Nilo', 'D', '3', 'Aprobada'),
    ('¿Cuál es el desierto más grande del mundo?', 'Geografía', 'Media', 'Gobi', 'Sahara', 'Atacama', 'Kalahari', 'B', '3', 'Aprobada'),
    ('¿Qué país tiene más islas en el mundo?', 'Geografía', 'Difícil', 'Filipinas', 'Indonesia', 'Suecia', 'Finlandia', 'C', '3', 'Aprobada'),
    ('¿Qué país está rodeado completamente por Sudáfrica?', 'Geografía', 'Difícil', 'Botsuana', 'Zimbabue', 'Lesoto', 'Namibia', 'C', '3', 'Aprobada');

--Preguntas: Arte
INSERT INTO preguntas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, creada_por, estado_aprobacion)
VALUES
    ('¿Quién pintó la Mona Lisa?', 'Arte', 'Fácil', 'Vincent van Gogh', 'Pablo Picasso', 'Leonardo da Vinci', 'Claude Monet', 'C', '4', 'Aprobada'),
    ('¿Qué estilo artístico está asociado con Picasso?', 'Arte', 'Media', 'Surrealismo', 'Impresionismo', 'Cubismo', 'Barroco', 'C', '4', 'Aprobada'),
    ('¿Cuál es la famosa escultura de mármol de Miguel Ángel?', 'Arte', 'Fácil', 'Piedad', 'David', 'Venus de Milo', 'El Pensador', 'B', '4', 'Aprobada'),
    ('¿En qué museo se encuentra la Mona Lisa?', 'Arte', 'Fácil', 'Museo del Prado', 'Galería Uffizi', 'Louvre', 'Tate Modern', 'C', '4', 'Aprobada'),
    ('¿Qué artista es famoso por su serie de pinturas de girasoles?', 'Arte', 'Fácil', 'Vincent van Gogh', 'Paul Gauguin', 'Claude Monet', 'Paul Cézanne', 'A', '4', 'Aprobada'),
    ('¿Quién pintó la Capilla Sixtina?', 'Arte', 'Media', 'Rafael', 'Miguel Ángel', 'Leonardo da Vinci', 'Tiziano', 'B', '4', 'Aprobada'),
    ('¿Qué obra de arte es conocida como "La noche estrellada"?', 'Arte', 'Media', 'Pablo Picasso', 'Vincent van Gogh', 'Claude Monet', 'Edvard Munch', 'B', '4', 'Aprobada'),
    ('¿Qué compositor es conocido por su obra "La Quinta Sinfonía"?', 'Arte', 'Fácil', 'Mozart', 'Beethoven', 'Bach', 'Chopin', 'B', '4', 'Aprobada'),
    ('¿Qué movimiento artístico surgió a fines del siglo XIX y se caracteriza por la captura de la luz?', 'Arte', 'Media', 'Realismo', 'Impresionismo', 'Romanticismo', 'Expresionismo', 'B', '4', 'Aprobada'),
    ('¿Quién esculpió "El Pensador"?', 'Arte', 'Media', 'Auguste Rodin', 'Donatello', 'Bernini', 'Miguel Ángel', 'A', '4', 'Aprobada');

--Preguntas: Ciencia
INSERT INTO preguntas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, creada_por, estado_aprobacion)
VALUES
    ('¿Cuál es el elemento más abundante en la atmósfera de la Tierra?', 'Ciencia', 'Fácil', 'Oxígeno', 'Nitrógeno', 'Carbono', 'Hidrógeno', 'B', '5', 'Aprobada'),
    ('¿Qué científico propuso la teoría de la relatividad?', 'Ciencia', 'Fácil', 'Isaac Newton', 'Galileo Galilei', '5 Einstein', 'Niels Bohr', 'C', '5', 'Aprobada'),
    ('¿Cuál es la sustancia más dura conocida por el hombre?', 'Ciencia', 'Media', 'Hierro', 'Acero', 'Diamante', 'Plomo', 'C', '5', 'Aprobada'),
    ('¿Qué planeta es conocido como el "Planeta Rojo"?', 'Ciencia', 'Fácil', 'Júpiter', 'Marte', 'Venus', 'Saturno', 'B', '5', 'Aprobada'),
    ('¿Cuál es la velocidad de la luz en el vacío?', 'Ciencia', 'Difícil', '150,000,000 m/s', '299,792,458 m/s', '1,080,000 m/s', '500,000,000 m/s', 'B', '5', 'Aprobada'),
    ('¿Qué parte de la célula es conocida como la "central energética"?', 'Ciencia', 'Fácil', 'Núcleo', 'Membrana', 'Ribosoma', 'Mitocondria', 'D', '5', 'Aprobada'),
    ('¿Cuál es el gas responsable del efecto invernadero?', 'Ciencia', 'Media', 'Metano', 'Dióxido de carbono', 'Oxígeno', 'Helio', 'B', '5', 'Aprobada'),
    ('¿Qué ley describe la relación entre la presión y el volumen de un gas?', 'Ciencia', 'Difícil', 'Ley de Dalton', 'Ley de Boyle', 'Ley de Charles', 'Ley de Avogadro', 'B', '5', 'Aprobada'),
    ('¿Qué partícula subatómica tiene carga positiva?', 'Ciencia', 'Fácil', 'Neutrón', 'Protón', 'Electrón', 'Fotón', 'B', '5', 'Aprobada'),
    ('¿Cuál es el órgano más grande del cuerpo humano?', 'Ciencia', 'Fácil', 'Corazón', 'Hígado', 'Cerebro', 'Piel', 'D', '5', 'Aprobada');

--Preguntas: Entretenimiento 
INSERT INTO preguntas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, creada_por, estado_aprobacion)
VALUES
    ('¿Quién protagonizó la película "Titanic" en 1997?', 'Entretenimiento', 'Fácil', 'Tom Hanks', 'Leonardo DiCaprio', 'Brad Pitt', 'Johnny Depp', 'B', '6', 'Aprobada'),
    ('¿Qué banda lanzó el álbum "Abbey Road" en 1969?', 'Entretenimiento', 'Fácil', 'The Rolling Stones', 'Led Zeppelin', 'The Beatles', 'Pink Floyd', 'C', '6', 'Aprobada'),
    ('¿En qué año se lanzó el videojuego "Fortnite"?', 'Entretenimiento', 'Media', '2015', '2016', '2017', '2018', 'C', '6', 'Aprobada'),
    ('¿Qué serie de televisión tiene como protagonistas a "Ross", "Rachel", y "Chandler"?', 'Entretenimiento', 'Fácil', 'Friends', 'How I Met Your Mother', 'The Big Bang Theory', 'Seinfeld', 'A', '6', 'Aprobada'),
    ('¿Quién es el creador del universo cinematográfico de Marvel?', 'Entretenimiento', 'Fácil', 'Jack Kirby', 'Stan Lee', 'Steve Ditko', 'Joe Simon', 'B', '6', 'Aprobada'),
    ('¿Cuál es la película más taquillera de todos los tiempos?', 'Entretenimiento', 'Media', 'Avatar', 'Titanic', 'Avengers: Endgame', 'Star Wars: The Force Awakens', 'C', '6', 'Aprobada'),
    ('¿Qué artista interpretó la canción "Thriller"?', 'Entretenimiento', 'Fácil', 'Prince', 'Michael Jackson', 'Elvis Presley', 'Stevie Wonder', 'B', '6', 'Aprobada'),
    ('¿Qué videojuego incluye la franquicia "Zelda"?', 'Entretenimiento', 'Fácil', 'Final Fantasy', 'The Legend of Zelda', 'Super Mario Bros', 'Metroid', 'B', '6', 'Aprobada'),
    ('¿Qué actriz interpretó a Hermione Granger en la serie de películas de Harry Potter?', 'Entretenimiento', 'Fácil', 'Emma Watson', 'Emma Thompson', 'Helena Bonham Carter', 'Maggie Smith', 'A', '6', 'Aprobada'),
    ('¿Qué película ganó el Oscar a mejor película en 2020?', 'Entretenimiento', 'Difícil', '1917', 'Parasite', 'Joker', 'Once Upon a Time in Hollywood', 'B', '6', 'Aprobada');

--tabla usuario_preguntas
CREATE TABLE usuarios_preguntas (
                                    id INT PRIMARY KEY AUTO_INCREMENT,
                                    id_usuario INT,
                                    id_pregunta INT,
                                    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
                                    FOREIGN KEY (id_pregunta) REFERENCES preguntas(id_pregunta)
);

--tabla partidas
CREATE TABLE partidas (
                          id_partida INT AUTO_INCREMENT PRIMARY KEY,
                          id_usuario INT,
                          id_pregunta INT,
                          horario_inicio DATETIME,
                          puntos_sumados INT DEFAULT 0,
                          estado VARCHAR(20)
);

--tabla ranking
CREATE TABLE ranking (
                          id_ranking INT AUTO_INCREMENT PRIMARY KEY,
                          id_usuario INT,
                          puntaje_total INT,
                          posicion INT,
                          FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

--rol editor/admin
ALTER TABLE usuarios
    MODIFY COLUMN tipo_usuario ENUM('jugador', 'editor', 'administrador') DEFAULT 'jugador';

--Pregunta sugerida
CREATE TABLE preguntas_sugeridas (
                                     id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
                                     contenido TEXT NOT NULL,
                                     categoria VARCHAR(100) NOT NULL,
                                     nivel_dificultad VARCHAR(50) DEFAULT 'facil',
                                     opcion_a TEXT NOT NULL,
                                     opcion_b TEXT NOT NULL,
                                     opcion_c TEXT NOT NULL,
                                     opcion_d TEXT NOT NULL,
                                     respuesta_correcta CHAR(1) NOT NULL,
                                     creada_por INT NOT NULL,
                                     estado ENUM('Pendiente', 'Aprobada', 'Rechazada') DEFAULT 'Pendiente',
                                     fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                     FOREIGN KEY (creada_por) REFERENCES usuarios(id_usuario)
);

--insert de ejemplo
INSERT INTO preguntas_sugeridas (contenido, categoria, nivel_dificultad, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, creada_por, estado)
VALUES
    ('¿Cuál es la capital de Francia?', 'Geografía', 'facil', 'Madrid', 'París', 'Roma', 'Berlín', 'B', 1, 'Pendiente'),

    ('¿Quién pintó La última cena?', 'Arte', 'medio', 'Miguel Ángel', 'Leonardo da Vinci', 'Rafael', 'Donatello', 'B', 2, 'Pendiente'),

    ('¿Cuál es el gas más abundante en la atmósfera terrestre?', 'Ciencia', 'dificil', 'Oxígeno', 'Nitrógeno', 'Dióxido de Carbono', 'Hidrógeno', 'B', 3, 'Pendiente');


--Pregunta reportada
CREATE TABLE preguntas_reportadas (
                                     id_pregunta INT AUTO_INCREMENT PRIMARY KEY,
                                     estado ENUM('Reportada', 'Aprobada', 'Deshabilitada') DEFAULT 'Reportada',
                                     comentario TEXT
);
--insert de ejemplo
INSERT INTO preguntas_reportadas (estado, comentario)
VALUES
    ('Reportada', 'La respuesta parece ser incorrecta'),

    ('Reportada', 'La pregunta fue revisada y es correcta'),

    ('Reportada', 'La pregunta fue deshabilitada por contenido inadecuado');


--Estado Reportada
ALTER TABLE preguntas
MODIFY estado_aprobacion ENUM('Aprobada', 'Rechazada', 'En Revisión', 'Reportada', 'Deshabilitada') NOT NULL DEFAULT 'En Revisión';

--Nivel de jugador
ALTER TABLE usuarios
    ADD COLUMN preguntas_respondidas_total INT DEFAULT 0,
ADD COLUMN preguntas_respondidas_correctas INT DEFAULT 0,
ADD COLUMN nivel_jugador ENUM('Facil', 'Medio', 'Dificil') DEFAULT 'Medio';

--Datos
-- Insertar registros adicionales en la tabla `partidas`
INSERT INTO `partidas` (`id_usuario`, `id_pregunta`, `horario_inicio`, `puntos_sumados`, `estado`) VALUES
                                                                                                       (2, 15, '2024-11-20 08:30:00', 50, 'finalizada'),
                                                                                                       (3, 12, '2024-11-21 09:00:00', 20, 'finalizada'),
                                                                                                       (4, 18, '2024-11-22 10:15:00', 10, 'finalizada'),
                                                                                                       (5, 25, '2024-11-22 11:30:00', 30, 'finalizada'),
                                                                                                       (2, 7, '2024-11-22 12:45:00', 40, 'finalizada'),
                                                                                                       (3, 20, '2024-11-23 13:00:00', 15, 'enCurso'),
                                                                                                       (4, 8, '2024-11-23 14:30:00', 25, 'finalizada'),
                                                                                                       (5, 3, '2024-11-24 15:45:00', 35, 'enCurso'),
                                                                                                       (2, 6, '2024-11-24 16:00:00', 45, 'finalizada'),
                                                                                                       (3, 19, '2024-11-24 16:15:00', 55, 'finalizada');

-- Insertar registros adicionales en la tabla `preguntas_reportadas`
INSERT INTO `preguntas_reportadas` (`id_pregunta`, `estado`, `comentario`) VALUES
                                                                               (11, 'Reportada', 'Opciones confusas'),
                                                                               (12, 'Aprobada', 'Revisada y aprobada'),
                                                                               (13, 'Deshabilitada', 'Contenido inapropiado'),
                                                                               (14, 'Reportada', 'Error en la categoría'),
                                                                               (15, 'Aprobada', 'Corregida correctamente'),
                                                                               (16, 'Deshabilitada', 'Pregunta duplicada'),
                                                                               (17, 'Reportada', 'Problemas de redacción'),
                                                                               (18, 'Aprobada', 'Verificada sin errores'),
                                                                               (19, 'Deshabilitada', 'Inexacta históricamente'),
                                                                               (20, 'Reportada', 'Problemas en las opciones');

-- Insertar registros adicionales en la tabla `preguntas_sugeridas`
INSERT INTO `preguntas_sugeridas` (`contenido`, `categoria`, `nivel_dificultad`, `opcion_a`, `opcion_b`, `opcion_c`, `opcion_d`, `respuesta_correcta`, `creada_por`, `estado`, `fecha_creacion`) VALUES
                                                                                                                                                                                                     ('¿Cuál es la capital de Alemania?', 'Geografía', 'medio', 'Berlín', 'Múnich', 'Hamburgo', 'Colonia', 'A', 3, 'Pendiente', NOW()),
                                                                                                                                                                                                     ('¿Qué elemento tiene el símbolo He?', 'Ciencia', 'facil', 'Helio', 'Hidrógeno', 'Hafnio', 'Helmio', 'A', 2, 'Pendiente', NOW()),
                                                                                                                                                                                                     ('¿En qué continente está Egipto?', 'Geografía', 'facil', 'Asia', 'África', 'Europa', 'América', 'B', 4, 'Pendiente', NOW()),
                                                                                                                                                                                                     ('¿Quién escribió "La Divina Comedia"?', 'Arte', 'dificil', 'Dante Alighieri', 'William Shakespeare', 'Johann Goethe', 'Homer', 'A', 1, 'Pendiente', NOW()),
                                                                                                                                                                                                     ('¿Qué año se firmó la Declaración de Independencia de EE.UU.?', 'Historia', 'medio', '1774', '1776', '1778', '1780', 'B', 5, 'Pendiente', NOW()),
                                                                                                                                                                                                     ('¿Qué gas respiramos principalmente?', 'Ciencia', 'facil', 'Oxígeno', 'Nitrógeno', 'Hidrógeno', 'Helio', 'B', 1, 'Pendiente', NOW()),
                                                                                                                                                                                                     ('¿Cuál es la moneda de Japón?', 'Geografía', 'facil', 'Won', 'Yen', 'Dólar', 'Euro', 'B', 2, 'Pendiente', NOW()),
                                                                                                                                                                                                     ('¿Quién pintó "La Noche Estrellada"?', 'Arte', 'medio', 'Van Gogh', 'Monet', 'Picasso', 'Cézanne', 'A', 3, 'Pendiente', NOW()),
                                                                                                                                                                                                     ('¿Qué molécula es conocida como el "ADN"?', 'Ciencia', 'facil', 'Ácido Deoxirribonucleico', 'Ácido Ribonucléico', 'Proteína', 'Enzima', 'A', 4, 'Pendiente', NOW()),
                                                                                                                                                                                                     ('¿Cuál es el país con más población?', 'Geografía', 'facil', 'India', 'China', 'EE.UU.', 'Indonesia', 'B', 5, 'Pendiente', NOW());

-- Insertar registros adicionales en la tabla `ranking`
INSERT INTO `ranking` (`id_usuario`, `puntaje_total`, `posicion`) VALUES
                                                                      (4, 120, 4),
                                                                      (5, 90, 5),
                                                                      (6, 85, 6),
                                                                      (7, 80, 7),
                                                                      (8, 75, 8),
                                                                      (9, 70, 9),
                                                                      (10, 65, 10),
                                                                      (11, 60, 11),
                                                                      (12, 55, 12),
                                                                      (13, 50, 13);

-- Insertar registros adicionales en la tabla `usuarios`
INSERT INTO `usuarios` (`nombre_usuario`, `contraseña`, `nombre_completo`, `email`, `anio_nacimiento`, `sexo`, `pais`, `ciudad`, `foto_perfil`, `puntaje_total`, `trampitas`, `tipo_usuario`, `fecha_creacion`, `validado`, `preguntas_respondidas_total`, `preguntas_respondidas_correctas`, `nivel_jugador`) VALUES
                                                                                                                                                                                                                                                                                                                   ('carlos99', '$2y$10$randomhash01', 'Carlos López', 'carlos.lopez@mail.com', 1999, 'Masculino', 'España', 'Madrid', 'carlos.jpg', 100, 2, 'jugador', CURDATE(), 1, 20, 15, 'Medio'),
                                                                                                                                                                                                                                                                                                                   ('ana23', '$2y$10$randomhash02', 'Ana García', 'ana.garcia@mail.com', 1990, 'Femenino', 'México', 'Guadalajara', 'ana.jpg', 80, 1, 'editor', CURDATE(), 1, 15, 10, 'Facil'),
                                                                                                                                                                                                                                                                                                                   ('luisito', '$2y$10$randomhash03', 'Luis Pérez', 'luis.perez@mail.com', 1985, 'Masculino', 'Argentina', 'Rosario', 'luis.jpg', 90, 0, 'jugador', CURDATE(), 0, 18, 12, 'Dificil'),
                                                                                                                                                                                                                                                                                                                   ('marta', '$2y$10$randomhash04', 'Marta Suárez', 'marta.suarez@mail.com', 2000, 'Femenino', 'Chile', 'Santiago', 'marta.jpg', 70, 3, 'jugador', CURDATE(), 1, 14, 9, 'Medio'),
                                                                                                                                                                                                                                                                                                                   ('jorge', '$2y$10$randomhash05', 'Jorge López', 'jorge.lopez@mail.com', 1995, 'Masculino', 'Perú', 'Lima', 'jorge.jpg', 85, 4, 'jugador', CURDATE(), 0, 16, 11, 'Facil'),
                                                                                                                                                                                                                                                                                                                   ('lucia', '$2y$10$randomhash06', 'Lucía Fernández', 'lucia.fernandez@mail.com', 1998, 'Femenino', 'Uruguay', 'Montevideo', 'lucia.jpg', 95, 2, 'jugador', CURDATE(), 1, 19, 13, 'Dificil'),
                                                                                                                                                                                                                                                                                                                   ('pedro', '$2y$10$randomhash07', 'Pedro Martínez', 'pedro.martinez@mail.com', 1987, 'Masculino', 'Paraguay', 'Asunción', 'pedro.jpg', 110, 5, 'editor', CURDATE(), 1, 25, 18, 'Medio'),
                                                                                                                                                                                                                                                                                                                   ('sofia', '$2y$10$randomhash08', 'Sofía González', 'sofia.gonzalez@mail.com', 1992, 'Femenino', 'Colombia', 'Bogotá', 'sofia.jpg', 105, 1, 'jugador', CURDATE(), 1, 22, 16, 'Medio'),
                                                                                                                                                                                                                                                                                                                   ('antonio', '$2y$10$randomhash09', 'Antonio Torres', 'antonio.torres@mail.com', 1989, 'Masculino', 'Venezuela', 'Caracas', 'antonio.jpg', 120, 0, 'administrador', CURDATE(), 1, 30, 20, 'Dificil'),
                                                                                                                                                                                                                                                                                                                   ('cristina', '$2y$10$randomhash10', 'Cristina López', 'cristina.lopez@mail.com', 1983, 'Femenino', 'Ecuador', 'Quito', 'cristina.jpg', 125, 3, 'jugador', CURDATE(), 0, 28, 22, 'Medio');

-- Insertar registros adicionales en la tabla `usuarios_preguntas`
INSERT INTO `usuarios_preguntas` (`id_usuario`, `id_pregunta`) VALUES
                                                                   (4, 15),
                                                                   (5, 12),
                                                                   (6, 8),
                                                                   (7, 19),
                                                                   (8, 6),
                                                                   (9, 10),
                                                                   (10, 14),
                                                                   (11, 3),
                                                                   (12, 17),
                                                                   (13, 4);
