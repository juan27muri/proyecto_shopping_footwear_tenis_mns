<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CRUD Usuarios</title>
    <link rel="stylesheet" href="css/Style_Crud_Usuario.css">
</head>
<body>
    <h1 align="center">Panel de Usuarios</h1>

    <?php
    require("conexion.php");

    // Paginación
    $registrosPorPagina = 3;
    if (isset($_GET["pagina"])) {
        if ($_GET["pagina"] == 1) {
            header("Location:CRUD_usuarios.php");
        } else {
            $pagina = $_GET["pagina"];
        }
    } else {
        $pagina = 1;
    }

    $inicio = ($pagina - 1) * $registrosPorPagina;
    $sqlTotal = "SELECT * FROM usuarios";
    $resultadoTotal = $base->prepare($sqlTotal);
    $resultadoTotal->execute();
    $numFilas = $resultadoTotal->rowCount();
    $totalPaginas = ceil($numFilas / $registrosPorPagina);

    // Obtener registros de la tabla
    $sqlUsuarios = "SELECT * FROM usuarios LIMIT $inicio, $registrosPorPagina";
    $resultadoUsuarios = $base->query($sqlUsuarios)->fetchAll(PDO::FETCH_OBJ);

    // Obtener listas desplegables para rol, ciudad y departamento
    $sqlRoles = "SELECT * FROM roles";
    $resultadoRoles = $base->query($sqlRoles)->fetchAll(PDO::FETCH_OBJ);

    $sqlCiudades = "SELECT * FROM ciudades";
    $resultadoCiudades = $base->query($sqlCiudades)->fetchAll(PDO::FETCH_OBJ);

    $sqlDepartamentos = "SELECT * FROM departamentos";
    $resultadoDepartamentos = $base->query($sqlDepartamentos)->fetchAll(PDO::FETCH_OBJ);

    // Insertar nuevo Usuario
    if (isset($_POST['inserta'])) {
        $idUsuario = $_POST['id_usuario'];
        $nombreUsuario = $_POST['nombre_usuario'];
        $apellidoUsuario = $_POST['apellido_usuario'];
        $emailUsuario = $_POST['email_usuario'];
        $contraseñaUsuario = password_hash($_POST['contraseña_usuario'], PASSWORD_DEFAULT);
        $direccionUsuario = $_POST['direccion_usuario'];
        $telefonoUsuario = $_POST['telefono_usuario'];
        $rolId = $_POST['rol_id'];
        $ciudadId = $_POST['ciudad_id'];
        $departamentoId = $_POST['departamento_id'];

        // Verificar si el Usuario ya existe
        $sqlVerificar = "SELECT COUNT(*) as total FROM usuarios WHERE id_usuario = :id_usuario";
        $resultadoVerificar = $base->prepare($sqlVerificar);
        $resultadoVerificar->execute(array(":id_usuario" => $idUsuario));
        $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            echo "<h1 align='center'>¡Error! El Usuario con ID $idUsuario ya existe.</h1>";
        } else {
            // Usuario no existe, proceder con la inserción
            $sqlInsert = "INSERT INTO usuarios (id_usuario, nombre_usuario, apellido_usuario, email_usuario, contraseña_usuario, direccion_usuario, telefono_usuario, rol_id, ciudad_id, departamento_id) 
            VALUES (:id_usuario, :nombre_usuario, :apellido_usuario, :email_usuario, :contraseña_usuario, :direccion_usuario, :telefono_usuario, :rol_id, :ciudad_id, :departamento_id)";
            $resultadoInsert = $base->prepare($sqlInsert);
            $resultadoInsert->execute(array(
                ":id_usuario" => $idUsuario,
                ":nombre_usuario" => $nombreUsuario,
                ":apellido_usuario" => $apellidoUsuario,
                ":email_usuario" => $emailUsuario,
                ":contraseña_usuario" => $contraseñaUsuario,
                ":direccion_usuario" => $direccionUsuario,
                ":telefono_usuario" => $telefonoUsuario,
                ":rol_id" => $rolId,
                ":ciudad_id" => $ciudadId,
                ":departamento_id" => $departamentoId
            ));

            echo "<h1 align='center'>El Usuario con ID $idUsuario ha sido Registrado correctamente.</h1>";

            header("Location:CRUD_usuarios.php");
        }
    }

    // Obtener datos del Usuario para editar
    $UsuarioEditar = null;
    if (isset($_GET['editar'])) {
        $idUsuarioEditar = $_GET['editar'];
        $sqlEditar = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
        $resultadoEditar = $base->prepare($sqlEditar);
        $resultadoEditar->execute(array(":id_usuario" => $idUsuarioEditar));
        $UsuarioEditar = $resultadoEditar->fetch(PDO::FETCH_OBJ);
    }

    // Actualizar Usuario
    if (isset($_POST['actualiza'])) {
        $idUsuario = $_POST['id_usuario'];
        $nombreUsuario = $_POST['nombre_usuario'];
        $apellidoUsuario = $_POST['apellido_usuario'];
        $emailUsuario = $_POST['email_usuario'];
        $contraseñaUsuario = isset($_POST['contraseña_usuario']) ? password_hash($_POST['contrasena_usuario'], PASSWORD_DEFAULT) : $UsuarioEditar->contraseña_usuario;
        $direccionUsuario = $_POST['direccion_usuario'];
        $telefonoUsuario = $_POST['telefono_usuario'];
        $rolId = $_POST['rol_id'];
        $ciudadId = $_POST['ciudad_id'];
        $departamentoId = $_POST['departamento_id'];

        // Actualizar Usuario
        $sqlUpdate = "UPDATE usuarios SET nombre_usuario = :nombre_usuario, apellido_usuario = :apellido_usuario, email_usuario = :email_usuario, contraseña_usuario = :contraseña_usuario, direccion_usuario = :direccion_usuario, telefono_usuario = :telefono_usuario, rol_id = :rol_id, ciudad_id = :ciudad_id, departamento_id = :departamento_id WHERE id_usuario = :id_usuario";
        $resultadoUpdate = $base->prepare($sqlUpdate);
        $resultadoUpdate->execute(array(
            ":id_usuario" => $idUsuario,
            ":nombre_usuario" => $nombreUsuario,
            ":apellido_usuario" => $apellidoUsuario,
            ":email_usuario" => $emailUsuario,
            ":contraseña_usuario" => $contraseñaUsuario,
            ":direccion_usuario" => $direccionUsuario,
            ":telefono_usuario" => $telefonoUsuario,
            ":rol_id" => $rolId,
            ":ciudad_id" => $ciudadId,
            ":departamento_id" => $departamentoId
        ));

        echo "<h1 align='center'>El Usuario con ID $idUsuario ha sido Actualizado correctamente.</h1>";

        header("Location:CRUD_usuarios.php");
    }

    // Eliminar Usuario
    if (isset($_GET['eliminar'])) {
        $idUsuarioEliminar = $_GET['eliminar'];
        $sqlEliminar = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
        $resultadoEliminar = $base->prepare($sqlEliminar);
        $resultadoEliminar->execute(array(":id_usuario" => $idUsuarioEliminar));
        $UsuarioEliminar = $resultadoEliminar->fetch(PDO::FETCH_OBJ);

        if ($UsuarioEliminar) {
            ?>
            <div align="center">
                <h1>Confirmar Eliminación</h1>
                <h1>¿Está seguro de eliminar el Usuario "<?php echo $UsuarioEliminar->nombre_usuario . ' ' . $UsuarioEliminar->apellido_usuario; ?>"?</h1>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="hidden" name="id_usuario_eliminar" value="<?php echo $UsuarioEliminar->id_usuario; ?>">
                    <input type="submit" name="confirmar_eliminar" value="Sí">
                    <input type="button" value="No" onclick="location.href='CRUD_usuarios.php'">
                </form>
            </div>
            <?php
        } else {
            echo "<h3 align='center'>Usuario no encontrado</h3>";
        }
    }

    // Confirmar y realizar eliminación
    if (isset($_POST['confirmar_eliminar'])) {
        $idUsuarioEliminar = $_POST['id_usuario_eliminar'];
        $sqlEliminarConfirmado = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
        $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
        $resultadoEliminarConfirmado->execute(array(":id_usuario" => $idUsuarioEliminar));

        echo "<h1 align='center'>El Usuario con ID $idUsuarioEliminar ha sido Eliminado correctamente.</h1>";

        header("Location:CRUD_usuarios.php");
    }
    ?>

    <h3 align="center">CRUD Usuarios</h3>
    <div class="login-box">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
            <table align="center" border="" bordercolor="orange">
                <tr>
                    <th>ID Usuario</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Contraseña</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Ciudad</th>
                    <th>Departamento</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($resultadoUsuarios as $usuario) : ?>
                    <tr>
                        <td><?php echo $usuario->id_usuario; ?></td>
                        <td><?php echo $usuario->nombre_usuario; ?></td>
                        <td><?php echo $usuario->apellido_usuario; ?></td>
                        <td><?php echo $usuario->email_usuario; ?></td>
                        <td><?php echo $usuario->email_usuario; ?></td>

                        <td><?php echo $usuario->direccion_usuario; ?></td>
                        <td><?php echo $usuario->telefono_usuario; ?></td>
                        <td><?php echo $usuario->rol_id; ?></td>
                        <td><?php echo $usuario->ciudad_id; ?></td>
                        <td><?php echo $usuario->departamento_id; ?></td>
                        <td>
                            <a href="?editar=<?php echo $usuario->id_usuario; ?>" style="margin-right: 10px;">Editar</a>
                            <a href="?eliminar=<?php echo $usuario->id_usuario; ?>" style="margin-left: 5px;">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($UsuarioEditar) : ?>
                <tr>
                    <td><input type="hidden" name="id_usuario" value="<?php echo $UsuarioEditar->id_usuario; ?>"></td>
                    <td><input type="text" name="nombre_usuario" value="<?php echo $UsuarioEditar->nombre_usuario; ?>"></td>
                    <td><input type="text" name="apellido_usuario" value="<?php echo $UsuarioEditar->apellido_usuario; ?>"></td>
                    <td><input type="email" name="email_usuario" value="<?php echo $UsuarioEditar->email_usuario; ?>"></td>
                    <td><input type="password" name="contraseña_usuario" placeholder="Nueva contraseña"></td>
                    <td><input type="text" name="direccion_usuario" value="<?php echo $UsuarioEditar->direccion_usuario; ?>"></td>
                    <td><input type="text" name="telefono_usuario" value="<?php echo $UsuarioEditar->telefono_usuario; ?>"></td>
                    <td>
                        <select name="rol_id">
                            <?php foreach ($resultadoRoles as $rol) : ?>
                                <option value="<?php echo $rol->id_rol; ?>" <?php echo ($rol->id_rol == $UsuarioEditar->rol_id) ? 'selected' : ''; ?>>
                                    <?php echo $rol->nombre_rol; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="ciudad_id">
                            <?php foreach ($resultadoCiudades as $ciudad) : ?>
                                <option value="<?php echo $ciudad->id_ciudad; ?>" <?php echo ($ciudad->id_ciudad == $UsuarioEditar->ciudad_id) ? 'selected' : ''; ?>>
                                    <?php echo $ciudad->nombre_ciudad; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="departamento_id">
                            <?php foreach ($resultadoDepartamentos as $departamento) : ?>
                                <option value="<?php echo $departamento->id_departamento; ?>" <?php echo ($departamento->id_departamento == $UsuarioEditar->departamento_id) ? 'selected' : ''; ?>>
                                    <?php echo $departamento->nombre_departamento; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="submit" name="actualiza" value="Actualizar"></td>
                </tr>
                <?php else : ?>
                <tr>
                    <td><input type="text" name="id_usuario"></td>
                    <td><input type="text" name="nombre_usuario"></td>
                    <td><input type="text" name="apellido_usuario"></td>
                    <td><input type="email" name="email_usuario"></td>
                    <td><input type="password" name="contraseña_usuario" placeholder="Contraseña"></td>
                    <td><input type="text" name="direccion_usuario"></td>
                    <td><input type="text" name="telefono_usuario"></td>
                    <td>
                        <select name="rol_id">
                            <?php foreach ($resultadoRoles as $rol) : ?>
                                <option value="<?php echo $rol->id_rol; ?>">
                                    <?php echo $rol->nombre_rol; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="ciudad_id">
                            <?php foreach ($resultadoCiudades as $ciudad) : ?>
                                <option value="<?php echo $ciudad->id_ciudad; ?>">
                                    <?php echo $ciudad->nombre_ciudad; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
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
                    echo "<td><a href='CRUD_usuarios.php?pagina=$i'>$i</a></td>";
                }
                ?>
            </tr>
        </table>

        <br><br><br>
        <div align="center">
            <form action="CRUD_usuarios.php" method="get">
                <input type="submit" value="Inicio">
            </form>
        </div>
    </div>
</body>
</html>
