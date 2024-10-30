<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
require '../../config/database.php';

// Paginación
$registrosPorPagina = 6;
if (isset($_GET["pagina"])) {
    if ($_GET["pagina"] == 1) {
        header("Location: administradores.php.php");
    } else {
        $pagina = $_GET["pagina"];
    }
} else {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;

// Filtro de búsqueda
$filtro = "";
$parametros = [];

if (isset($_GET['buscar'])) {
    $buscar = '%' . $_GET['buscar'] . '%';
    $filtro = " AND (usuarios.id_usuario LIKE :buscar OR usuarios.nombre_usuario LIKE :buscar OR usuarios.apellido_usuario LIKE :buscar OR usuarios.email_usuario LIKE :buscar)";
    $parametros[':buscar'] = $buscar;
}

// Obtener total de usuarios administradores con el filtro
$sqlTotal = "SELECT COUNT(*) FROM usuarios WHERE rol_id = 2 $filtro";
$resultadoTotal = $base->prepare($sqlTotal);
$resultadoTotal->execute($parametros);
$numFilas = $resultadoTotal->fetchColumn();
$totalPaginas = ceil($numFilas / $registrosPorPagina);

// Obtener registros de la tabla usuarios con los nombres de las tablas relacionadas y el filtro
$sqlUsuarios = "
SELECT 
    usuarios.id_usuario, 
    usuarios.nombre_usuario, 
    usuarios.apellido_usuario, 
    usuarios.email_usuario, 
    usuarios.contraseña_usuario, 
    usuarios.direccion_usuario, 
    usuarios.telefono_usuario, 
    COALESCE(roles.nombre_rol, 'Sin rol') as nombre_rol, 
    COALESCE(ciudades.nombre_ciudad, 'Sin ciudad') as nombre_ciudad, 
    COALESCE(departamentos.nombre_departamento, 'Sin departamento') as nombre_departamento 
FROM usuarios
LEFT JOIN roles ON usuarios.rol_id = roles.id_rol
LEFT JOIN ciudades ON usuarios.ciudad_id = ciudades.id_ciudad
LEFT JOIN departamentos ON usuarios.departamento_id = departamentos.id_departamento
WHERE usuarios.rol_id = 1 $filtro
LIMIT $inicio, $registrosPorPagina";
$resultadoUsuarios = $base->prepare($sqlUsuarios);
$resultadoUsuarios->execute($parametros);
$resultadoUsuarios = $resultadoUsuarios->fetchAll(PDO::FETCH_OBJ);

// Obtener roles, ciudades y departamentos para los selects
$sqlRoles = "SELECT * FROM roles";
$resultadoRoles = $base->query($sqlRoles)->fetchAll(PDO::FETCH_OBJ);

$sqlCiudades = "SELECT * FROM ciudades";
$resultadoCiudades = $base->query($sqlCiudades)->fetchAll(PDO::FETCH_OBJ);

$sqlDepartamentos = "SELECT * FROM departamentos";
$resultadoDepartamentos = $base->query($sqlDepartamentos)->fetchAll(PDO::FETCH_OBJ);

// Eliminar Usuario - Parte 1: Mostrar Formulario
if (isset($_GET['eliminar'])) {
    $idUsuarioEliminar = $_GET['eliminar'];
}

// Confirmar y realizar eliminación - Parte 2: Verificar Contraseña
if (isset($_POST['verificar_contraseña'])) {
    $adminPassword = $_POST['admin_password'];
    $idUsuarioEliminar = $_POST['id_usuario_eliminar'];

    // Verificar la contraseña del administrador
    $sqlVerificarAdmin = "SELECT * FROM usuarios WHERE id_usuario = :admin_id";
    $stmtVerificarAdmin = $base->prepare($sqlVerificarAdmin);
    $stmtVerificarAdmin->execute(array(":admin_id" => $_SESSION['admin_id']));
    $admin = $stmtVerificarAdmin->fetch(PDO::FETCH_OBJ);

    // Comparar la contraseña encriptada
    if ($admin && password_verify($adminPassword, $admin->contraseña_usuario)) {
        // Contraseña correcta, mostrar confirmación
        ?>
        <script>
            let confirmDelete = confirm("¿Está seguro de eliminar el usuario?");
            if (confirmDelete) {
                // Enviar la solicitud de eliminación
                let formData = new FormData();
                formData.append('confirmar_eliminar', true);
                formData.append('id_usuario_eliminar', '<?php echo htmlspecialchars($idUsuarioEliminar); ?>');
                
                fetch('administradores.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    alert(result); // Mostrar mensaje de resultado
                    window.location.href = 'administradores.php'; // Redirigir después de la eliminación
                });
            }
        </script>
        <?php
    } else {
        // Contraseña incorrecta
        echo '<script>alert("Contraseña incorrecta"); window.location.href = "administradores.php";</script>';
    }
    exit();
}

// Confirmar y realizar eliminación - Parte 3: Eliminar Usuario
if (isset($_POST['confirmar_eliminar'])) {
    $idUsuarioEliminar = $_POST['id_usuario_eliminar'];

    // Proceder con la eliminación
    $sqlEliminarConfirmado = "DELETE FROM usuarios WHERE id_usuario = :id_usuario";
    $stmtEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
    $stmtEliminarConfirmado->execute(array(":id_usuario" => $idUsuarioEliminar));

    echo "El Usuario ha sido eliminado correctamente.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Usuarios</title>
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
            <a href="usuarios.php" onclick="showSection('usuarios')">Crud usuarios</a>
            <a href="../../cruds/pedidos/pedidos.php" onclick="showSection('pedidos')">Pedidos</a>
            <a href="../../cruds/soporte/soporte.php" onclick="showSection('soporte')">Soporte</a>
            <a href="../../views/reportes.php" onclick="showSection('reportes')">Reportes</a>

        </div>

        <main id="main-content">
            <div id="usuarios" class="section active">
                <h1><span class="color">Gestionar Administradores</span></h1>
                <div class="crud-container">
                <a href="register_Administrador.php" class="buttonA">Agregar Administrador</a>
                <br><br>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get" autocomplete="off">
                        <input type="text" name="buscar" placeholder="Buscar Administrador" value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                        <button type="submit" class="button">Buscar</button>
                    </form>
                    <br>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                        <table align="center" border="" bordercolor="orange" class="crud-table">
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
                        <tbody id="crud-table-body">
                            <?php foreach ($resultadoUsuarios as $usuario) : ?>
                                <tr>
                                <td><?php echo htmlspecialchars($usuario->id_usuario); ?></td>
                                <td><?php echo htmlspecialchars($usuario->nombre_usuario); ?></td>
                                <td><?php echo htmlspecialchars($usuario->apellido_usuario); ?></td>
                                <td><?php echo htmlspecialchars($usuario->email_usuario); ?></td>
                                <td>******</td>
                                <td><?php echo htmlspecialchars($usuario->direccion_usuario); ?></td>
                                <td><?php echo htmlspecialchars($usuario->telefono_usuario); ?></td>
                                <td><?php echo htmlspecialchars($usuario->nombre_rol); ?></td>
                                <td><?php echo htmlspecialchars($usuario->nombre_ciudad); ?></td>
                                <td><?php echo htmlspecialchars($usuario->nombre_departamento); ?></td>
                                    <td class="td_boton">
                                        <a href="editar_usuario.php?id_usuario=<?php echo htmlspecialchars($usuario->id_usuario); ?>" class="buttonA">Editar</a>
                                        <a href="clientes.php?eliminar=<?php echo htmlspecialchars($usuario->id_usuario); ?>" class="buttonE" onclick="eliminarUsuario(event, '<?php echo htmlspecialchars($usuario->id_usuario); ?>')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                        <div class="pagination">
                    <?php if ($pagina > 1) : ?>
                            <li><a href="?pagina=<?php echo $pagina - 1; ?>" class="button">Anterior</a></li>
                        <?php endif; ?>

                        <?php if ($pagina < $totalPaginas) : ?>
                            <li><a href="?pagina=<?php echo $pagina + 1; ?>" class="button">Siguiente</a></li>
                        <?php endif; ?>
                </div>
                    </form>
                    

                    <!-- Formulario de Contraseña para eliminar Usuario -->
                    <?php if (isset($_GET['eliminar']) && !isset($_POST['verificar_contraseña'])): ?>
                    <div id="confirm-delete" style="padding: 20px;">
                        <form id="delete-form" method="post" action="">
                            <input type="hidden" name="id_usuario_eliminar" value="<?php echo htmlspecialchars($idUsuarioEliminar); ?>">
                            <label for="admin_password">Ingrese la contraseña de Admin:</label>
                            <input type="password" id="admin_password" name="admin_password" required>
                            <button type="submit" name="verificar_contraseña" class="button">Verificar</button>
                        </form>
                    </div>
                <?php endif; ?>
                </div>


            </div>
        </main>
    </div>

    <?php if (isset($_SESSION['message'])) : ?>
        <script>
            alert('<?php echo $_SESSION['message']; ?>');
        </script>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>

    <script src="../../js/admin_Dashboard.js"></script>
</body>
</html