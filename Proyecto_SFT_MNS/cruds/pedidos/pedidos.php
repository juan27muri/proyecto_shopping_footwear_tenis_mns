<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
require '../../config/database.php';

// Paginación
$registrosPorPagina = 6;
$pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
$inicio = ($pagina - 1) * $registrosPorPagina;

// Filtro de búsqueda
$numeroPedidoFiltro = isset($_GET['numero_pedido']) ? $_GET['numero_pedido'] : '';

// Consulta para contar el número total de pedidos (con o sin filtro)
$sqlTotal = "SELECT COUNT(*) FROM pedidos WHERE numero_pedido LIKE :numero_pedido";
$stmtTotal = $base->prepare($sqlTotal);
$stmtTotal->execute([':numero_pedido' => '%' . $numeroPedidoFiltro . '%']);
$numFilas = $stmtTotal->fetchColumn();
$totalPaginas = ceil($numFilas / $registrosPorPagina);

// Obtener registros de la tabla (con o sin filtro)
$sqlPedidos = "SELECT p.*, u.nombre_usuario, u.apellido_usuario 
               FROM pedidos p 
               INNER JOIN usuarios u ON p.usuario_id = u.id_usuario 
               WHERE p.numero_pedido LIKE :numero_pedido 
               LIMIT $inicio, $registrosPorPagina";
$stmtPedidos = $base->prepare($sqlPedidos);
$stmtPedidos->execute([':numero_pedido' => '%' . $numeroPedidoFiltro . '%']);
$resultadoPedidos = $stmtPedidos->fetchAll(PDO::FETCH_OBJ);

// Actualizar estado del pedido
if (isset($_POST['actualiza'])) {
    $numeroPedido = $_POST['numero_pedido'];
    $estadoPedido = $_POST['estado_pedido'];
    $sqlUpdate = "UPDATE pedidos SET estado_pedido = :estado_pedido WHERE numero_pedido = :numero_pedido";
    $resultadoUpdate = $base->prepare($sqlUpdate);
    $resultadoUpdate->execute(array(":numero_pedido" => $numeroPedido, ":estado_pedido" => $estadoPedido));

    $_SESSION['message'] = "El estado del pedido $numeroPedido ha sido actualizado correctamente.";
    $_SESSION['message_type'] = 'success';
    header("Location: pedidos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <link rel="stylesheet" href="../../css/admin_dashboard.css">
    <link rel="icon" href="../../imagenes/favicon.png" type="image/x-icon">
</head>
<body>
    <header>
        <div class="admin-header">
            <div class="admin-logo">
                <img src="../../imagenes/usuario.png" alt="User Logo" width="50" height="50">
            </div>
            <div class="admin-name">
                <h2>Administrador: <?php echo htmlspecialchars($_SESSION['admin_nombre']) . ' ' . htmlspecialchars($_SESSION['admin_apellido']); ?></h2>
            </div>
            <button class="menu-button" onclick="toggleSidebar()">☰</button>
            <form id="logout-form" method="POST" action="../../views/cerrar_sesion.php">
                <button type="button" id="logout-button" class="buttonC">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <div class="admin-container">
        <div class="sidebar" id="sidebar">
            <a href="../../views/admin_dashboard.php">Inicio</a>
            <a href="../../cruds/roles.php" onclick="showSection('roles')">Crud Roles</a>
            <a href="../../cruds/departamentos.php" onclick="showSection('departamentos')">Crud Departamentos</a>
            <a href="../../cruds/ciudades.php" onclick="showSection('ciudades')">Crud Ciudades</a>
            <a href="../../cruds/categorias.php" onclick="showSection('categorias')">Crud Categorías</a>
            <a href="../../cruds/marcas.php" onclick="showSection('marcas')">Crud Marcas</a>
            <a href="../../cruds/productos/productos.php" onclick="showSection('productos')">Crud Productos</a>
            <a href="../../cruds/usuarios/usuarios.php" onclick="showSection('usuarios')">Crud Usuarios</a>
            <a href="../../cruds/pedidos/pedidos.php" onclick="showSection('pedidos')">Pedidos</a>
            <a href="../../cruds/soporte/soporte.php" onclick="showSection('soporte')">Soporte</a>
            <a href="../../views/reportes.php" onclick="showSection('reportes')">Reportes</a>

        </div>

        <main id="main-content">
            <div id="pedidos" class="section active">
                <h1><span class="color">Gestionar Pedidos</span></h1>
                <div class="crud-container">
                <form method="get" action="">
                    <input type="text" name="numero_pedido" placeholder="Buscar por número de pedido" value="<?php echo htmlspecialchars($numeroPedidoFiltro); ?>">
                    <button type="submit" class="button">Buscar</button>
                </form><br>
                    <table align="center" border="" bordercolor="orange" class="crud-table">
                        <tr>
                            <th>Número de Pedido</th>
                            <th>Fecha de Pedido</th>
                            <th>Total Pedido</th>
                            <th>Tipo de Pago</th>
                            <th>Estado Pedido</th>
                            <th>ID Usuario</th>
                            <th>Nombre Usuario</th>
                            <th>Apellido Usuario</th>
                            <th>Acciones</th>
                        </tr>
                        <tbody id="crud-table-body">
                            <?php foreach ($resultadoPedidos as $pedido) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($pedido->numero_pedido); ?></td>
                                    <td><?php echo htmlspecialchars($pedido->fecha_pedido); ?></td>
                                    <td><?php echo htmlspecialchars($pedido->total_pedido); ?></td>
                                    <td><?php echo htmlspecialchars($pedido->tipo_pago); ?></td>
                                    <td>
                                        <form method="post" action="">
                                            <input type="hidden" name="numero_pedido" value="<?php echo htmlspecialchars($pedido->numero_pedido); ?>">
                                            <select name="estado_pedido">
                                                <option value="Procesando" <?php echo $pedido->estado_pedido == 'Procesando' ? 'selected' : ''; ?>>Procesando</option>
                                                <option value="Enviado" <?php echo $pedido->estado_pedido == 'Enviado' ? 'selected' : ''; ?>>Enviado</option>
                                                <option value="Entregado" <?php echo $pedido->estado_pedido == 'Entregado' ? 'selected' : ''; ?>>Entregado</option>
                                                <option value="Cancelado" <?php echo $pedido->estado_pedido == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                            </select>
                                            <button type="submit" name="actualiza" class="button">Actualizar</button>
                                        </form>
                                    </td>
                                    <td><?php echo htmlspecialchars($pedido->usuario_id); ?></td>
                                    <td><?php echo htmlspecialchars($pedido->nombre_usuario); ?></td>
                                    <td><?php echo htmlspecialchars($pedido->apellido_usuario); ?></td>
                                    <td>
                                    <a href="factura_admin.php?numero_pedido=<?php echo htmlspecialchars($pedido->numero_pedido); ?>" class="button">Ver Factura</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <?php if ($pagina > 1) : ?>
                            <li><a href="?pagina=<?php echo $pagina - 1; ?>&numero_pedido=<?php echo htmlspecialchars($numeroPedidoFiltro); ?>" class="button">Anterior</a></li>
                        <?php endif; ?>

                        <?php if ($pagina < $totalPaginas) : ?>
                            <li><a href="?pagina=<?php echo $pagina + 1; ?>&numero_pedido=<?php echo htmlspecialchars($numeroPedidoFiltro); ?>" class="button">Siguiente</a></li>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php if (isset($_SESSION['message'])) : ?>
        <script>
            alert('<?php echo $_SESSION['message']; ?>');
        </script>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <script src="../../js/admin_Dashboard.js"></script>
</body>
</html>
