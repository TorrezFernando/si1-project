<?php
$sql = "\n\n" . "DROP TABLE IF EXISTS bitacora;\n" .
"CREATE TABLE bitacora (\n" .
"  id_bitacora INT NOT NULL AUTO_INCREMENT,\n" .
"  id_user INT NOT NULL,\n" .
"  fecha_hora DATETIME NOT NULL,\n" .
"  accion VARCHAR(100) NOT NULL,\n" .
"  ip VARCHAR(45) NOT NULL,\n" .
"  PRIMARY KEY (id_bitacora),\n" .
"  CONSTRAINT fk_bitacora_usuario FOREIGN KEY (id_user) REFERENCES usuario (id_user)\n" .
");\n\n" .
"INSERT INTO rol (id_rol, tipo) VALUES (1, 'Admin');\n" .
"INSERT INTO usuario (id_user, username, password, id_rol) VALUES (1, 'admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);\n";

file_put_contents('colegio_mysql_limpio_v3.sql', $sql, FILE_APPEND);
echo "SQL appended successfully.\n";
