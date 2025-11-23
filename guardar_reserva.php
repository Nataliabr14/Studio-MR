<?php
$conexion = new mysqli("localhost", "root", "", "manuela_esthetic");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$nombre = $_POST['nombre'];
$telefono = $_POST['telefono'];
$servicio = $_POST['servicio'];
$modelo_pestanas = $_POST['modelo_pestanas'] ?? NULL;
$modelo_labios = $_POST['modelo_labios'] ?? NULL;
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];

// INSERTAR CLIENTE
$sqlCliente = "INSERT INTO clientes (nombre, telefono) VALUES ('$nombre', '$telefono')";
$conexion->query($sqlCliente);
$id_cliente = $conexion->insert_id;

// INSERTAR RESERVA
$sqlReserva = "INSERT INTO reservas (id_cliente, id_servicio, id_modelo_pestanas, id_modelo_labios, fecha, hora, estado)
VALUES ('$id_cliente', '$servicio', 
        " . ($modelo_pestanas ? "'$modelo_pestanas'" : "NULL") . ",
        " . ($modelo_labios ? "'$modelo_labios'" : "NULL") . ",
        '$fecha', '$hora', 'pendiente')";

if ($conexion->query($sqlReserva) === TRUE) {
    echo "<script>alert('¡Reserva registrada con éxito!');window.location='reservas.php';</script>";
} else {
    echo "Error al guardar la reserva: " . $conexion->error;
}

$conexion->close();
?>



