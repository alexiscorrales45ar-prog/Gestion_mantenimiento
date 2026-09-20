<?php
try {
    // Conectamos a la base de datos en la carpeta principal
    $db = new PDO('sqlite:../database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Creamos la tabla
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        rol TEXT NOT NULL
    )";
    $db->exec($sql);

    // Insertamos el admin de prueba (ignoramos si ya existe)
    $db->exec("INSERT OR IGNORE INTO usuarios (usuario, password, rol) VALUES ('admin', '123456', 'administrador')");

    echo "<h1>¡Tabla de usuarios creada con éxito!</h1>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>