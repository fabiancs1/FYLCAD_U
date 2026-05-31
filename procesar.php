<?php
/**
 * FYLCAD — Plataforma de Topografía Digital
 * Copyright (c) 2026 Fabian Eduardo Rodriguez Hernandez
 * Todos los derechos reservados.
 * Uso no autorizado prohibido.
 */


if (isset($_FILES['archivo'])) {

    $archivo = $_FILES['archivo']['tmp_name'];
    $puntos = [];

    if (($handle = fopen($archivo, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $puntos[] = [
                "x" => floatval($data[0]),
                "y" => floatval($data[1])
            ];
        }
        fclose($handle);
    }

    echo "<h2>Archivo Procesado</h2>";
    echo "Total de puntos: " . count($puntos);
    echo "<br><a href='proyecto.php'>Volver</a>";
}
