<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    // Redirigir al índice con un mensaje
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
require '../../config/database.php';

// Inicializar la variable $producto
$producto = null;

// Verificar si se ha proporcionado un código de producto para editar
if (isset($_GET['codigo_producto'])) {
    $codigoProductoEditar = $_GET['codigo_producto'];

    // Obtener los datos del producto a editar
    $sqlProducto = "SELECT * FROM productos WHERE codigo_producto = :codigo_producto";
    $resultadoProducto = $base->prepare($sqlProducto);
    $resultadoProducto->execute(array(":codigo_producto" => $codigoProductoEditar));
    $producto = $resultadoProducto->fetch(PDO::FETCH_OBJ);

    // Obtener categorías y marcas para los selects
    $sqlCategorias = "SELECT * FROM categorías";
    $resultadoCategorias = $base->query($sqlCategorias)->fetchAll(PDO::FETCH_OBJ);

    $sqlMarcas = "SELECT * FROM marcas";
    $resultadoMarcas = $base->query($sqlMarcas)->fetchAll(PDO::FETCH_OBJ);
}

// Actualizar producto
if (isset($_POST['actualizar_producto'])) {
    $codigoProducto = $_POST['codigo_producto'];
    $nombreProducto = $_POST['nombre_producto'];
    $descripcionProducto = $_POST['descripcion_producto'];
    $precioProducto = $_POST['precio_producto'];
    $stock = $_POST['stock'];
    $categoriaId = $_POST['categoria_id'];
    $marcaId = $_POST['marca_id'];
    $tallas = $_POST['tallas']; // Obtener tallas

    $imagenProducto = $_POST['imagen_producto_actual'];
    if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
        $nombreImagen = basename($_FILES['imagen_producto']['name']);
        $rutaTemporal = $_FILES['imagen_producto']['tmp_name'];
        $rutaDestino = __DIR__ . "/images/" . $nombreImagen;

        if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
            $imagenProducto = $nombreImagen;
        } else {
            echo "Error al subir la imagen.";
        }
    }

    // Actualizar el producto incluyendo las tallas
    $sqlActualizar = "UPDATE productos SET nombre_producto = :nombre_producto, descripcion_producto = :descripcion_producto, precio_producto = :precio_producto, stock = :stock, categoria_id = :categoria_id, marca_id = :marca_id, imagen_producto = :imagen_producto, tallas = :tallas WHERE codigo_producto = :codigo_producto";
    $resultadoActualizar = $base->prepare($sqlActualizar);
    $resultadoActualizar->execute(array(
        ":codigo_producto" => $codigoProducto,
        ":nombre_producto" => $nombreProducto,
        ":descripcion_producto" => $descripcionProducto,
        ":precio_producto" => $precioProducto,
        ":stock" => $stock,
        ":categoria_id" => $categoriaId,
        ":marca_id" => $marcaId,
        ":imagen_producto" => $imagenProducto,
        ":tallas" => $tallas
    ));

    echo '<script>
            alert("Producto actualizado correctamente.");
            window.location.href = "productos.php";
        </script>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
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
        <a href="../../cruds/productos/productos.php" onclick="showSection('productos')">Crud productos</a>
        <a href="../../cruds/usuarios/usuarios.php" onclick="showSection('usuarios')">Crud usuarios</a>
        <a href="../../cruds/pedidos/pedidos.php" onclick="showSection('pedidos')">Pedidos</a>
        <a href="../../cruds/soporte/soporte.php" onclick="showSection('soporte')">Soporte</a>
        <a href="../../views/reportes.php" onclick="showSection('reportes')">Reportes</a>

    </div>

    <main id="main-content">
        <div id="productos" class="crud-containerR">
            <h2>Editar Producto</h2>
            <?php if ($producto): ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                <input type="hidden" name="codigo_producto" value="<?php echo htmlspecialchars($producto->codigo_producto); ?>">
                
                <label for="nombre_producto">Nombre Producto:</label>
                <input type="text" name="nombre_producto" value="<?php echo htmlspecialchars($producto->nombre_producto); ?>" required><br><br>
                
                <label for="descripcion_producto">Descripción:</label><br>
                <textarea name="descripcion_producto" required><?php echo htmlspecialchars($producto->descripcion_producto); ?></textarea><br><br>
                
                <label for="precio_producto">Precio:</label>
                <input type="number" name="precio_producto" value="<?php echo htmlspecialchars($producto->precio_producto); ?>" required><br><br>
                
                <label for="stock">Stock:</label>
                <input type="number" name="stock" value="<?php echo htmlspecialchars($producto->stock); ?>" required><br><br>
                
                <label for="categoria_id">Categoría:</label>
                <select name="categoria_id" required>
                    <?php foreach ($resultadoCategorias as $categoria) : ?>
                        <option value="<?php echo htmlspecialchars($categoria->id_categoria); ?>" <?php if ($categoria->id_categoria == $producto->categoria_id) echo 'selected'; ?>><?php echo htmlspecialchars($categoria->nombre_categoria); ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                
                <label for="marca_id">Marca:</label>
                <select name="marca_id" required>
                    <?php foreach ($resultadoMarcas as $marca) : ?>
                        <option value="<?php echo htmlspecialchars($marca->id_marca); ?>" <?php if ($marca->id_marca == $producto->marca_id) echo 'selected'; ?>><?php echo htmlspecialchars($marca->nombre_marca); ?></option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="tallas">Tallas (separadas por comas):</label>
                <input type="text" name="tallas" value="<?php echo htmlspecialchars($producto->tallas); ?>" required><br><br>
                
                <label for="imagen_producto">Imagen Actual: </label>
                <label><?php echo htmlspecialchars($producto->imagen_producto); ?></label><br><br>
                <input type="hidden" name="imagen_producto_actual" value="<?php echo htmlspecialchars($producto->imagen_producto); ?>">
                <label for="imagen_producto">Imagen Nueva</label><br><br>
                <input type="file" name="imagen_producto" accept="image/*"><br><br>

                <button type="submit" name="actualizar_producto" class="button">Actualizar Producto</button>
            </form>
            <?php else: ?>
                <p>El producto que intentas editar no existe.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="../../js/admin_dashboard.js"></script>
</body>
</html>
