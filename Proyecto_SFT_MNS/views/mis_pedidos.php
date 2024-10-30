<?php
session_start();
require '../config/database.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre_usuario'];

// Obtener los pedidos del cliente
$usuario_id = $_SESSION['usuario_id'];
$query_pedidos = "SELECT numero_pedido, fecha_pedido, total_pedido, tipo_pago, estado_pedido
                  FROM pedidos
                  WHERE usuario_id = :usuario_id
                  ORDER BY fecha_pedido DESC";
$stmt_pedidos = $base->prepare($query_pedidos);
$stmt_pedidos->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_pedidos->execute();
$pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

// Filtro de búsqueda
$numero_pedido = isset($_GET['numero_pedido']) ? $_GET['numero_pedido'] : '';
$pedidos_filtrados = [];

if ($numero_pedido) {
    foreach ($pedidos as $pedido) {
        if (strpos($pedido['numero_pedido'], $numero_pedido) !== false) {
            $pedidos_filtrados[] = $pedido;
        }
    }
} else {
    $pedidos_filtrados = $pedidos;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Footwear Tenis MNS</title> 
    <link rel="stylesheet" href="../css/mis_pedidos.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>ㅤShopping Footwear Tenis MNS</h1> <br> 
            </div>
            
            <div class="search-bar">
            ㅤㅤㅤ<input type="text" id="search-bar" placeholder="Buscar productos...">ㅤ
                <a href="index_cliente.php"><button id="search-button">Buscar</button></a>
                <a href="carrito.php">
                    <div class="move-car">
                        <img src="../imagenes/carrito.png" alt="Carrito" width="30px" height="30px">
                    
                </a>
            </div>


            <div class="user-options">
                <span>Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></span>
                <a href="mi_perfil.php">Mi Perfil</a>
                <a href="soporte.php">Soporte</a>
                <a href="index_cliente.php">Productos</a>
            </div>ㅤ
            <div class="move-sesion">
                <form id="logout-form" method="POST" action="cerrar_sesion.php">
                    <button type="button" id="logout-button" class="buttonC">Cerrar sesión</button>
                </form>
            </div> <br> 
        </div>
    </header><br>

    <div class="Center"><h1>Mis Pedidos</h1></div> <br> <br> 


    <form method="GET" action="">
        <label for="numero_pedido">Buscar por Número de Pedido</label>ㅤ
        <input type="text" id="numero_pedido" name="numero_pedido" value="<?php echo htmlspecialchars($numero_pedido); ?>">
        <button type="submit">Buscar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Número de Pedido</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Tipo de Pago</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pedidos_filtrados)): ?>
                <tr>
                    <td colspan="6">No se encontraron pedidos.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($pedidos_filtrados as $pedido): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pedido['numero_pedido']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['fecha_pedido']); ?></td>
                        <td><?php echo "$" . htmlspecialchars($pedido['total_pedido']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['tipo_pago']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['estado_pedido']); ?></td>
                        <td><a href="factura.php?pedido_id=<?php echo htmlspecialchars($pedido['numero_pedido']); ?>">Ver Factura</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <form action="index_cliente.php">
        <button>Volver</button>
    </form>

    <footer>
        <div class="container">
            <p>&copy; 2024 Shopping Footwear Tenis MNS. Todos los derechos reservados.</p>
        </div>
    </footer>
    <script src="../js/pag_principal_cliente.js"></script>
    <?php if (isset($_GET['message'])): ?>
        <script>
            alert("<?php echo htmlspecialchars($_GET['message']); ?>");
        </script>
    <?php endif; ?>
</body>
</html>
