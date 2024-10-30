<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    // Redirigir al índice con un mensaje
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}

require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo_producto = $_POST['codigo_producto'];
    $nombre_producto = $_POST['nombre_producto'];
    $descripcion_producto = $_POST['descripcion_producto'];
    $precio_producto = $_POST['precio_producto'];
    $stock = $_POST['stock'];
    $categoria_id = $_POST['categoria_id'];
    $marca_id = $_POST['marca_id'];
    $tallas = $_POST['tallas']; // Recogiendo las tallas
    $imagen_producto = '';

    if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
        $nombreImagen = basename($_FILES['imagen_producto']['name']);
        $rutaTemporal = $_FILES['imagen_producto']['tmp_name'];
        $rutaDestino = __DIR__ . "/images/" . $nombreImagen;

        if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
            $imagen_producto = $nombreImagen;
        } else {
            echo "Error al subir la imagen.";
        }
    }

    // Incluir las tallas en la inserción SQL
    $sql = "INSERT INTO productos (codigo_producto, nombre_producto, descripcion_producto, precio_producto, stock, categoria_id, marca_id, tallas, imagen_producto) 
            VALUES (:codigo_producto, :nombre_producto, :descripcion_producto, :precio_producto, :stock, :categoria_id, :marca_id, :tallas, :imagen_producto)";
    $stmt = $base->prepare($sql);
    $stmt->execute([
        ':codigo_producto' => $codigo_producto,
        ':nombre_producto' => $nombre_producto,
        ':descripcion_producto' => $descripcion_producto,
        ':precio_producto' => $precio_producto,
        ':stock' => $stock,
        ':categoria_id' => $categoria_id,
        ':marca_id' => $marca_id,
        ':tallas' => $tallas, // Insertando las tallas
        ':imagen_producto' => $imagen_producto
    ]);

    echo "<script>
            alert('Producto registrado con éxito');
            window.location.href = 'productos.php';
        </script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Productos</title>
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
            <div id="registrar_producto" class="crud-containerR">
                <h2>Registrar Producto</h2>
                <form method="post" action="" enctype="multipart/form-data">
                    <label for="codigo_producto">Código de Producto:</label>
                    <input type="text" id="codigo_producto" name="codigo_producto" required><br><br>

                    <label for="nombre_producto">Nombre:</label>
                    <input type="text" id="nombre_producto" name="nombre_producto" required><br><br>

                    <label for="descripcion_producto">Descripción:</label> <br>
                    <textarea id="descripcion_producto" name="descripcion_producto" required></textarea><br><br>

                    <label for="precio_producto">Precio:</label>
                    <input type="number" id="precio_producto" name="precio_producto" required><br><br>

                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" required><br><br>

                    <label for="categoria_id">Categoría:</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <!-- Opciones de categorías -->
                        <?php
                        $resultadoCategorias = $base->query("SELECT id_categoria, nombre_categoria FROM categorías")->fetchAll(PDO::FETCH_OBJ);
                        foreach ($resultadoCategorias as $categoria) {
                            echo "<option value='$categoria->id_categoria'>$categoria->nombre_categoria</option>";
                        }
                        ?>
                    </select><br><br>

                    <label for="marca_id">Marca:</label>
                    <select id="marca_id" name="marca_id" required>
                        <!-- Opciones de marcas -->
                        <?php
                        $resultadoMarcas = $base->query("SELECT id_marca, nombre_marca FROM marcas")->fetchAll(PDO::FETCH_OBJ);
                        foreach ($resultadoMarcas as $marca) {
                            echo "<option value='$marca->id_marca'>$marca->nombre_marca</option>";
                        }
                        ?>
                    </select><br><br>

                    <!-- Campo para ingresar tallas -->
                    <label for="tallas">Tallas (separadas por comas):</label>
                    <input type="text" id="tallas" name="tallas" placeholder="Ejemplo: 35, 36, 37" required><br><br>

                    <label for="imagen_producto">Imagen:</label>
                    <input type="file" id="imagen_producto" name="imagen_producto" accept="image/*"><br><br>

                    <button type="submit" class="button">Registrar</button>
                </form>
            </div>
        </main>
    </div>

    <script src="../../js/admin_dashboard.js"></script>
    <script>
    document.getElementById('logout-button').addEventListener('click', function() {
        document.getElementById('logout-form').submit();
    });
    </script>
</body>
</html>
