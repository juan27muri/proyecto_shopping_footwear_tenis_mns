<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CRUD Roles</title>
    <link rel="stylesheet" href="css/Style_Crud_Rol.css">
</head>
<body>
    <h1 align="center">Panel de Roles</h1>

    <?php
    require("conexion.php");

    // Paginación
    $registrosPorPagina = 3;
    if (isset($_GET["pagina"])) {
        if ($_GET["pagina"] == 1) {
            header("Location:CRUD_roles.php");
        } else {
            $pagina = $_GET["pagina"];
        }
    } else {
        $pagina = 1;
    }

    $inicio = ($pagina - 1) * $registrosPorPagina;
    $sqlTotal = "SELECT * FROM roles";
    $resultadoTotal = $base->prepare($sqlTotal);
    $resultadoTotal->execute(array());
    $numFilas = $resultadoTotal->rowCount();
    $totalPaginas = ceil($numFilas / $registrosPorPagina);

    // Obtener registros de la tabla
    $sqlRoles = "SELECT * FROM roles LIMIT $inicio, $registrosPorPagina";
    $resultadoRoles = $base->query($sqlRoles)->fetchAll(PDO::FETCH_OBJ);

    // Insertar nuevo Rol
    if (isset($_POST['inserta'])) {
        $idRol = $_POST['id_rol'];
        $nombreRol = $_POST['nombre_rol'];

        // Verificar si el Rol ya existe
        $sqlVerificar = "SELECT COUNT(*) as total FROM roles WHERE id_rol = :id_rol";
        $resultadoVerificar = $base->prepare($sqlVerificar);
        $resultadoVerificar->execute(array(":id_rol" => $idRol));
        $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            echo "<h1 align='center'>¡Error! El Rol con ID $idRol ya existe.</h1>";
        } else {
            // Rol no existe, proceder con la inserción
            $sqlInsert = "INSERT INTO roles (id_rol, nombre_rol) VALUES (:id_rol, :nombre_rol)";
            $resultadoInsert = $base->prepare($sqlInsert);
            $resultadoInsert->execute(array(":id_rol" => $idRol, ":nombre_rol" => $nombreRol));

            echo "<h1 align='center'>El Rol con ID $idRol ha sido Registrado correctamente.</h1>";

            header("Location:CRUD_roles.php");
        }
    }

    // Obtener datos del Rol para editar
    $RolEditar = null;
    if (isset($_GET['editar'])) {
        $idRolEditar = $_GET['editar'];
        $sqlEditar = "SELECT * FROM roles WHERE id_rol = :id_rol";
        $resultadoEditar = $base->prepare($sqlEditar);
        $resultadoEditar->execute(array(":id_rol" => $idRolEditar));
        $RolEditar = $resultadoEditar->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar Rol
    if (isset($_POST['actualiza'])) {
        $idRol = $_POST['id_rol'];
        $nombreRol = $_POST['nombre_rol'];
        $sqlUpdate = "UPDATE roles SET nombre_rol = :nombre_rol WHERE id_rol = :id_rol";
        $resultadoUpdate = $base->prepare($sqlUpdate);
        $resultadoUpdate->execute(array(":id_rol" => $idRol, ":nombre_rol" => $nombreRol));

        echo "<h1 align='center'>El Rol con ID $idRol ha sido Actualizado correctamente.</h1>";

        header("Location:CRUD_roles.php");
    }

    // Eliminar Rol
    if (isset($_GET['eliminar'])) {
        $idRolEliminar = $_GET['eliminar'];
        $sqlEliminar = "SELECT * FROM roles WHERE id_rol = :id_rol";
        $resultadoEliminar = $base->prepare($sqlEliminar);
        $resultadoEliminar->execute(array(":id_rol" => $idRolEliminar));
        $RolEliminar = $resultadoEliminar->fetch(PDO::FETCH_OBJ);

        if ($RolEliminar) {
            ?>
            <div align="center">
                <h1>Confirmar Eliminación</h1>
                <h1>¿Está seguro de eliminar el Rol "<?php echo $RolEliminar->nombre_rol; ?>"?</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="id_rol_eliminar" value="<?php echo $RolEliminar->id_rol; ?>">
                    <input type="submit" name="confirmar_eliminar" value="Sí">
                    <input type="button" value="No" onclick="location.href='CRUD_roles.php'">
                </form>
            </div>
            <?php
        } else {
            echo "<h3 align='center'>Rol no encontrado</h3>";
        }
    }

    // Confirmar y realizar eliminación
    if (isset($_POST['confirmar_eliminar'])) {
        $idRolEliminar = $_POST['id_rol_eliminar'];
        $sqlEliminarConfirmado = "DELETE FROM roles WHERE id_rol = :id_rol";
        $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
        $resultadoEliminarConfirmado->execute(array(":id_rol" => $idRolEliminar));

        echo "<h1 align='center'>El Rol con ID $idRolEliminar ha sido Eliminado correctamente.</h1>";

        header("Location:CRUD_roles.php");
    }
    ?>

    <h3 align="center">CRUD Roles</h3>
    <div class="login-box">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
            <table align="center" border="" bordercolor="orange">
                <tr>
                    <th>ID Rol</th>
                    <th>Nombre Rol</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($resultadoRoles as $rol) : ?>
                    <tr>
                        <td><?php echo $rol->id_rol; ?></td>
                        <td><?php echo $rol->nombre_rol; ?></td>
                        <td>
                            <a href="?editar=<?php echo $rol->id_rol; ?>" style="margin-right: 10px;">Editar</a>
                            <a href="?eliminar=<?php echo $rol->id_rol; ?>" style="margin-left: 5px;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($RolEditar) : ?>
                <tr>
                    <td><input type="hidden" name="id_rol" value="<?php echo $RolEditar->id_rol; ?>"></td>
                    <td><input type="text" name="nombre_rol" value="<?php echo $RolEditar->nombre_rol; ?>"></td>
                    <td><input type="submit" name="actualiza" value="Actualizar"></td>
                </tr>
                <?php else : ?>
                <tr>
                    <td><input type="text" name="id_rol"></td>
                    <td><input type="text" name="nombre_rol"></td>
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
