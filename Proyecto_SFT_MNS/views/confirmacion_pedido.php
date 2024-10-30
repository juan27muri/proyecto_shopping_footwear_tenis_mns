<?php
session_start();
require '../config/database.php';

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
$query_detalles = "SELECT p.nombre_producto, dp.cantidad, dp.precio, dp.talla, (dp.cantidad * dp.precio) AS total_producto
                   FROM detalles_pedido dp
                   JOIN productos p ON dp.producto_id = p.codigo_producto
                   WHERE dp.pedido_id = :pedido_id";
$stmt_detalles = $base->prepare($query_detalles);
$stmt_detalles->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
$stmt_detalles->execute();
$detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Pedido</title>
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/confirmacion_pedido.css"> <!-- Enlaza aquí tu archivo CSS -->
</head>
<body>
    <h1>Confirmación de Pedido</h1>

    <h2>Datos del Pedido</h2>
    <p><strong>Número de Pedido:</strong> <?php echo htmlspecialchars($pedido['numero_pedido']); ?></p>
    <p><strong>Fecha de Realización:</strong> <?php echo htmlspecialchars($pedido['fecha_pedido']); ?></p>
    <p><strong>Tipo de Pago:</strong> <?php echo htmlspecialchars($pedido['tipo_pago']); ?></p>
    <p><strong>Estado del Pedido:</strong> <?php echo htmlspecialchars($pedido['estado_pedido']); ?></p>

    <h2>Detalles del Pedido</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Talla</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_pedido = 0;
            foreach ($detalles as $detalle) {
                $total_producto = $detalle['total_producto'];
                $total_pedido += $total_producto;
                echo "<tr>
                        <td>{$detalle['nombre_producto']}</td>
                        <td>{$detalle['talla']}</td>
                        <td>{$detalle['cantidad']}</td>
                        <td>\${$detalle['precio']}</td>
                        <td>\${$total_producto}</td>
                      </tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total a Pagar</td>
                <td><?php echo "\${$total_pedido}"; ?></td>
            </tr>
        </tfoot>
    </table>

    <h2>Datos del Cliente</h2>
    <?php
    // Obtener los datos del cliente, incluyendo ciudad y departamento
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
    ?>
    <p><strong>Identificacion:</strong> <?php echo htmlspecialchars($cliente['id_usuario']); ?></p>
    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($cliente['nombre_usuario']); ?> <?php echo htmlspecialchars($cliente['apellido_usuario']); ?></p>
    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($cliente['direccion_usuario']); ?></p>
    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente['telefono_usuario']); ?></p>
    <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($cliente['email_usuario']); ?></p>
    <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($cliente['nombre_ciudad']); ?></p>
    <p><strong>Departamento:</strong> <?php echo htmlspecialchars($cliente['nombre_departamento']); ?></p>

    <form action="index_cliente.php">
        <button type="submit">Volver</button>
    </form> <br>
    <a href="factura.php?pedido_id=<?php echo htmlspecialchars($pedido['numero_pedido']); ?>" class="boton-enlace">Ver Factura</a>
</body>
</html>
