<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CRUD Marcas</title>
    <link rel="stylesheet" href="css/Style_Crud_Marca.css">
</head>
<body>
    <h1 align="center">Panel de Marcas</h1>

    <?php
    require("conexion.php");

    // Paginación
    $registrosPorPagina = 3;
    if (isset($_GET["pagina"])) {
        if ($_GET["pagina"] == 1) {
            header("Location:CRUD_marcas.php");
        } else {
            $pagina = $_GET["pagina"];
        }
    } else {
        $pagina = 1;
    }

    $inicio = ($pagina - 1) * $registrosPorPagina;
    $sqlTotal = "SELECT * FROM marcas";
    $resultadoTotal = $base->prepare($sqlTotal);
    $resultadoTotal->execute(array());
    $numFilas = $resultadoTotal->rowCount();
    $totalPaginas = ceil($numFilas / $registrosPorPagina);

    // Obtener registros de la tabla
    $sqlMarcas = "SELECT * FROM marcas LIMIT $inicio, $registrosPorPagina";
    $resultadoMarcas = $base->query($sqlMarcas)->fetchAll(PDO::FETCH_OBJ);

    // Insertar nueva Marca
    if (isset($_POST['inserta'])) {
        $idMarca = $_POST['id_marca'];
        $nombreMarca = $_POST['nombre_marca'];

        // Verificar si la Marca ya existe
        $sqlVerificar = "SELECT COUNT(*) as total FROM marcas WHERE id_marca = :id_marca";
        $resultadoVerificar = $base->prepare($sqlVerificar);
        $resultadoVerificar->execute(array(":id_marca" => $idMarca));
        $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            echo "<h1 align='center'>¡Error! La Marca con ID $idMarca ya existe.</h1>";
        } else {
            // Marca no existe, proceder con la inserción
            $sqlInsert = "INSERT INTO marcas (id_marca, nombre_marca) VALUES (:id_marca, :nombre_marca)";
            $resultadoInsert = $base->prepare($sqlInsert);
            $resultadoInsert->execute(array(":id_marca" => $idMarca, ":nombre_marca" => $nombreMarca));

            echo "<h1 align='center'>La Marca con ID $idMarca ha sido Registrada correctamente.</h1>";

            header("Location:CRUD_marcas.php");
        }
    }

    // Obtener datos de la Marca para editar
    $MarcaEditar = null;
    if (isset($_GET['editar'])) {
        $idMarcaEditar = $_GET['editar'];
        $sqlEditar = "SELECT * FROM marcas WHERE id_marca = :id_marca";
        $resultadoEditar = $base->prepare($sqlEditar);
        $resultadoEditar->execute(array(":id_marca" => $idMarcaEditar));
        $MarcaEditar = $resultadoEditar->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar Marca
    if (isset($_POST['actualiza'])) {
        $idMarca = $_POST['id_marca'];
        $nombreMarca = $_POST['nombre_marca'];
        $sqlUpdate = "UPDATE marcas SET nombre_marca = :nombre_marca WHERE id_marca = :id_marca";
        $resultadoUpdate = $base->prepare($sqlUpdate);
        $resultadoUpdate->execute(array(":id_marca" => $idMarca, ":nombre_marca" => $nombreMarca));

        echo "<h1 align='center'>La Marca con ID $idMarca ha sido Actualizada correctamente.</h1>";

        header("Location:CRUD_marcas.php");
    }

    // Eliminar Marca
    if (isset($_GET['eliminar'])) {
        $idMarcaEliminar = $_GET['eliminar'];
        $sqlEliminar = "SELECT * FROM marcas WHERE id_marca = :id_marca";
        $resultadoEliminar = $base->prepare($sqlEliminar);
        $resultadoEliminar->execute(array(":id_marca" => $idMarcaEliminar));
        $MarcaEliminar = $resultadoEliminar->fetch(PDO::FETCH_OBJ);

        if ($MarcaEliminar) {
            ?>
            <div align="center">
                <h1>Confirmar Eliminación</h1>
                <h1>¿Está seguro de eliminar la Marca "<?php echo $MarcaEliminar->nombre_marca; ?>"?</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="id_marca_eliminar" value="<?php echo $MarcaEliminar->id_marca; ?>">
                    <input type="submit" name="confirmar_eliminar" value="Sí">
                    <input type="button" value="No" onclick="location.href='CRUD_marcas.php'">
                </form>
            </div>
            <?php
        } else {
            echo "<h3 align='center'>Marca no encontrada</h3>";
        }
    }

    // Confirmar y realizar eliminación
    if (isset($_POST['confirmar_eliminar'])) {
        $idMarcaEliminar = $_POST['id_marca_eliminar'];
        $sqlEliminarConfirmado = "DELETE FROM marcas WHERE id_marca = :id_marca";
        $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
        $resultadoEliminarConfirmado->execute(array(":id_marca" => $idMarcaEliminar));

        echo "<h1 align='center'>La Marca con ID $idMarcaEliminar ha sido Eliminada correctamente.</h1>";

        header("Location:CRUD_marcas.php");
    }
    ?>

    <h3 align="center">CRUD Marcas</h3>
    <div class="login-box">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
            <table align="center" border="" bordercolor="orange">
                <tr>
                    <th>ID Marca</th>
                    <th>Nombre Marca</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($resultadoMarcas as $marca) : ?>
                    <tr>
                        <td><?php echo $marca->id_marca; ?></td>
                        <td><?php echo $marca->nombre_marca; ?></td>
                        <td>
                            <a href="?editar=<?php echo $marca->id_marca; ?>" style="margin-right: 10px;">Editar</a>
                            <a href="?eliminar=<?php echo $marca->id_marca; ?>" style="margin-left: 5px;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($MarcaEditar) : ?>
                <tr>
                    <td><input type="hidden" name="id_marca" value="<?php echo $MarcaEditar->id_marca; ?>"></td>
                    <td><input type="text" name="nombre_marca" value="<?php echo $MarcaEditar->nombre_marca; ?>"></td>
                    <td><input type="submit" name="actualiza" value="Actualizar"></td>
                </tr>
                <?php else : ?>
                <tr>
                    <td><input type="text" name="id_marca"></td>
                    <td><input type="text" name="nombre_marca"></td>
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
