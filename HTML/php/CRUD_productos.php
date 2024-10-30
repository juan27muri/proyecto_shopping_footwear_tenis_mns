<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CRUD Productos</title>
    <link rel="stylesheet" href="css/Style_Crud_Producto.css">
</head>
<body>
    <h1 align="center">Panel de Productos</h1>

    <?php
    require("conexion.php");

    // Paginación
    $registrosPorPagina = 3;
    if (isset($_GET["pagina"])) {
        if ($_GET["pagina"] == 1) {
            header("Location:CRUD_productos.php");
        } else {
            $pagina = $_GET["pagina"];
        }
    } else {
        $pagina = 1;
    }

    $inicio = ($pagina - 1) * $registrosPorPagina;
    $sqlTotal = "SELECT * FROM productos";
    $resultadoTotal = $base->prepare($sqlTotal);
    $resultadoTotal->execute();
    $numFilas = $resultadoTotal->rowCount();
    $totalPaginas = ceil($numFilas / $registrosPorPagina);

    // Obtener registros de la tabla
    $sqlProductos = "SELECT * FROM productos LIMIT $inicio, $registrosPorPagina";
    $resultadoProductos = $base->query($sqlProductos)->fetchAll(PDO::FETCH_OBJ);

    // Obtener listas desplegables para categorías y marcas
    $sqlCategorias = "SELECT * FROM categorías";
    $resultadoCategorias = $base->query($sqlCategorias)->fetchAll(PDO::FETCH_OBJ);

    $sqlMarcas = "SELECT * FROM marcas";
    $resultadoMarcas = $base->query($sqlMarcas)->fetchAll(PDO::FETCH_OBJ);

    // Insertar nuevo Producto
    if (isset($_POST['inserta'])) {
        $codigoProducto = $_POST['codigo_producto'];
        $nombreProducto = $_POST['nombre_producto'];
        $descripcionProducto = $_POST['descripcion_producto'];
        $precioProducto = $_POST['precio_producto'];
        $stock = $_POST['stock'];
        $categoriaId = $_POST['categoria_id'];
        $marcaId = $_POST['marca_id'];
        $imagenProducto = $_FILES['imagen_producto']['name'];

        // Subir imagen
        if ($imagenProducto) {
            $rutaImagen = 'images/' . basename($imagenProducto);
            move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $rutaImagen);
        } else {
            $rutaImagen = '';
        }

        // Verificar si el Producto ya existe
        $sqlVerificar = "SELECT COUNT(*) as total FROM productos WHERE codigo_producto = :codigo_producto";
        $resultadoVerificar = $base->prepare($sqlVerificar);
        $resultadoVerificar->execute(array(":codigo_producto" => $codigoProducto));
        $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            echo "<h1 align='center'>¡Error! El Producto con código $codigoProducto ya existe.</h1>";
        } else {
            // Producto no existe, proceder con la inserción
            $sqlInsert = "INSERT INTO productos (codigo_producto, nombre_producto, descripcion_producto, precio_producto, stock, categoria_id, marca_id, imagen_producto) 
            VALUES (:codigo_producto, :nombre_producto, :descripcion_producto, :precio_producto, :stock, :categoria_id, :marca_id, :imagen_producto)";
            $resultadoInsert = $base->prepare($sqlInsert);
            $resultadoInsert->execute(array(
                ":codigo_producto" => $codigoProducto,
                ":nombre_producto" => $nombreProducto,
                ":descripcion_producto" => $descripcionProducto,
                ":precio_producto" => $precioProducto,
                ":stock" => $stock,
                ":categoria_id" => $categoriaId,
                ":marca_id" => $marcaId,
                ":imagen_producto" => $rutaImagen
            ));

            echo "<h1 align='center'>El Producto con código $codigoProducto ha sido Registrado correctamente.</h1>";

            header("Location:CRUD_productos.php");
        }
    }

    // Obtener datos del Producto para editar
    $productoEditar = null;
    if (isset($_GET['editar'])) {
        $codigoProductoEditar = $_GET['editar'];
        $sqlEditar = "SELECT * FROM productos WHERE codigo_producto = :codigo_producto";
        $resultadoEditar = $base->prepare($sqlEditar);
        $resultadoEditar->execute(array(":codigo_producto" => $codigoProductoEditar));
        $productoEditar = $resultadoEditar->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar Producto
    if (isset($_POST['actualiza'])) {
        $codigoProducto = $_POST['codigo_producto'];
        $nombreProducto = $_POST['nombre_producto'];
        $descripcionProducto = $_POST['descripcion_producto'];
        $precioProducto = $_POST['precio_producto'];
        $stock = $_POST['stock'];
        $categoriaId = $_POST['categoria_id'];
        $marcaId = $_POST['marca_id'];
        $imagenProducto = $_FILES['imagen_producto']['name'];

        // Subir imagen
        if ($imagenProducto) {
            $rutaImagen = 'images/' . basename($imagenProducto);
            move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $rutaImagen);
        } else {
            $rutaImagen = $_POST['imagen_producto'];
        }

        // Actualizar Producto
        $sqlUpdate = "UPDATE productos SET nombre_producto = :nombre_producto, descripcion_producto = :descripcion_producto, precio_producto = :precio_producto, stock = :stock, categoria_id = :categoria_id, marca_id = :marca_id, imagen_producto = :imagen_producto WHERE codigo_producto = :codigo_producto";
        $resultadoUpdate = $base->prepare($sqlUpdate);
        $resultadoUpdate->execute(array(
            ":codigo_producto" => $codigoProducto,
            ":nombre_producto" => $nombreProducto,
            ":descripcion_producto" => $descripcionProducto,
            ":precio_producto" => $precioProducto,
            ":stock" => $stock,
            ":categoria_id" => $categoriaId,
            ":marca_id" => $marcaId,
            ":imagen_producto" => $rutaImagen
        ));

        echo "<h1 align='center'>El Producto con código $codigoProducto ha sido Actualizado correctamente.</h1>";

        header("Location:CRUD_productos.php");
    }

    // Eliminar Producto
    if (isset($_GET['eliminar'])) {
        $codigoProductoEliminar = $_GET['eliminar'];
        $sqlEliminar = "SELECT * FROM productos WHERE codigo_producto = :codigo_producto";
        $resultadoEliminar = $base->prepare($sqlEliminar);
        $resultadoEliminar->execute(array(":codigo_producto" => $codigoProductoEliminar));
        $productoEliminar = $resultadoEliminar->fetch(PDO::FETCH_OBJ);

        if ($productoEliminar) {
            ?>
            <div align="center">
                <h1>Confirmar Eliminación</h1>
                <h1>¿Está seguro de eliminar el Producto "<?php echo $productoEliminar->nombre_producto; ?>"?</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="codigo_producto_eliminar" value="<?php echo $productoEliminar->codigo_producto; ?>">
                    <input type="submit" name="confirmar_eliminar" value="Sí">
                    <input type="button" value="No" onclick="location.href='CRUD_productos.php'">
                </form>
            </div>
            <?php
        } else {
            echo "<h3 align='center'>Producto no encontrado</h3>";
        }
    }

    // Confirmar y realizar eliminación
    if (isset($_POST['confirmar_eliminar'])) {
        $codigoProductoEliminar = $_POST['codigo_producto_eliminar'];
        $sqlEliminarConfirmado = "DELETE FROM productos WHERE codigo_producto = :codigo_producto";
        $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
        $resultadoEliminarConfirmado->execute(array(":codigo_producto" => $codigoProductoEliminar));

        echo "<h1 align='center'>El Producto con código $codigoProductoEliminar ha sido Eliminado correctamente.</h1>";

        header("Location:CRUD_productos.php");
    }
    ?>

    <h3 align="center">CRUD Productos</h3>
    <div class="login-box">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" autocomplete="off">
            <table align="center" border="" bordercolor="orange">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($resultadoProductos as $producto) : ?>
                    <tr>
                        <td><?php echo $producto->codigo_producto; ?></td>
                        <td><?php echo $producto->nombre_producto; ?></td>
                        <td><?php echo $producto->descripcion_producto; ?></td>
                        <td><?php echo $producto->precio_producto; ?></td>
                        <td><?php echo $producto->stock; ?></td>
                        <td><?php echo $producto->categoria_id; ?></td>
                        <td><?php echo $producto->marca_id; ?></td>
                        <td><img src="images/<?php echo $producto->imagen_producto; ?>" alt="Imagen" width="100"></td>
                        <td>
                            <a href="?editar=<?php echo $producto->codigo_producto; ?>" style="margin-right: 10px;">Editar</a>
                            <a href="?eliminar=<?php echo $producto->codigo_producto; ?>" style="margin-left: 5px;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <?php if ($productoEditar) : ?>
                        <td><input type="text" name="codigo_producto" value="<?php echo $productoEditar->codigo_producto; ?>" readonly></td>
                        <td><input type="text" name="nombre_producto" value="<?php echo $productoEditar->nombre_producto; ?>"></td>
                        <td><input type="text" name="descripcion_producto" value="<?php echo $productoEditar->descripcion_producto; ?>"></td>
                        <td><input type="text" name="precio_producto" value="<?php echo $productoEditar->precio_producto; ?>"></td>
                        <td><input type="text" name="stock" value="<?php echo $productoEditar->stock; ?>"></td>
                        <td>
                            <select name="categoria_id">
                                <?php foreach ($resultadoCategorias as $categoria) : ?>
                                    <option value="<?php echo $categoria->id_categoria; ?>" <?php echo ($productoEditar->categoria_id == $categoria->id_categoria) ? 'selected' : ''; ?>>
                                        <?php echo $categoria->nombre_categoria; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="marca_id">
                                <?php foreach ($resultadoMarcas as $marca) : ?>
                                    <option value="<?php echo $marca->id_marca; ?>" <?php echo ($productoEditar->marca_id == $marca->id_marca) ? 'selected' : ''; ?>>
                                        <?php echo $marca->nombre_marca; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="file" name="imagen_producto">
                            <input type="hidden" name="imagen_producto" value="<?php echo $productoEditar->imagen_producto; ?>">
                        </td>
                        <td><input type="submit" name="actualiza" value="Actualizar"></td>
                    <?php else : ?>
                        <td><input type="text" name="codigo_producto" placeholder="Código Producto" required></td>
                        <td><input type="text" name="nombre_producto" placeholder="Nombre" required></td>
                        <td><input type="text" name="descripcion_producto" placeholder="Descripción" required></td>
                        <td><input type="text" name="precio_producto" placeholder="Precio" required></td>
                        <td><input type="text" name="stock" placeholder="Stock" required></td>
                        <td>
                            <select name="categoria_id" required>
                                <?php foreach ($resultadoCategorias as $categoria) : ?>
                                    <option value="<?php echo $categoria->id_categoria; ?>"><?php echo $categoria->nombre_categoria; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="marca_id" required>
                                <?php foreach ($resultadoMarcas as $marca) : ?>
                                    <option value="<?php echo $marca->id_marca; ?>"><?php echo $marca->nombre_marca; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="file" name="imagen_producto"></td>
                        <td><input type="submit" name="inserta" value="Agregar"></td>
                    <?php endif; ?>
                </tr>
            </table>
        </form>
    </div>
    <div class="paginacion">
        <?php if ($totalPaginas > 1) : ?>
            <ul>
                <?php if ($pagina > 1) : ?>
                    <li><a href="?pagina=<?php echo $pagina - 1; ?>">Anterior</a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
                    <li><a href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <?php if ($pagina < $totalPaginas) : ?>
                    <li><a href="?pagina=<?php echo $pagina + 1; ?>">Siguiente</a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
