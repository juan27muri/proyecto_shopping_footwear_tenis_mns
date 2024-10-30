<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}

require '../../config/database.php';

// Paginación
$registrosPorPagina = 6;
if (isset($_GET["pagina"])) {
    if ($_GET["pagina"] == 1) {
        header("Location: productos.php");
    } else {
        $pagina = $_GET["pagina"];
    }
} else {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;
$sqlTotal = "SELECT COUNT(*) FROM productos";
$resultadoTotal = $base->prepare($sqlTotal);
$resultadoTotal->execute();
$numFilas = $resultadoTotal->fetchColumn();
$totalPaginas = ceil($numFilas / $registrosPorPagina);

// Filtro de búsqueda
$filtro = '';
if (isset($_GET['buscar'])) {
    $buscar = $_GET['buscar'];
    $filtro = "WHERE p.codigo_producto LIKE :buscar OR p.nombre_producto LIKE :buscar";
}

// Contar total de registros filtrados
$sqlTotal = "SELECT COUNT(*) FROM productos p $filtro";
$resultadoTotal = $base->prepare($sqlTotal);
if ($filtro) {
    $resultadoTotal->execute(array(':buscar' => "%$buscar%"));
} else {
    $resultadoTotal->execute();
}
$numFilas = $resultadoTotal->fetchColumn();
$totalPaginas = ceil($numFilas / $registrosPorPagina);

// Obtener registros de la tabla productos con INNER JOIN y filtro
$sqlProductos = "SELECT p.*, c.nombre_categoria, m.nombre_marca
                 FROM productos p
                 INNER JOIN categorías c ON p.categoria_id = c.id_categoria
                 INNER JOIN marcas m ON p.marca_id = m.id_marca
                 $filtro
                 LIMIT $inicio, $registrosPorPagina";
$resultadoProductos = $base->prepare($sqlProductos);
if ($filtro) {
    $resultadoProductos->execute(array(':buscar' => "%$buscar%"));
} else {
    $resultadoProductos->execute();
}
$productos = $resultadoProductos->fetchAll(PDO::FETCH_OBJ);

// Obtener categorías y marcas para los selects
$sqlCategorias = "SELECT * FROM categorías";
$resultadoCategorias = $base->query($sqlCategorias)->fetchAll(PDO::FETCH_OBJ);

$sqlMarcas = "SELECT * FROM marcas";
$resultadoMarcas = $base->query($sqlMarcas)->fetchAll(PDO::FETCH_OBJ);



// Eliminar Producto - Parte 1: Mostrar Formulario
if (isset($_GET['eliminar'])) {
    $codigoProductoEliminar = $_GET['eliminar'];
}

// Confirmar y realizar eliminación - Parte 2: Verificar Contraseña
if (isset($_POST['verificar_contraseña'])) {
    $adminPassword = $_POST['admin_password'];
    $codigoProductoEliminar = $_POST['codigo_producto_eliminar'];

    // Verificar la contraseña del administrador
    $sqlVerificarAdmin = "SELECT * FROM usuarios WHERE id_usuario = :admin_id";
    $stmtVerificarAdmin = $base->prepare($sqlVerificarAdmin);
    $stmtVerificarAdmin->execute(array(":admin_id" => $_SESSION['admin_id']));
    $admin = $stmtVerificarAdmin->fetch(PDO::FETCH_OBJ);

    // Comparar la contraseña encriptada
    if ($admin && password_verify($adminPassword, $admin->contraseña_usuario)) {
        // Contraseña correcta, mostrar confirmación
        ?>
        <script>
            let confirmDelete = confirm("¿Está seguro de eliminar el producto?");
            if (confirmDelete) {
                // Enviar la solicitud de eliminación
                let formData = new FormData();
                formData.append('confirmar_eliminar', true);
                formData.append('codigo_producto_eliminar', '<?php echo htmlspecialchars($codigoProductoEliminar); ?>');
                
                fetch('productos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    alert(result); // Mostrar mensaje de resultado
                    window.location.href = 'productos.php'; // Redirigir después de la eliminación
                });
            }
        </script>
        <?php
    } else {
        // Contraseña incorrecta
        echo '<script>alert("Contraseña incorrecta"); window.location.href = "productos.php";</script>';
    }
    exit();
}

// Confirmar y realizar eliminación - Parte 3: Eliminar Producto
if (isset($_POST['confirmar_eliminar'])) {
    $codigoProductoEliminar = $_POST['codigo_producto_eliminar'];

    // Proceder con la eliminación
    $sqlEliminarConfirmado = "DELETE FROM productos WHERE codigo_producto = :codigo_producto";
    $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
    $resultadoEliminarConfirmado->execute(array(":codigo_producto" => $codigoProductoEliminar));

    echo "El Producto ha sido eliminado correctamente.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Productos</title>
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
            <a href="../../cruds/ciudades.php" onclick="showSection('ciudades')">Crud ciudades</a>
            <a href="../../cruds/categorias.php" onclick="showSection('categorias')">Crud categorias</a>
            <a href="../../cruds/marcas.php" onclick="showSection('marcas')">Crud marcas</a>
            <a href="productos.php" onclick="showSection('productos')">Crud productos</a>
            <a href="../../cruds/usuarios/usuarios.php" onclick="showSection('usuarios')">Crud usuarios</a>
            <a href="../../cruds/pedidos/pedidos.php" onclick="showSection('pedidos')">Pedidos</a>
            <a href="../../cruds/soporte/soporte.php" onclick="showSection('soporte')">Soporte</a>
            <a href="../../views/reportes.php" onclick="showSection('reportes')">Reportes</a>

    </div>

    <main id="main-content">
        <div id="productos" class="section active">
            <h1><span class="color">Gestionar Productos</span></h1>
            <div class="crud-container">
            <a href="register_productos.php" class="buttonA">Agregar producto</a>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" autocomplete="off">
                        <input type="text" name="buscar" placeholder="Buscar por ID o Nombre" value="<?php echo isset($buscar) ? htmlspecialchars($buscar) : ''; ?>">
                        <button type="submit" class="button">Buscar</button>
                    </form>
                    <table align="center" border="" bordercolor="orange" class="crud-table">
                        <tr>
                            <th>Código Producto</th>
                            <th>Nombre Producto</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Categoría</th>
                            <th>Marca</th>
                            <th>Tallas</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                        <tbody id="crud-table-body">
                            <?php foreach ($productos as $producto) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto->codigo_producto); ?></td>
                                    <td><?php echo htmlspecialchars($producto->nombre_producto); ?></td>
                                    <td><?php echo htmlspecialchars($producto->descripcion_producto); ?></td>
                                    <td><?php echo htmlspecialchars($producto->precio_producto); ?></td>
                                    <td><?php echo htmlspecialchars($producto->stock); ?></td>
                                    <td><?php echo htmlspecialchars($producto->nombre_categoria); ?></td>
                                    <td><?php echo htmlspecialchars($producto->nombre_marca); ?></td>
                                    <td><?php echo htmlspecialchars($producto->tallas); ?></td>
                                    <td><img src="images/<?php echo htmlspecialchars($producto->imagen_producto); ?>" alt="Imagen del Producto" width="100"></td>
                                    <td>
                                        <a href="editar_productos.php?codigo_producto=<?php echo htmlspecialchars($producto->codigo_producto); ?>" class="buttonA" style="margin-left: 5px;">Editar</a>
                                        <a href="productos.php?eliminar=<?php echo htmlspecialchars($producto->codigo_producto); ?>" class="buttonE" style="margin-left: 5px;">Eliminar</a> 
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="pagination">
                    <?php if ($pagina > 1) : ?>
                            <li><a href="?pagina=<?php echo $pagina - 1; ?>" class="button">Anterior</a></li>
                        <?php endif; ?>

                        <?php if ($pagina < $totalPaginas) : ?>
                            <li><a href="?pagina=<?php echo $pagina + 1; ?>" class="button">Siguiente</a></li>
                        <?php endif; ?>
                </div>
                </form>

            
        <!-- Confirmación para eliminar productos -->
        <?php if (isset($_GET['eliminar']) && !isset($_POST['verificar_contraseña'])): ?>
                    <div id="confirm-delete" style="padding: 20px;">
                        <form id="delete-form" method="post" action="">
                            <input type="hidden" name="codigo_producto_eliminar" value="<?php echo htmlspecialchars($codigoProductoEliminar); ?>">
                            <label for="admin_password">Ingrese la contraseña de Admin:</label>
                            <input type="password" id="admin_password" name="admin_password" required>
                            <button type="submit" name="verificar_contraseña" class="button">Verificar</button>
                        </form>
                    </div>
        <?php endif; ?>
    </main>
</div>
<script src="../../js/admin_dashboard.js"></script>
</body>
</html>
