<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CRUD Ciudades</title>
    <link rel="stylesheet" href="css/Style_Crud_Ciudad.css">
</head>
<body>
    <h1 align="center">Panel de Ciudades</h1>

    <?php
    require("conexion.php");

    // Paginación
    $registrosPorPagina = 3;
    if (isset($_GET["pagina"])) {
        if ($_GET["pagina"] == 1) {
            header("Location:CRUD_ciudades.php");
        } else {
            $pagina = $_GET["pagina"];
        }
    } else {
        $pagina = 1;
    }

    $inicio = ($pagina - 1) * $registrosPorPagina;
    $sqlTotal = "SELECT * FROM ciudades";
    $resultadoTotal = $base->prepare($sqlTotal);
    $resultadoTotal->execute(array());
    $numFilas = $resultadoTotal->rowCount();
    $totalPaginas = ceil($numFilas / $registrosPorPagina);

    // Obtener registros de la tabla
    $sqlCiudades = "SELECT * FROM ciudades LIMIT $inicio, $registrosPorPagina";
    $resultadoCiudades = $base->query($sqlCiudades)->fetchAll(PDO::FETCH_OBJ);

    // Obtener lista de departamentos para el campo desplegable
    $sqlDepartamentos = "SELECT * FROM departamentos";
    $resultadoDepartamentos = $base->query($sqlDepartamentos)->fetchAll(PDO::FETCH_OBJ);

    // Insertar nueva Ciudad
    if (isset($_POST['inserta'])) {
        $idCiudad = $_POST['id_ciudad'];
        $nombreCiudad = $_POST['nombre_ciudad'];
        $departamentoId = $_POST['departamento_id'];

        // Verificar si la Ciudad ya existe
        $sqlVerificar = "SELECT COUNT(*) as total FROM ciudades WHERE id_ciudad = :id_ciudad";
        $resultadoVerificar = $base->prepare($sqlVerificar);
        $resultadoVerificar->execute(array(":id_ciudad" => $idCiudad));
        $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            echo "<h1 align='center'>¡Error! La Ciudad con ID $idCiudad ya existe.</h1>";
        } else {
            // Ciudad no existe, proceder con la inserción
            $sqlInsert = "INSERT INTO ciudades (id_ciudad, nombre_ciudad, departamento_id) VALUES (:id_ciudad, :nombre_ciudad, :departamento_id)";
            $resultadoInsert = $base->prepare($sqlInsert);
            $resultadoInsert->execute(array(":id_ciudad" => $idCiudad, ":nombre_ciudad" => $nombreCiudad, ":departamento_id" => $departamentoId));

            echo "<h1 align='center'>La Ciudad con ID $idCiudad ha sido Registrada correctamente.</h1>";

            header("Location:CRUD_ciudades.php");
        }
    }

    // Obtener datos de la Ciudad para editar
    $CiudadEditar = null;
    if (isset($_GET['editar'])) {
        $idCiudadEditar = $_GET['editar'];
        $sqlEditar = "SELECT * FROM ciudades WHERE id_ciudad = :id_ciudad";
        $resultadoEditar = $base->prepare($sqlEditar);
        $resultadoEditar->execute(array(":id_ciudad" => $idCiudadEditar));
        $CiudadEditar = $resultadoEditar->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar Ciudad
    if (isset($_POST['actualiza'])) {
        $idCiudad = $_POST['id_ciudad'];
        $nombreCiudad = $_POST['nombre_ciudad'];
        $departamentoId = $_POST['departamento_id'];
        $sqlUpdate = "UPDATE ciudades SET nombre_ciudad = :nombre_ciudad, departamento_id = :departamento_id WHERE id_ciudad = :id_ciudad";
        $resultadoUpdate = $base->prepare($sqlUpdate);
        $resultadoUpdate->execute(array(":id_ciudad" => $idCiudad, ":nombre_ciudad" => $nombreCiudad, ":departamento_id" => $departamentoId));

        echo "<h1 align='center'>La Ciudad con ID $idCiudad ha sido Actualizada correctamente.</h1>";

        header("Location:CRUD_ciudades.php");
    }

    // Eliminar Ciudad
    if (isset($_GET['eliminar'])) {
        $idCiudadEliminar = $_GET['eliminar'];
        $sqlEliminar = "SELECT * FROM ciudades WHERE id_ciudad = :id_ciudad";
        $resultadoEliminar = $base->prepare($sqlEliminar);
        $resultadoEliminar->execute(array(":id_ciudad" => $idCiudadEliminar));
        $CiudadEliminar = $resultadoEliminar->fetch(PDO::FETCH_OBJ);

        if ($CiudadEliminar) {
            ?>
            <div align="center">
                <h1>Confirmar Eliminación</h1>
                <h1>¿Está seguro de eliminar la Ciudad "<?php echo $CiudadEliminar->nombre_ciudad; ?>"?</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="id_ciudad_eliminar" value="<?php echo $CiudadEliminar->id_ciudad; ?>">
                    <input type="submit" name="confirmar_eliminar" value="Sí">
                    <input type="button" value="No" onclick="location.href='CRUD_ciudades.php'">
                </form>
            </div>
            <?php
        } else {
            echo "<h3 align='center'>Ciudad no encontrada</h3>";
        }
    }

    // Confirmar y realizar eliminación
    if (isset($_POST['confirmar_eliminar'])) {
        $idCiudadEliminar = $_POST['id_ciudad_eliminar'];
        $sqlEliminarConfirmado = "DELETE FROM ciudades WHERE id_ciudad = :id_ciudad";
        $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
        $resultadoEliminarConfirmado->execute(array(":id_ciudad" => $idCiudadEliminar));

        echo "<h1 align='center'>La Ciudad con ID $idCiudadEliminar ha sido Eliminada correctamente.</h1>";

        header("Location:CRUD_ciudades.php");
    }
    ?>

    <h3 align="center">CRUD Ciudades</h3>
    <div class="login-box">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
            <table align="center" border="" bordercolor="orange">
                <tr>
                    <th>ID Ciudad</th>
                    <th>Nombre Ciudad</th>
                    <th>Departamento</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($resultadoCiudades as $ciudad) : ?>
                    <tr>
                        <td><?php echo $ciudad->id_ciudad; ?></td>
                        <td><?php echo $ciudad->nombre_ciudad; ?></td>
                        <td><?php echo $ciudad->departamento_id; ?></td>
                        <td>
                            <a href="?editar=<?php echo $ciudad->id_ciudad; ?>" style="margin-right: 10px;">Editar</a>
                            <a href="?eliminar=<?php echo $ciudad->id_ciudad; ?>" style="margin-left: 5px;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($CiudadEditar) : ?>
                <tr>
                    <td><input type="hidden" name="id_ciudad" value="<?php echo $CiudadEditar->id_ciudad; ?>"></td>
                    <td><input type="text" name="nombre_ciudad" value="<?php echo $CiudadEditar->nombre_ciudad; ?>"></td>
                    <td>
                        <select name="departamento_id">
                            <?php foreach ($resultadoDepartamentos as $departamento) : ?>
                                <option value="<?php echo $departamento->id_departamento; ?>" <?php echo ($departamento->id_departamento == $CiudadEditar->departamento_id) ? 'selected' : ''; ?>>
                                    <?php echo $departamento->nombre_departamento; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="submit" name="actualiza" value="Actualizar"></td>
                </tr>
                <?php else : ?>
                <tr>
                    <td><input type="text" name="id_ciudad"></td>
                    <td><input type="text" name="nombre_ciudad"></td>
                    <td>
                        <select name="departamento_id">
                            <?php foreach ($resultadoDepartamentos as $departamento) : ?>
                                <option value="<?php echo $departamento->id_departamento; ?>">
                                    <?php echo $departamento->nombre_departamento; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="submit" name="inserta" value="Insertar"></td>
                </tr>
                <?php endif; ?>
            </table>
        </form><br><br><br>

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