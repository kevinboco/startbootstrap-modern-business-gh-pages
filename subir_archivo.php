<?php
require_once 'db.php'; // Tu archivo de conexión

$carpetaDestino = 'informacion/';

// Verificar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_persona'];

    // Función para guardar el archivo y devolver la ruta
    function guardarArchivo($archivo, $carpeta) {
        $nombreTemporal = $_FILES[$archivo]['tmp_name'];
        $nombreOriginal = basename($_FILES[$archivo]['name']);
        $nombreFinal = uniqid() . '_' . $nombreOriginal;
        $rutaDestino = $carpeta . $nombreFinal;

        if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
            return $rutaDestino;
        } else {
            return null;
        }
    }

    // Guardar los archivos
    $rutaRUT = guardarArchivo('rut', $carpetaDestino);
    $rutaCertificado = guardarArchivo('certificado_bancario', $carpetaDestino);
    $rutaCedula = guardarArchivo('cedula', $carpetaDestino);

    if ($rutaRUT && $rutaCertificado && $rutaCedula) {
        // Guardar en la base de datos
        $stmt = $conn->prepare("INSERT INTO info_asociados (nombre_persona, rut_path, certificado_bancario_path, cedula_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $rutaRUT, $rutaCertificado, $rutaCedula);

        if ($stmt->execute()) {
            echo "Información subida correctamente. <a href='bienvenida.php'>Volver</a>";
        } else {
            echo "Error al guardar en la base de datos: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error al subir uno o más archivos. Intenta nuevamente.";
    }

    $conn->close();
} else {
    echo "Acceso no permitido.";
}
?>
