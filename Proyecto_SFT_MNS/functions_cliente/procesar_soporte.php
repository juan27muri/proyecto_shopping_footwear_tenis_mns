<?php
session_start();
require '../config/database.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener los datos del formulario
$tipo_soporte = $_POST['tipo_soporte'];
$descripcion_soporte = $_POST['descripcion_soporte'];
$usuario_id = $_SESSION['usuario_id'];

// Insertar el soporte en la base de datos
$query = "INSERT INTO soporte (usuario_id, tipo_soporte, descripcion_soporte, fecha_soporte, estado_soporte) 
          VALUES (:usuario_id, :tipo_soporte, :descripcion_soporte, NOW(), 'Pendiente')";

$stmt = $base->prepare($query);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->bindParam(':tipo_soporte', $tipo_soporte, PDO::PARAM_STR);
$stmt->bindParam(':descripcion_soporte', $descripcion_soporte, PDO::PARAM_STR);
$stmt->execute();

// Redirigir al usuario a la página de soporte con un mensaje de éxito
header("Location: ../views/soporte.php?mensaje=Soporte%20enviado%20con%20éxito");
exit();

