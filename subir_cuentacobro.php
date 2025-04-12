<?php
require_once 'db.php'; // conexión a la base de datos

function enviarNotificacionTelegram($mensaje) {
    $botToken = '7590591675:AAHiatr9TqbXkd4_7F7lQBure5n6U-0C14Y';
    $chatId = '6133806918'; // ID del usuario que recibe la notificación
    $url = "https://api.telegram.org/bot$botToken/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => $mensaje,
        'parse_mode' => 'HTML'
    ];

    $opciones = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type:application/x-www-form-urlencoded",
            'content' => http_build_query($data),
        ],
    ];
    $contexto = stream_context_create($opciones);
    file_get_contents($url, false, $contexto);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $nombre_viaje = $_POST['nombre_viaje'];

    if (isset($_FILES['cuenta_cobro']) && $_FILES['cuenta_cobro']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['cuenta_cobro'];
        $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid('cuenta_') . '.' . $ext;
        $ruta_destino = 'informacion/' . $nombre_archivo;

        if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
            $ruta_captura = null;

            if (isset($_FILES['captura_pago_realizado']) && $_FILES['captura_pago_realizado']['error'] === UPLOAD_ERR_OK) {
                $captura = $_FILES['captura_pago_realizado'];
                $ext_captura = pathinfo($captura['name'], PATHINFO_EXTENSION);
                $nombre_captura = uniqid('pago_') . '.' . $ext_captura;
                $ruta_captura = 'informacion/' . $nombre_captura;

                if (!move_uploaded_file($captura['tmp_name'], $ruta_captura)) {
                    echo "Error al mover la captura de pago.";
                    exit();
                }
            }

            // Guardamos en la base de datos
            $stmt = $conn->prepare("INSERT INTO cuentas_cobro (fecha, nombre_viaje, archivo_path, captura_pago_realizado) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fecha, $nombre_viaje, $ruta_destino, $ruta_captura);

            if ($stmt->execute()) {
                echo "Cuenta de cobro subida exitosamente. <a href='bienvenida.php'>Volver</a>";

                // Notificación Telegram
                $mensaje = "📄 Se ha subido una nueva cuenta de cobro:\n\n🗓 Fecha: $fecha\n✈️ Viaje: $nombre_viaje\n\nPor favor, revisa y procede con el pago.";
                enviarNotificacionTelegram($mensaje);
            } else {
                echo "Error al guardar en la base de datos: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error al mover el archivo de cuenta de cobro.";
        }
    } else {
        echo "Error al subir el archivo de cuenta de cobro.";
    }

    $conn->close();
} else {
    header("Location: formulario_cuentacobro.php");
    exit();
}
?>
