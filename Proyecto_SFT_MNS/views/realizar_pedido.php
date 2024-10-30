<?php
session_start();
require '../config/database.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// AJAX handler para cargar ciudades
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_request'])) {
    $departamento_id = $_POST['departamento_id'];

    $sql = "SELECT id_ciudad, nombre_ciudad FROM ciudades WHERE departamento_id = :departamento_id";
    $stmt = $base->prepare($sql);
    $stmt->execute([':departamento_id' => $departamento_id]);

    $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ciudades);
    exit();
}

// Obtener los datos del cliente
$query = "SELECT id_usuario, nombre_usuario, apellido_usuario, direccion_usuario, telefono_usuario, email_usuario, ciudad_id, departamento_id
          FROM usuarios
          WHERE id_usuario = :usuario_id";
$stmt = $base->prepare($query);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener departamentos para el select
$query_departamentos = "SELECT id_departamento, nombre_departamento FROM departamentos";
$stmt_departamentos = $base->prepare($query_departamentos);
$stmt_departamentos->execute();
$departamentos = $stmt_departamentos->fetchAll(PDO::FETCH_ASSOC);

// Actualizar datos del cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_datos'])) {
    $direccion = $_POST['direccion_usuario'];
    $telefono = $_POST['telefono_usuario'];
    $email = $_POST['email_usuario'];
    $ciudad_id = $_POST['ciudad_id'];
    $departamento_id = $_POST['departamento_id'];

    $query_update = "UPDATE usuarios 
                     SET direccion_usuario = :direccion, telefono_usuario = :telefono, email_usuario = :email, 
                         ciudad_id = :ciudad_id, departamento_id = :departamento_id
                     WHERE id_usuario = :usuario_id";
    $stmt_update = $base->prepare($query_update);
    $stmt_update->execute([
        ':direccion' => $direccion,
        ':telefono' => $telefono,
        ':email' => $email,
        ':ciudad_id' => $ciudad_id,
        ':departamento_id' => $departamento_id,
        ':usuario_id' => $usuario_id
    ]);

    header("Location: realizar_pedido.php");
    exit();
}

// Procesar el pago y crear el pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['metodo_pago'])) {
    $tipo_pago = $_POST['metodo_pago'];
    $estado_pedido = "Procesando";

    // Obtener el carrito del usuario
    $query_carrito = "SELECT id_carrito FROM carritos WHERE usuario_id = :usuario_id";
    $stmt_carrito = $base->prepare($query_carrito);
    $stmt_carrito->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_carrito->execute();
    $carrito = $stmt_carrito->fetch(PDO::FETCH_ASSOC);

    // Insertar el pedido en la tabla pedidos
    $query_pedido = "INSERT INTO pedidos (usuario_id, fecha_pedido, tipo_pago, estado_pedido, total_pedido) VALUES (:usuario_id, NOW(), :tipo_pago, :estado_pedido, 0)";
    $stmt_pedido = $base->prepare($query_pedido);
    $stmt_pedido->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_pedido->bindParam(':tipo_pago', $tipo_pago, PDO::PARAM_STR);
    $stmt_pedido->bindParam(':estado_pedido', $estado_pedido, PDO::PARAM_STR);
    $stmt_pedido->execute();

    $pedido_id = $base->lastInsertId();

    // Obtener los detalles del carrito, incluyendo la talla
$query_detalles_carrito = "SELECT producto_id, cantidad, talla FROM detalles_carrito WHERE carrito_id = :carrito_id";
$stmt_detalles_carrito = $base->prepare($query_detalles_carrito);
$stmt_detalles_carrito->bindParam(':carrito_id', $carrito['id_carrito'], PDO::PARAM_INT);
$stmt_detalles_carrito->execute();
$detalles_carrito = $stmt_detalles_carrito->fetchAll(PDO::FETCH_ASSOC);

$total_pedido = 0;

foreach ($detalles_carrito as $detalle) {
    // Obtener el precio del producto
    $query_producto = "SELECT precio_producto FROM productos WHERE codigo_producto = :producto_id";
    $stmt_producto = $base->prepare($query_producto);
    $stmt_producto->bindParam(':producto_id', $detalle['producto_id'], PDO::PARAM_STR);
    $stmt_producto->execute();
    $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);

    $precio_producto = $producto['precio_producto'];
    $total_producto = $detalle['cantidad'] * $precio_producto;
    $total_pedido += $total_producto;

    // Insertar el detalle del pedido, incluyendo la talla
    $query_detalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, talla, precio) 
                      VALUES (:pedido_id, :producto_id, :cantidad, :talla, :precio)";
    $stmt_detalle = $base->prepare($query_detalle);
    $stmt_detalle->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt_detalle->bindParam(':producto_id', $detalle['producto_id'], PDO::PARAM_STR);
    $stmt_detalle->bindParam(':cantidad', $detalle['cantidad'], PDO::PARAM_INT);
    $stmt_detalle->bindParam(':talla', $detalle['talla'], PDO::PARAM_STR); // Aquí se usa la talla del detalle del carrito
    $stmt_detalle->bindParam(':precio', $precio_producto, PDO::PARAM_STR);
    $stmt_detalle->execute();
}

    // Actualizar el stock de los productos
    foreach ($detalles_carrito as $detalle) {
        $query_actualizar_stock = "UPDATE productos 
                                   SET stock = stock - :cantidad
                                   WHERE codigo_producto = :producto_id";
        $stmt_actualizar_stock = $base->prepare($query_actualizar_stock);
        $stmt_actualizar_stock->bindParam(':cantidad', $detalle['cantidad'], PDO::PARAM_INT);
        $stmt_actualizar_stock->bindParam(':producto_id', $detalle['producto_id'], PDO::PARAM_INT);
        $stmt_actualizar_stock->execute();
    }

    // Actualizar el total del pedido en la tabla pedidos
    $query_update_pedido = "UPDATE pedidos SET total_pedido = :total_pedido WHERE numero_pedido = :pedido_id";
    $stmt_update_pedido = $base->prepare($query_update_pedido);
    $stmt_update_pedido->bindParam(':total_pedido', $total_pedido, PDO::PARAM_STR);
    $stmt_update_pedido->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
    $stmt_update_pedido->execute();

    // Limpiar el carrito del usuario
    $query_clear_cart = "DELETE FROM detalles_carrito WHERE carrito_id = :carrito_id";
    $stmt_clear_cart = $base->prepare($query_clear_cart);
    $stmt_clear_cart->bindParam(':carrito_id', $carrito['id_carrito'], PDO::PARAM_INT);
    $stmt_clear_cart->execute();

    header("Location: confirmacion_pedido.php?pedido_id=" . $pedido_id);
    exit();
}

// Modificar la consulta para incluir la talla
$query_productos_carrito = "SELECT p.codigo_producto, p.nombre_producto, p.precio_producto, dc.cantidad, dc.talla 
                            FROM detalles_carrito dc 
                            INNER JOIN productos p ON dc.producto_id = p.codigo_producto
                            WHERE dc.carrito_id = (
                                SELECT id_carrito 
                                FROM carritos 
                                WHERE usuario_id = :usuario_id
                            )";
$stmt_productos_carrito = $base->prepare($query_productos_carrito);
$stmt_productos_carrito->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_productos_carrito->execute();
$productos_carrito = $stmt_productos_carrito->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pedido</title>
    <link rel="stylesheet" href="../css/pedido1.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
</head>
<body>
    <header>Realizar Pedido
    <a href="carrito.php">
        <div class="move-car">
            <img src="../imagenes/carrito.png" alt="Carrito" width="30px" height="30px">             
    </a>


    </header>
    

    <div class="container">
        <!-- Datos del Cliente -->
        <div class="form-container">
            <h2>Identificación: <?php echo htmlspecialchars($usuario['id_usuario']); ?></h2>
            <h2>Nombre: <?php echo htmlspecialchars($usuario['nombre_usuario']); ?></h2>
            <h2>Apellido: <?php echo htmlspecialchars($usuario['apellido_usuario']); ?></h2>

            <h2>Datos Editables</h2>
            <form method="POST">
                <label for="email_usuario">Correo Electrónico:</label>
                <input type="email" id="email_usuario" name="email_usuario" value="<?php echo htmlspecialchars($usuario['email_usuario']); ?>" required>
                <br>
                <label for="direccion_usuario">Dirección:</label>
                <input type="text" id="direccion_usuario" name="direccion_usuario" value="<?php echo htmlspecialchars($usuario['direccion_usuario']); ?>" required>
                <br>
                <label for="telefono_usuario">Teléfono:</label>
                <input type="text" id="telefono_usuario" name="telefono_usuario" value="<?php echo htmlspecialchars($usuario['telefono_usuario']); ?>" required>
                <br>
                <label for="departamento_id">Departamento:</label>
                <select id="departamento_id" name="departamento_id" required>
                    <?php foreach ($departamentos as $departamento): ?>
                        <option value="<?php echo htmlspecialchars($departamento['id_departamento']); ?>" <?php if ($departamento['id_departamento'] == $usuario['departamento_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($departamento['nombre_departamento']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br>
                <label for="ciudad_id">Ciudad:</label>
                <select id="ciudad_id" name="ciudad_id" required>
                    <!-- Las opciones de ciudades se cargarán mediante JavaScript -->
                </select>
                <br>
                <button type="submit" name="actualizar_datos">Actualizar Datos</button>
            </form>
        </div>

        <!-- Método de Pago -->
        <div class="form-container">
            <h2>Productos en el Carrito</h2>
            <div class="table-container">
            <table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Talla</th> <!-- Nueva columna para la talla -->
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php $total_carrito = 0; ?>
        <?php foreach ($productos_carrito as $producto): ?>
            <?php $subtotal = $producto['precio_producto'] * $producto['cantidad']; ?>
            <tr>
                <td><?php echo htmlspecialchars($producto['nombre_producto']); ?></td>
                <td><?php echo number_format($producto['precio_producto'], 2); ?> COP</td>
                <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                <td><?php echo htmlspecialchars($producto['talla']); ?></td> <!-- Mostrar la talla -->
                <td><?php echo number_format($subtotal, 2); ?> COP</td>
            </tr>
            <?php $total_carrito += $subtotal; ?>
        <?php endforeach; ?>
    </tbody>
</table>
                <p class="total"><strong>Total Carrito:</strong> <?php echo number_format($total_carrito, 2); ?> COP</p>
            </div>

            <!-- Formulario de Pago -->
            <form method="POST">
                <label for="metodo_pago">Método de Pago:</label>
                <select id="metodo_pago" name="metodo_pago" required>
                    <option value="Contraentrega">Contraentrega</option>
                </select>
                <br>
                <button type="submit" name="confirmar_pedido">Confirmar Pedido</button>
            </form>
        </div>
    </div>

    <script>
        // Cargar ciudades según el departamento seleccionado
        document.getElementById('departamento_id').addEventListener('change', function() {
            var departamento_id = this.value;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'realizar_pedido.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status == 200) {
                    var ciudades = JSON.parse(this.responseText);
                    var ciudadSelect = document.getElementById('ciudad_id');
                    ciudadSelect.innerHTML = '';
                    ciudades.forEach(function(ciudad) {
                        var option = document.createElement('option');
                        option.value = ciudad.id_ciudad;
                        option.textContent = ciudad.nombre_ciudad;
                        ciudadSelect.appendChild(option);
                    });
                }
            };
            xhr.send('ajax_request=true&departamento_id=' + departamento_id);
        });

        // Establecer la ciudad seleccionada actual
        document.addEventListener('DOMContentLoaded', function() {
            var ciudad_id = <?php echo json_encode($usuario['ciudad_id']); ?>;
            if (ciudad_id) {
                document.getElementById('ciudad_id').value = ciudad_id;
            }
        });
    </script>
</body>
</html>
