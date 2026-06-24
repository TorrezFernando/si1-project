-- Normaliza el usuario administrador para que el login funcione con:
-- usuario: admin
-- password: password
--
-- Ejecutar despues de importar la base de datos del proyecto.

START TRANSACTION;

SET @admin_password_hash = '$2y$12$9gW5hFaIs1nAx.Ng8.GNE.TMBx6Cpyo6uScNlfDKAfC7iOpGLCkkS';

-- Si ya existe el id 1, lo convertimos en el admin oficial.
UPDATE usuario
SET username = 'admin',
    password = @admin_password_hash,
    id_rol = 1,
    remember_token = NULL
WHERE id_user = 1;

-- Si no existe el id 1, lo creamos.
INSERT INTO usuario (id_user, username, password, id_rol, remember_token)
SELECT 1, 'admin', @admin_password_hash, 1, NULL
WHERE NOT EXISTS (
    SELECT 1
    FROM usuario
    WHERE id_user = 1
);

-- Si por una importacion previa existe otro "admin", le dejamos
-- el mismo acceso para evitar errores de login por registros duplicados.
UPDATE usuario
SET password = @admin_password_hash,
    id_rol = 1,
    remember_token = NULL
WHERE username = 'admin';

COMMIT;

-- Verificacion final.
SELECT id_user, username, id_rol
FROM usuario
WHERE username = 'admin' OR id_user = 1;
