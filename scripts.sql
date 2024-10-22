CREATE DATABASE tpfinalpreguntas;

USE tpfinalpreguntas;
--Creo la tabla usuario
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
                          token_activacion VARCHAR(32);
);

--insert de usuario
INSERT INTO usuarios (nombre_usuario, contraseña, nombre_completo, email, anio_nacimiento, sexo, pais, ciudad, foto_perfil, puntaje_total, trampitas, tipo_usuario, validado)
VALUES ('jmartinez', '$2y$10$zhmzgVp2Ud/5VQ5DWBAnFugtpa2rkED1MG.w1BlcSaS.J6JTanZbq', 'Juan Martinez', 'juan.martinez@example.com', 1990, 'Masculino', 'Argentina', 'Buenos Aires', 'foto_1.jpg', 150, 3, 'jugador', TRUE);

INSERT INTO usuarios (nombre_usuario, contraseña, nombre_completo, email, anio_nacimiento, sexo, pais, ciudad, foto_perfil, puntaje_total, trampitas, tipo_usuario, validado)
VALUES ('mperez', '$2y$10$zhmzgVp2Ud/5VQ5DWBAnFugtpa2rkED1MG.w1BlcSaS.J6JTanZbq', 'Maria Perez', 'maria.perez@example.com', 1985, 'Femenino', 'Mexico', 'Guadalajara', 'foto_2.jpg', 200, 5, 'editor', FALSE);

INSERT INTO usuarios (nombre_usuario, contraseña, nombre_completo, email, anio_nacimiento, sexo, pais, ciudad, foto_perfil, puntaje_total, trampitas, tipo_usuario, validado)
VALUES ('lgomez', '$2y$10$zhmzgVp2Ud/5VQ5DWBAnFugtpa2rkED1MG.w1BlcSaS.J6JTanZbq', 'Luis Gomez', 'luis.gomez@example.com', 2000, 'Prefiero no cargarlo', 'España', 'Madrid', 'foto_3.jpg', 100, 1, 'jugador', TRUE);
