<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
require '../../config/database.php';

// Inicializar la variable $usuario
$usuario = null;

// Verificar si se ha proporcionado un ID de usuario para editar
if (isset($_GET['id_usuario'])) {
    $idUsuarioEditar = $_GET['id_usuario'];

    // Obtener los datos del usuario a editar
    $sqlUsuario = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
    $resultadoUsuario = $base->prepare($sqlUsuario);
    $resultadoUsuario->execute(array(":id_usuario" => $idUsuarioEditar));
    $usuario = $resultadoUsuario->fetch(PDO::FETCH_OBJ);

    // Obtener roles, ciudades y departamentos para los selects
    $sqlRoles = "SELECT * FROM roles";
    $resultadoRoles = $base->query($sqlRoles)->fetchAll(PDO::FETCH_OBJ);

    $sqlCiudades = "SELECT * FROM ciudades";
    $resultadoCiudades = $base->query($sqlCiudades)->fetchAll(PDO::FETCH_OBJ);

    $sqlDepartamentos = "SELECT * FROM departamentos";
    $resultadoDepartamentos = $base->query($sqlDepartamentos)->fetchAll(PDO::FETCH_OBJ);
}

// Actualizar usuario
if (isset($_POST['actualizar_usuario'])) {
    $idUsuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $rol_id = $_POST['rol_id'];
    $ciudad_id = $_POST['ciudad_id'];
    $departamento_id = $_POST['departamento_id'];

    $sqlActualizar = "UPDATE usuarios SET nombre_usuario = :nombre, apellido_usuario = :apellido, email_usuario = :email, direccion_usuario = :direccion, telefono_usuario = :telefono, rol_id = :rol_id, ciudad_id = :ciudad_id, departamento_id = :departamento_id WHERE id_usuario = :id_usuario";
    $resultadoActualizar = $base->prepare($sqlActualizar);
    $resultadoActualizar->execute(array(
        ":id_usuario" => $idUsuario,
        ":nombre" => $nombre,
        ":apellido" => $apellido,
        ":email" => $email,
        ":direccion" => $direccion,
        ":telefono" => $telefono,
        ":rol_id" => $rol_id,
        ":ciudad_id" => $ciudad_id,
        ":departamento_id" => $departamento_id
    ));

    // Redireccionar según el rol
    $redirectPage = ($rol_id == 1) ? 'administradores.php' : 'clientes.php';
    echo '<script>
            alert("Usuario actualizado correctamente.");
            window.location.href = "' . $redirectPage . '";
        </script>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
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
        <div id="usuarios" class="section active">
            <div class="crud-containerR">
            <h1><span class="color">Editar Usuario</span></h1>
                <?php if ($usuario): ?>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($usuario->id_usuario); ?>">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario->nombre_usuario); ?>" required><br>
                    <br>
                    <label for="apellido">Apellido:</label>
                    <input type="text" name="apellido" value="<?php echo htmlspecialchars($usuario->apellido_usuario); ?>" required><br>
                    <br>
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($usuario->email_usuario); ?>" required><br>
                    <br>
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" value="<?php echo htmlspecialchars($usuario->direccion_usuario); ?>"><br>
                    <br>
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario->telefono_usuario); ?>"><br>
                    <br>
                    
                    <input type="hidden" name="rol_id" value="<?php echo htmlspecialchars($usuario->rol_id); ?>">

                    <label for="ciudad_id">Ciudad:</label>
                    <select name="ciudad_id" required>
                        <?php foreach ($resultadoCiudades as $ciudad) : ?>
                            <option value="<?php echo htmlspecialchars($ciudad->id_ciudad); ?>" <?php if ($ciudad->id_ciudad == $usuario->ciudad_id) echo 'selected'; ?>><?php echo htmlspecialchars($ciudad->nombre_ciudad); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br><br>
                    <label for="departamento_id">Departamento:</label>
                    <select name="departamento_id" required>
                        <?php foreach ($resultadoDepartamentos as $departamento) : ?>
                            <option value="<?php echo htmlspecialchars($departamento->id_departamento); ?>" <?php if ($departamento->id_departamento == $usuario->departamento_id) echo 'selected'; ?>><?php echo htmlspecialchars($departamento->nombre_departamento); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br><br>
                    <button type="submit" name="actualizar_usuario" class="button">Actualizar Usuario</button>
                </form>
                <?php else: ?>
                    <p>El usuario que intentas editar no existe.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script src="../../js/admin_dashboard.js"></script>
</body>
</html>
