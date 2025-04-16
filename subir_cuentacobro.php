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

    // Guardar archivo cuenta_empresa
    $cuenta_empresa_path = null;
    if (isset($_FILES['cuenta_empresa']) && $_FILES['cuenta_empresa']['error'] === UPLOAD_ERR_OK) {
        $archivo_empresa = $_FILES['cuenta_empresa'];
        $ext_empresa = pathinfo($archivo_empresa['name'], PATHINFO_EXTENSION);
        $nombre_archivo_empresa = uniqid('empresa_') . '.' . $ext_empresa;
        $cuenta_empresa_path = 'informacion/' . $nombre_archivo_empresa;
        move_uploaded_file($archivo_empresa['tmp_name'], $cuenta_empresa_path);
    }

    // Guardar archivo cuenta_cobro
    $cuenta_cobro_path = null;
    if (isset($_FILES['cuenta_cobro']) && $_FILES['cuenta_cobro']['error'] === UPLOAD_ERR_OK) {
        $archivo_cobro = $_FILES['cuenta_cobro'];
        $ext_cobro = pathinfo($archivo_cobro['name'], PATHINFO_EXTENSION);
        $nombre_archivo_cobro = uniqid('consolidado_') . '.' . $ext_cobro;
        $cuenta_cobro_path = 'informacion/' . $nombre_archivo_cobro;
        move_uploaded_file($archivo_cobro['tmp_name'], $cuenta_cobro_path);
    }

    // Guardar archivos de cuentas_trabajadores[]
    $trabajadores_files = [];
    if (isset($_FILES['cuentas_trabajadores']) && count($_FILES['cuentas_trabajadores']['name']) > 0) {
        foreach ($_FILES['cuentas_trabajadores']['name'] as $key => $filename) {
            $file_path = 'informacion/' . basename($filename);
            move_uploaded_file($_FILES['cuentas_trabajadores']['tmp_name'][$key], $file_path);
            $trabajadores_files[] = $file_path;
        }
    }

    // Guardar archivo captura_pago_realizado
    $captura_pago_path = null;
    if (isset($_FILES['captura_pago_realizado']) && $_FILES['captura_pago_realizado']['error'] === UPLOAD_ERR_OK) {
        $captura = $_FILES['captura_pago_realizado'];
        $ext_captura = pathinfo($captura['name'], PATHINFO_EXTENSION);
        $nombre_captura = uniqid('pago_') . '.' . $ext_captura;
        $captura_pago_path = 'informacion/' . $nombre_captura;
        move_uploaded_file($captura['tmp_name'], $captura_pago_path);
    }

    // Guardar los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO cuentas_cobro (fecha, nombre_viaje, cuenta_empresa, cuenta_cobro, captura_pago_realizado) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fecha, $nombre_viaje, $cuenta_empresa_path, $cuenta_cobro_path, $captura_pago_path);

    if ($stmt->execute()) {
        // Obtener el ID de la cuenta de cobro recién insertada
        $cuenta_cobro_id = $stmt->insert_id;

        // Insertar los archivos de los trabajadores en la tabla intermedia
        if (!empty($trabajadores_files)) {
            foreach ($trabajadores_files as $file_path) {
                $stmt_trabajadores = $conn->prepare("INSERT INTO cuentas_trabajadores (cuenta_cobro_id, archivo) VALUES (?, ?)");
                $stmt_trabajadores->bind_param('is', $cuenta_cobro_id, $file_path);
                $stmt_trabajadores->execute();
            }
        }

        echo "Cuenta de cobro subida exitosamente. <a href='bienvenida.php'>Volver</a>";

        // Notificación Telegram
        $mensaje = "📄 Se ha subido una nueva cuenta de cobro:\n\n🗓 Fecha: $fecha\n✈️ Viaje: $nombre_viaje\n\nPor favor, revisa y procede con el pago.";
        enviarNotificacionTelegram($mensaje);
    } else {
        echo "Error al guardar en la base de datos: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: formulario_cuentacobro.php");
    exit();
}
?>
