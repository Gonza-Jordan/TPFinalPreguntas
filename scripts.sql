CREATE DATABASE tpfinalpreguntas;

USE tpfinalpreguntas;
--Creo la tabla usuario
CREATE TABLE usuarios (
                          id_usuario INT AUTO_INCREMENT PRIMARY KEY,
                          nombre_usuario VARCHAR(50) NOT NULL,
                          contraseña VARCHAR(255) NOT NULL,
                          nombre_completo VARCHAR(100),
                          email VARCHAR(100) NOT NULL,
                          fecha_nacimiento INT,
                          sexo VARCHAR(20),
                          pais VARCHAR(50),
                          ciudad VARCHAR(50),
                          foto_perfil VARCHAR(255),
                          puntaje_total INT DEFAULT 0,
                          trampitas INT DEFAULT 0,
                          tipo_usuario VARCHAR(20) DEFAULT 'jugador',
                          fecha_creacion DATE DEFAULT CURRENT_DATE,
                          validado BOOLEAN DEFAULT FALSE
);

--insert de usuario
INSERT INTO usuarios (nombre_usuario, contraseña, nombre_completo, email, fecha_nacimiento, sexo, pais, ciudad, foto_perfil, puntaje_total, trampitas, tipo_usuario, validado)
VALUES ('jmartinez', '12345password', 'Juan Martinez', 'juan.martinez@example.com', 1990, 'Masculino', 'Argentina', 'Buenos Aires', 'foto_1.jpg', 150, 3, 'jugador', TRUE);

INSERT INTO usuarios (nombre_usuario, contraseña, nombre_completo, email, fecha_nacimiento, sexo, pais, ciudad, foto_perfil, puntaje_total, trampitas, tipo_usuario, validado)
VALUES ('mperez', 'securePass456', 'Maria Perez', 'maria.perez@example.com', 1985, 'Femenino', 'Mexico', 'Guadalajara', 'foto_2.jpg', 200, 5, 'editor', FALSE);

INSERT INTO usuarios (nombre_usuario, contraseña, nombre_completo, email, fecha_nacimiento, sexo, pais, ciudad, foto_perfil, puntaje_total, trampitas, tipo_usuario, validado)
VALUES ('lgomez', 'mypassword789', 'Luis Gomez', 'luis.gomez@example.com', 2000, 'Prefiero no cargarlo', 'España', 'Madrid', 'foto_3.jpg', 100, 1, 'jugador', TRUE);
