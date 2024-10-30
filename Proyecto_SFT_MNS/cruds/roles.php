<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
require '../config/database.php';

// Paginación
$registrosPorPagina = 6;
$pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
$inicio = ($pagina - 1) * $registrosPorPagina;

// Filtro de búsqueda
$filtroRol = isset($_GET['filtro_rol']) ? $_GET['filtro_rol'] : '';

// Consulta para contar el número total de roles (con o sin filtro)
$sqlTotal = "SELECT COUNT(*) FROM roles WHERE nombre_rol LIKE :filtro_rol OR id_rol LIKE :filtro_rol";
$stmtTotal = $base->prepare($sqlTotal);
$stmtTotal->execute([':filtro_rol' => '%' . $filtroRol . '%']);
$numFilas = $stmtTotal->fetchColumn();
$totalPaginas = ceil($numFilas / $registrosPorPagina);

// Obtener registros de la tabla (con o sin filtro)
$sqlRoles = "SELECT * FROM roles WHERE nombre_rol LIKE :filtro_rol OR id_rol LIKE :filtro_rol LIMIT $inicio, $registrosPorPagina";
$stmtRoles = $base->prepare($sqlRoles);
$stmtRoles->execute([':filtro_rol' => '%' . $filtroRol . '%']);
$resultadoRoles = $stmtRoles->fetchAll(PDO::FETCH_OBJ);


// Insertar nuevo Rol
if (isset($_POST['inserta'])) {
    $nombreRol = $_POST['nombre_rol'];
    // Verificar si el Rol ya existe
    $sqlVerificar = "SELECT COUNT(*) as total FROM roles WHERE nombre_rol = :nombre_rol";
    $resultadoVerificar = $base->prepare($sqlVerificar);
    $resultadoVerificar->execute(array(":nombre_rol" => $nombreRol));
    $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

    if ($row['total'] > 0) {
        $_SESSION['message'] = "¡Error! El Rol \"$nombreRol\" ya existe.";
        $_SESSION['message_type'] = 'error';
    } else {
        // Rol no existe, proceder con la inserción
        $sqlInsert = "INSERT INTO roles (nombre_rol) VALUES (:nombre_rol)";
        $resultadoInsert = $base->prepare($sqlInsert);
        $resultadoInsert->execute(array(":nombre_rol" => $nombreRol));

        $_SESSION['message'] = "El Rol \"$nombreRol\" ha sido registrado correctamente.";
        $_SESSION['message_type'] = 'success';
    }
    header("Location: roles.php");
    exit();
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

    $_SESSION['message'] = "El Rol \"$nombreRol\" ha sido actualizado correctamente.";
    $_SESSION['message_type'] = 'success';
    header("Location: roles.php");
    exit();
}

if (isset($_GET['eliminar'])) {
    $idRolEliminar = $_GET['eliminar'];
}

// Confirmar y realizar eliminación - Parte 2: Verificar Contraseña
if (isset($_POST['verificar_contraseña'])) {
    $adminPassword = $_POST['admin_password'];
    $idRolEliminar = $_POST['id_rol_eliminar'];

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
            let confirmDelete = confirm("¿Está seguro de eliminar el rol?");
            if (confirmDelete) {
                // Enviar la solicitud de eliminación
                let formData = new FormData();
                formData.append('confirmar_eliminar', true);
                formData.append('id_rol_eliminar', '<?php echo htmlspecialchars($idRolEliminar); ?>');
                
                fetch('roles.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    alert(result); // Mostrar mensaje de resultado
                    window.location.href = 'roles.php'; // Redirigir después de la eliminación
                });
            }
        </script>
        <?php
    } else {
        // Contraseña incorrecta
        echo '<script>alert("Contraseña incorrecta"); window.location.href = "roles.php";</script>';
    }
    exit();
}

// Confirmar y realizar eliminación - Parte 3: Eliminar Rol
if (isset($_POST['confirmar_eliminar'])) {
    $idRolEliminar = $_POST['id_rol_eliminar'];

    // Proceder con la eliminación
    $sqlEliminarConfirmado = "DELETE FROM roles WHERE id_rol = :id_rol";
    $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
    $resultadoEliminarConfirmado->execute(array(":id_rol" => $idRolEliminar));

    echo "El Rol ha sido eliminado correctamente.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Roles</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
</head>
<body>
    <header>
        <div class="admin-header">
            <div class="admin-logo">
                <img src="../imagenes/usuario.png" alt="User Logo" width="50" height="50">
            </div>
            <div class="admin-name">
                <h2>Administrador: <?php echo htmlspecialchars($_SESSION['admin_nombre']) . ' ' . htmlspecialchars($_SESSION['admin_apellido']); ?></h2>
            </div>
            <button class="menu-button" onclick="toggleSidebar()">☰</button>
            <form id="logout-form" method="POST" action="../views/cerrar_sesion.php">
                <button type="button" id="logout-button" class="buttonC">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <div class="admin-container">
        <div class="sidebar" id="sidebar">
            <a href="../views/admin_dashboard.php">Inicio</a>
            <a href="../cruds/roles.php" onclick="showSection('roles')">Crud Roles</a>
            <a href="../cruds/departamentos.php" onclick="showSection('departamentos')">Crud Departamentos</a>
            <a href="../cruds/ciudades.php" onclick="showSection('ciudades')">Crud ciudades</a>
            <a href="../cruds/categorias.php" onclick="showSection('categorias')">Crud categorias</a>
            <a href="../cruds/marcas.php" onclick="showSection('marcas')">Crud marcas</a>
            <a href="../cruds/productos/productos.php" onclick="showSection('productos')">Crud productos</a>
            <a href="../cruds/usuarios/usuarios.php" onclick="showSection('usuarios')">Crud usuarios</a>
            <a href="../cruds/pedidos/pedidos.php" onclick="showSection('usuarios')">Pedidos</a>
            <a href="../cruds/soporte/soporte.php" onclick="showSection('usuarios')">Soporte</a>
            <a href="../views/reportes.php" onclick="showSection('reportes')">Reportes</a>
        </div>

        <main id="main-content">
            <div id="roles" class="section active">
                <h1><span class="color">Gestionar Roles</span></h1>
            
                <div class="crud-container">
                <form method="get" action="">
                        <input type="text" name="filtro_rol" placeholder="Buscar por nombre de rol" value="<?php echo htmlspecialchars($filtroRol); ?>">
                        <button type="submit" class="button">Buscar</button>
                    </form><br>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                        <table align="center" border="" bordercolor="orange" class="crud-table">
                            <tr>
                                <th>ID Rol</th>
                                <th>Nombre Rol</th>
                                <th>Acciones</th>
                            </tr>
                        <tbody id="crud-table-body">
                            <?php foreach ($resultadoRoles as $rol) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rol->id_rol); ?></td>
                                    <td><?php echo htmlspecialchars($rol->nombre_rol); ?></td>
                                    <td>
                                        <a href="?editar=<?php echo htmlspecialchars($rol->id_rol); ?>" class="buttonA" style="margin-right: 10px;">Editar</a>
                                        <a href="?eliminar=<?php echo htmlspecialchars($rol->id_rol); ?>" class="buttonE" style="margin-left: 5px;">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($RolEditar) : ?>
                            <tr>
                                <form method="post" action="">
                                    <td><input type="hidden" name="id_rol" value="<?php echo htmlspecialchars($RolEditar->id_rol); ?>"></td>
                                    <td><input type="text" name="nombre_rol" value="<?php echo htmlspecialchars($RolEditar->nombre_rol); ?>"></td>
                                    <td>
                                        <button type="submit" name="actualiza" class="button">Actualizar</button>
                                    </td>
                                </form>
                            </tr>
                            <?php else : ?>
                            <tr>
                                <form method="post" action="">
                                    <td></td>
                                    <td><input type="text" name="nombre_rol" placeholder="Nombre del Rol" class="inputAgregar"></td>
                                    <td>
                                        <button type="submit" name="inserta" class="button">Agregar Rol</button>
                                    </td>
                                </form>
                            </tr>
                            <?php endif; ?>
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
                    <!-- Formulario de Contraseña para eliminar Rol -->
                    <?php if (isset($_GET['eliminar']) && !isset($_POST['verificar_contraseña'])): ?>
                    <div id="confirm-delete" style="padding: 20px;">
                        <form id="delete-form" method="post" action="">
                            <input type="hidden" name="id_rol_eliminar" value="<?php echo htmlspecialchars($idRolEliminar); ?>">
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

    <script src="../js/admin_Dashboard.js"></script>
</body>
</html>
