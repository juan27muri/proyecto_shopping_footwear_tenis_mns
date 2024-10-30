<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CRUD Categorías</title>
</head>
<body>
    <h1 align="center">Panel de Categorías</h1>

    <?php
    require("Conexion.php");

    // Paginación
    $registrosPorPagina = 3;
    if (isset($_GET["pagina"])) {
        if ($_GET["pagina"] == 1) {
            header("Location:CRUD_categorias.php");
        } else {
            $pagina = $_GET["pagina"];
        }
    } else {
        $pagina = 1;
    }

    $inicio = ($pagina - 1) * $registrosPorPagina;
    $sqlTotal = "SELECT * FROM categorías";
    $resultadoTotal = $base->prepare($sqlTotal);
    $resultadoTotal->execute(array());
    $numFilas = $resultadoTotal->rowCount();
    $totalPaginas = ceil($numFilas / $registrosPorPagina);

    // Obtener registros de la tabla
    $sqlCategorias = "SELECT * FROM categorías LIMIT $inicio, $registrosPorPagina";
    $resultadoCategorias = $base->query($sqlCategorias)->fetchAll(PDO::FETCH_OBJ);

    // Insertar nueva Categoría
    if (isset($_POST['inserta'])) {
        $idCategoria = $_POST['id_categoria'];
        $nombreCategoria = $_POST['nombre_categoria'];
        $descripcionCategoria = $_POST['descripcion_categoria'];

        // Verificar si la Categoría ya existe
        $sqlVerificar = "SELECT COUNT(*) as total FROM categorías WHERE id_categoria = :id_categoria";
        $resultadoVerificar = $base->prepare($sqlVerificar);
        $resultadoVerificar->execute(array(":id_categoria" => $idCategoria));
        $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            echo "<h1 align='center'>¡Error! La Categoría con ID $idCategoria ya existe.</h1>";
        } else {
            // Categoría no existe, proceder con la inserción
            $sqlInsert = "INSERT INTO categorías (id_categoria, nombre_categoria, descripcion_categoria) VALUES (:id_categoria, :nombre_categoria, :descripcion_categoria)";
            $resultadoInsert = $base->prepare($sqlInsert);
            $resultadoInsert->execute(array(":id_categoria" => $idCategoria, ":nombre_categoria" => $nombreCategoria, ":descripcion_categoria" => $descripcionCategoria));

            echo "<h1 align='center'>La Categoría con ID $idCategoria ha sido Registrada correctamente.</h1>";

            header("Location:CRUD_categorias.php");
        }
    }

    // Obtener datos de la Categoría para editar
    $CategoriaEditar = null;
    if (isset($_GET['editar'])) {
        $idCategoriaEditar = $_GET['editar'];
        $sqlEditar = "SELECT * FROM categorías WHERE id_categoria = :id_categoria";
        $resultadoEditar = $base->prepare($sqlEditar);
        $resultadoEditar->execute(array(":id_categoria" => $idCategoriaEditar));
        $CategoriaEditar = $resultadoEditar->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar Categoría
    if (isset($_POST['actualiza'])) {
        $idCategoria = $_POST['id_categoria'];
        $nombreCategoria = $_POST['nombre_categoria'];
        $descripcionCategoria = $_POST['descripcion_categoria'];
        $sqlUpdate = "UPDATE categorías SET nombre_categoria = :nombre_categoria, descripcion_categoria = :descripcion_categoria WHERE id_categoria = :id_categoria";
        $resultadoUpdate = $base->prepare($sqlUpdate);
        $resultadoUpdate->execute(array(":id_categoria" => $idCategoria, ":nombre_categoria" => $nombreCategoria, ":descripcion_categoria" => $descripcionCategoria));

        echo "<h1 align='center'>La Categoría con ID $idCategoria ha sido Actualizada correctamente.</h1>";

        header("Location:CRUD_categorias.php");
    }

    // Eliminar Categoría
    if (isset($_GET['eliminar'])) {
        $idCategoriaEliminar = $_GET['eliminar'];
        $sqlEliminar = "SELECT * FROM categorías WHERE id_categoria = :id_categoria";
        $resultadoEliminar = $base->prepare($sqlEliminar);
        $resultadoEliminar->execute(array(":id_categoria" => $idCategoriaEliminar));
        $CategoriaEliminar = $resultadoEliminar->fetch(PDO::FETCH_OBJ);

        if ($CategoriaEliminar) {
            ?>
            <div align="center">
                <h1>Confirmar Eliminación</h1>
                <h1>¿Está seguro de eliminar la Categoría "<?php echo $CategoriaEliminar->nombre_categoria; ?>"?</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="id_categoria_eliminar" value="<?php echo $CategoriaEliminar->id_categoria; ?>">
                    <input type="submit" name="confirmar_eliminar" value="Sí">
                    <input type="button" value="No" onclick="location.href='CRUD_categorias.php'">
                </form>
            </div>
            <?php
        } else {
            echo "<h3 align='center'>Categoría no encontrada</h3>";
        }
    }

    // Confirmar y realizar eliminación
    if (isset($_POST['confirmar_eliminar'])) {
        $idCategoriaEliminar = $_POST['id_categoria_eliminar'];
        $sqlEliminarConfirmado = "DELETE FROM categorías WHERE id_categoria = :id_categoria";
        $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
        $resultadoEliminarConfirmado->execute(array(":id_categoria" => $idCategoriaEliminar));

        echo "<h1 align='center'>La Categoría con ID $idCategoriaEliminar ha sido Eliminada correctamente.</h1>";

        header("Location:CRUD_categorias.php");
    }
    ?>

    <h3 align="center">CRUD Categorías</h3>
    <div class="login-box">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
            <table align="center" border="" bordercolor="orange">
                <tr>
                    <th>ID Categoría</th>
                    <th>Nombre Categoría</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($resultadoCategorias as $categoria) : ?>
                    <tr>
                        <td><?php echo $categoria->id_categoria; ?></td>
                        <td><?php echo $categoria->nombre_categoria; ?></td>
                        <td><?php echo $categoria->descripcion_categoria; ?></td>
                        <td>
                            <a href="?editar=<?php echo $categoria->id_categoria; ?>" style="margin-right: 10px;">Editar</a>
                            <a href="?eliminar=<?php echo $categoria->id_categoria; ?>" style="margin-left: 5px;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($CategoriaEditar) : ?>
                <tr>
                    <td><input type="hidden" name="id_categoria" value="<?php echo $CategoriaEditar->id_categoria; ?>"></td>
                    <td><input type="text" name="nombre_categoria" value="<?php echo $CategoriaEditar->nombre_categoria; ?>"></td>
                    <td><input type="text" name="descripcion_categoria" value="<?php echo $CategoriaEditar->descripcion_categoria; ?>"></td>
                    <td><input type="submit" name="actualiza" value="Actualizar"></td>
                </tr>
                <?php else : ?>
                <tr>
                    <td><input type="text" name="id_categoria"></td>
                    <td><input type="text" name="nombre_categoria"></td>
                    <td><input type="text" name="descripcion_categoria"></td>
                    <td><input type="submit" name="inserta" value="Insertar"></td>
                </tr>
                <?php endif; ?>
            </table>
        </form><br><br><br>

        <!-- Paginación -->
        <table class="numeracion" border="0" align="center">
            <tr>
                <?php
                for ($i = 1; $i <= $totalPaginas; $i++) {
                    echo "<td><a class='numeracion1' href='?pagina=" . $i . "'>" . $i . "  </a>  </td>";
                }
                ?>
            </tr>
        </table><br><br>
        
        <!-- Botón para volver -->
        <form align='center' action='admin_dashboard.php' method='POST'>
            <input class='grow2' type='submit' name='' value='Volver'>
        </form>
    </div>
</body>
</html>
