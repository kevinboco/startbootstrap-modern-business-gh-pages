<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    if (isset($_FILES['captura_pago']) && $_FILES['captura_pago']['error'] === 0) {
        $nombreArchivo = basename($_FILES['captura_pago']['name']);
        $rutaDestino = "informacion/" . $nombreArchivo;

        if (move_uploaded_file($_FILES['captura_pago']['tmp_name'], $rutaDestino)) {
            $stmt = $conn->prepare("UPDATE cuentas_cobro SET captura_pago_realizado = ? WHERE id = ?");
            $stmt->bind_param("si", $nombreArchivo, $id);
            $stmt->execute();
            $stmt->close();

            header("Location: ver_cuentas_cobro.php");
            exit;
        } else {
            echo "Error al mover el archivo.";
        }
    } else {
        echo "Error al subir el archivo.";
    }
} else {
    echo "Acceso no autorizado.";
}
?>
