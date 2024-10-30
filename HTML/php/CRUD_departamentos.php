<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CRUD Departamentos</title>
    <link rel="stylesheet" href="css/Style_Crud_Departamento.css">
</head>
<body>
    <h1 align="center">Panel de Departamentos</h1>

    <?php
    require("conexion.php");

    // Paginación
    $registrosPorPagina = 3;
    if (isset($_GET["pagina"])) {
        if ($_GET["pagina"] == 1) {
            header("Location:CRUD_departamentos.php");
        } else {
            $pagina = $_GET["pagina"];
        }
    } else {
        $pagina = 1;
    }

    $inicio = ($pagina - 1) * $registrosPorPagina;
    $sqlTotal = "SELECT * FROM departamentos";
    $resultadoTotal = $base->prepare($sqlTotal);
    $resultadoTotal->execute();
    $numFilas = $resultadoTotal->rowCount();
    $totalPaginas = ceil($numFilas / $registrosPorPagina);

    // Obtener registros de la tabla
    $sqlDepartamentos = "SELECT * FROM departamentos LIMIT $inicio, $registrosPorPagina";
    $resultadoDepartamentos = $base->query($sqlDepartamentos)->fetchAll(PDO::FETCH_OBJ);

    // Insertar nuevo Departamento
    if (isset($_POST['inserta'])) {
        $id = $_POST['id'];
        $nombreDepartamento = $_POST['nombre_departamento'];

        // Verificar si el Departamento ya existe
        $sqlVerificar = "SELECT COUNT(*) as total FROM departamentos WHERE id_departamento = :id_departamento";
        $resultadoVerificar = $base->prepare($sqlVerificar);
        $resultadoVerificar->execute(array(":id_departamento" => $id));
        $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            echo "<h1 align='center'>¡Error! El Departamento con ID $id ya existe.</h1>";
        } else {
            // Departamento no existe, proceder con la inserción
            $sqlInsert = "INSERT INTO departamentos (id_departamento, nombre_departamento) VALUES (:id_departamento, :nombre_departamento)";
            $resultadoInsert = $base->prepare($sqlInsert);
            $resultadoInsert->execute(array(":id_departamento" => $id, ":nombre_departamento" => $nombreDepartamento));

            echo "<h1 align='center'>El Departamento con ID $id ha sido Registrado correctamente.</h1>";

            header("Location:CRUD_departamentos.php");
        }
    }

    // Obtener datos del Departamento para editar
    $DepartamentoEditar = null;
    if (isset($_GET['editar'])) {
        $idDepartamentoEditar = $_GET['editar'];
        $sqlEditar = "SELECT * FROM departamentos WHERE id_departamento = :id_departamento";
        $resultadoEditar = $base->prepare($sqlEditar);
        $resultadoEditar->execute(array(":id_departamento" => $idDepartamentoEditar));
        $DepartamentoEditar = $resultadoEditar->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar Departamento
    if (isset($_POST['actualiza'])) {
        $idDepartamento = $_POST['id_departamento'];
        $nombreDepartamento = $_POST['nombre_departamento'];
        $sqlUpdate = "UPDATE departamentos SET nombre_departamento = :nombre_departamento WHERE id_departamento = :id_departamento";
        $resultadoUpdate = $base->prepare($sqlUpdate);
        $resultadoUpdate->execute(array(":id_departamento" => $idDepartamento, ":nombre_departamento" => $nombreDepartamento));

        echo "<h1 align='center'>El Departamento con ID $idDepartamento ha sido Actualizado correctamente.</h1>";

        header("Location:CRUD_departamentos.php");
    }

    // Eliminar Departamento
    if (isset($_GET['eliminar'])) {
        $idDepartamentoEliminar = $_GET['eliminar'];
        $sqlEliminar = "SELECT * FROM departamentos WHERE id_departamento = :id_departamento";
        $resultadoEliminar = $base->prepare($sqlEliminar);
        $resultadoEliminar->execute(array(":id_departamento" => $idDepartamentoEliminar));
        $DepartamentoEliminar = $resultadoEliminar->fetch(PDO::FETCH_OBJ);

        if ($DepartamentoEliminar) {
            ?>
            <div align="center">
                <h1>Confirmar Eliminación</h1>
                <h1>¿Está seguro de eliminar el Departamento "<?php echo $DepartamentoEliminar->nombre_departamento; ?>"?</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="id_departamento_eliminar" value="<?php echo $DepartamentoEliminar->id_departamento; ?>">
                    <input type="submit" name="confirmar_eliminar" value="Sí">
                    <input type="submit" value="No" onclick="location.href='CRUD_departamentos.php'">
                </form>
            </div>
            <?php
        } else {
            echo "<h3 align='center'>Departamento no encontrado</h3>";
        }
    }

    // Confirmar y realizar eliminación
    if (isset($_POST['confirmar_eliminar'])) {
        $idDepartamentoEliminar = $_POST['id_departamento_eliminar'];
        $sqlEliminarConfirmado = "DELETE FROM departamentos WHERE id_departamento = :id_departamento";
        $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
        $resultadoEliminarConfirmado->execute(array(":id_departamento" => $idDepartamentoEliminar));

        echo "<h1 align='center'>El Departamento con ID $idDepartamentoEliminar ha sido Eliminado correctamente.</h1>";

        header("Location:CRUD_departamentos.php");
    }
    ?>

    <h3 align="center">CRUD Departamentos</h3>
    <div class="login-box">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
            <table align="center" border="" bordercolor="orange">
                <tr>
                    <th>ID</th>
                    <th>Nombre Departamento</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($resultadoDepartamentos as $departamento) : ?>
                    <tr>
                        <td><?php echo $departamento->id_departamento; ?></td>
                        <td><?php echo $departamento->nombre_departamento; ?></td>
                        <td>
                            <a href="?editar=<?php echo $departamento->id_departamento; ?>" style="margin-right: 10px;">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>Editar
                            </a>
                            <a href="?eliminar=<?php echo $departamento->id_departamento; ?>" style="margin-left: 5px;">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($DepartamentoEditar) : ?>
                    <tr>
                        <td><input type="hidden" name="id_departamento" value="<?php echo $DepartamentoEditar->id_departamento; ?>"></td>
                        <td><input type="text" name="nombre_departamento" value="<?php echo $DepartamentoEditar->nombre_departamento; ?>"></td>
                        <td><input align="center" type="submit" name="actualiza" value="Actualizar"></td>
                    </tr>
                <?php else : ?>
                    <tr>
                        <td><input type="text" name="id"></td>
                        <td><input type="text" name="nombre_departamento"></td>
                        <td><input align="center" type="submit" name="inserta" value="Insertar"></td>
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
        <form align='center' action='admin_dashboard.php' method='POST'>
            <input class='grow2' type='submit' name='' value='Volver'>
        </form>
    </div>
</body>
</html>
