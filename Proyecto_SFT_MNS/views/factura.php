<?php
session_start();
require '../config/database.php';
require('../fpdf/fpdf.php'); // Incluye la biblioteca FPDF

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del pedido desde la URL
if (!isset($_GET['pedido_id']) || !is_numeric($_GET['pedido_id'])) {
    header("Location: realizar_pedido.php");
    exit();
}

$pedido_id = (int) $_GET['pedido_id'];

// Obtener los detalles del pedido
$query_pedido = "SELECT * FROM pedidos WHERE numero_pedido = :pedido_id";
$stmt_pedido = $base->prepare($query_pedido);
$stmt_pedido->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
$stmt_pedido->execute();
$pedido = $stmt_pedido->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: realizar_pedido.php");
    exit();
}

// Obtener los detalles del pedido
$query_detalles = "SELECT p.nombre_producto, dp.cantidad, dp.talla, dp.precio, (dp.cantidad * dp.precio) AS total_producto
                    FROM detalles_pedido dp
                    JOIN productos p ON dp.producto_id = p.codigo_producto
                    WHERE dp.pedido_id = :pedido_id";
$stmt_detalles = $base->prepare($query_detalles);
$stmt_detalles->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
$stmt_detalles->execute();
$detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

// Obtener los datos del cliente
$query_cliente = "SELECT u.id_usuario, u.nombre_usuario, u.apellido_usuario, u.direccion_usuario, u.telefono_usuario, u.email_usuario, 
                    c.nombre_ciudad, d.nombre_departamento
                    FROM usuarios u
                    INNER JOIN ciudades c ON u.ciudad_id = c.id_ciudad
                    INNER JOIN departamentos d ON u.departamento_id = d.id_departamento
                    WHERE u.id_usuario = :usuario_id";
$stmt_cliente = $base->prepare($query_cliente);
$stmt_cliente->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
$stmt_cliente->execute();
$cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();

// Agregar la imagen de fondo
$pdf->Image('../imagenes/icono.png', 0, 0, 210, 297, 'PNG'); // Ajusta el tamaño según sea necesario

// Estilo del título
$pdf->SetFont('Arial', 'B', 18); // Tamaño más grande para el título
$pdf->SetTextColor(30, 126, 52); // Color verde
$pdf->Cell(0, 15, 'Recibo del Pedido', 0, 1, 'C');
$pdf->Ln(10);

// Datos del Pedido
$pdf->SetFont('Arial', 'B', 14); // Tamaño más grande para las secciones en negrita
$pdf->SetTextColor(30, 126, 52); // Color verde
$pdf->Cell(0, 10, 'Datos del Pedido', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(30, 126, 52); // Color negro
$pdf->Cell(0, 10, str_repeat('_', 140), 0, 1, 'C'); // Línea decorativa centrada
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Numero de Pedido: ' . $pedido['numero_pedido'], 0, 1);
$pdf->Cell(0, 10, 'Fecha de Realizacion: ' . $pedido['fecha_pedido'], 0, 1);
$pdf->Cell(0, 10, 'Tipo de Pago: ' . $pedido['tipo_pago'], 0, 1);
$pdf->Cell(0, 10, 'Estado del Pedido: ' . $pedido['estado_pedido'], 0, 1);
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 14); // Tamaño más grande para las secciones en negrita
$pdf->SetTextColor(30, 126, 52); // Color verde
$pdf->Cell(0, 10, 'Detalles del Pedido', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(30, 126, 52); // Color negro
$pdf->Cell(0, 10, str_repeat('_', 140), 0, 1, 'C'); // Línea decorativa centrada

// Detalles del Pedido
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(30, 126, 52); // Color verde para el fondo
$pdf->SetTextColor(255, 255, 255); // Color blanco para el texto
$pdf->Cell(60, 10, 'Producto', 1, 0, 'C', true); // Ajusta el ancho de las columnas
$pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Talla', 1, 0, 'C', true); // Columna para la talla
$pdf->Cell(40, 10, 'Precio Unitario', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Total', 1, 1, 'C', true);
$pdf->SetTextColor(0, 0, 0); // Color negro para el texto de la tabla

$total_pedido = 0;
foreach ($detalles as $detalle) {
    $pdf->SetFont('Arial', '', 10); // Tamaño más pequeño para el contenido de la tabla
    $pdf->MultiCell(60, 10, $detalle['nombre_producto'], 1, 'L'); // Ajusta el texto al ancho de la celda
    $pdf->SetXY($pdf->GetX() + 60, $pdf->GetY() - 10); // Mueve el cursor para la siguiente columna
    $pdf->Cell(30, 10, $detalle['cantidad'], 1);
    $pdf->Cell(30, 10, $detalle['talla'], 1); // Mostrar la talla
    $pdf->Cell(40, 10, '$' . number_format($detalle['precio'], 2), 1);
    $pdf->Cell(40, 10, '$' . number_format($detalle['total_producto'], 2), 1);
    $pdf->Ln();
    $total_pedido += $detalle['total_producto'];
}
$pdf->Cell(160, 10, 'Total a Pagar', 1);
$pdf->Cell(40, 10, '$' . number_format($total_pedido, 2), 1);
$pdf->Ln(20);

// Datos del Cliente
$pdf->SetFont('Arial', 'B', 14); // Tamaño más grande para las secciones en negrita
$pdf->SetTextColor(30, 126, 52); // Color verde
$pdf->Cell(0, 10, 'Datos del Cliente', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(30, 126, 52); 
$pdf->Cell(0, 10, str_repeat('_', 140), 0, 1, 'C'); // Línea decorativa centrada
$pdf->SetTextColor(0, 0, 0); // Color negro
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Identificacion: ' . $cliente['id_usuario'], 0, 1);
$pdf->Cell(0, 10, 'Nombre: ' . $cliente['nombre_usuario'] . ' ' . $cliente['apellido_usuario'], 0, 1);
$pdf->Cell(0, 10, 'Direccion: ' . $cliente['direccion_usuario'], 0, 1);
$pdf->Cell(0, 10, 'Telefono: ' . $cliente['telefono_usuario'], 0, 1);
$pdf->Cell(0, 10, 'Correo Electronico: ' . $cliente['email_usuario'], 0, 1);
$pdf->Cell(0, 10, 'Ciudad: ' . $cliente['nombre_ciudad'], 0, 1);
$pdf->Cell(0, 10, 'Departamento: ' . $cliente['nombre_departamento'], 0, 1);

// Salida del PDF para visualizar en el navegador
$pdf->Output('I', 'Confirmacion_Pedido_' . $pedido['numero_pedido'] . '.pdf');
?>
