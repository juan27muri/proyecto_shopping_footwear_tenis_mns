<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    // Redirigir al índice con un mensaje
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}

require '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_POST['id_usuario'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $apellido_usuario = $_POST['apellido_usuario'];
    $email_usuario = $_POST['email_usuario'];
    $contraseña_usuario = $_POST['contraseña_usuario'];
    $direccion_usuario = $_POST['direccion_usuario'];
    $telefono_usuario = $_POST['telefono_usuario'];
    $rol_id = $_POST['rol_id'];
    $ciudad_id = $_POST['ciudad_id'];
    $departamento_id = $_POST['departamento_id'];

    $sql = "INSERT INTO usuarios (id_usuario, nombre_usuario, apellido_usuario, email_usuario, contraseña_usuario, direccion_usuario, telefono_usuario, rol_id, ciudad_id, departamento_id) 
            VALUES (:id_usuario, :nombre_usuario, :apellido_usuario, :email_usuario, :contrasena_usuario, :direccion_usuario, :telefono_usuario, :rol_id, :ciudad_id, :departamento_id)";
    $stmt = $base->prepare($sql);
    $stmt->execute([
        ':id_usuario' => $id_usuario,
        ':nombre_usuario' => $nombre_usuario,
        ':apellido_usuario' => $apellido_usuario,
        ':email_usuario' => $email_usuario,
        ':contrasena_usuario' => password_hash($contraseña_usuario, PASSWORD_DEFAULT), // Encriptar la contraseña
        ':direccion_usuario' => $direccion_usuario,
        ':telefono_usuario' => $telefono_usuario,
        ':rol_id' => $rol_id,
        ':ciudad_id' => $ciudad_id,
        ':departamento_id' => $departamento_id
    ]);

    echo "<script>
            alert('Usuario registrado con éxito');
            window.location.href = 'administradores.php';
        </script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
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
            <div id="registrar_usuario" class="crud-containerR">
                <h2>Registrar Usuario</h2>
                <form method="post" action="">
                    <label for="id_usuario">ID de Usuario:</label>
                    <input type="text" id="id_usuario" name="id_usuario" required><br><br>

                    <label for="nombre_usuario">Nombre:</label>
                    <input type="text" id="nombre_usuario" name="nombre_usuario" required><br><br>

                    <label for="apellido_usuario">Apellido:</label>
                    <input type="text" id="apellido_usuario" name="apellido_usuario" required><br><br>

                    <label for="email_usuario">Email:</label>
                    <input type="email" id="email_usuario" name="email_usuario" required><br><br>

                    <label for="contraseña_usuario">Contraseña:</label>
                    <input type="password" id="contraseña_usuario" name="contraseña_usuario" required><br><br>

                    <label for="direccion_usuario">Dirección:</label>
                    <input type="text" id="direccion_usuario" name="direccion_usuario"><br><br>

                    <label for="telefono_usuario">Teléfono:</label>
                    <input type="number" id="telefono_usuario" name="telefono_usuario"><br><br>

                    <input type="hidden" id="rol_id" name="rol_id" value="1">

                    <label for="ciudad_id">Ciudad:</label>
                    <select id="ciudad_id" name="ciudad_id">
                        <!-- Opciones de ciudades -->
                        <?php
                        $resultadoCiudades = $base->query("SELECT id_ciudad, nombre_ciudad FROM ciudades")->fetchAll(PDO::FETCH_OBJ);
                        foreach ($resultadoCiudades as $ciudad) {
                            echo "<option value='$ciudad->id_ciudad'>$ciudad->nombre_ciudad</option>";
                        }
                        ?>
                    </select><br><br>

                    <label for="departamento_id">Departamento:</label>
                    <select id="departamento_id" name="departamento_id">
                        <!-- Opciones de departamentos -->
                        <?php
                        $resultadoDepartamentos = $base->query("SELECT id_departamento, nombre_departamento FROM departamentos")->fetchAll(PDO::FETCH_OBJ);
                        foreach ($resultadoDepartamentos as $departamento) {
                            echo "<option value='$departamento->id_departamento'>$departamento->nombre_departamento</option>";
                        }
                        ?>
                    </select><br><br>

                    <button type="submit" class="button">Registrar</button>
                </form>
            </div>
        </main>
    </div>

    <script src="../../js/admin_Dashboard.js"></script>
</body>
</html>
