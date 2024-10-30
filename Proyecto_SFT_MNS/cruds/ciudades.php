<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
require '../config/database.php';

// Paginación
$registrosPorPagina = 6;
if (isset($_GET["pagina"])) {
    if ($_GET["pagina"] == 1) {
        header("Location: ciudades.php");
    } else {
        $pagina = $_GET["pagina"];
    }
} else {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;
$sqlTotal = "SELECT * FROM ciudades";
$resultadoTotal = $base->prepare($sqlTotal);
$resultadoTotal->execute();
$numFilas = $resultadoTotal->rowCount();
$totalPaginas = ceil($numFilas / $registrosPorPagina);

// Obtener registros de la tabla
$sqlCiudades = "SELECT c.*, d.nombre_departamento FROM ciudades c INNER JOIN departamentos d ON c.departamento_id = d.id_departamento LIMIT $inicio, $registrosPorPagina";
$resultadoCiudades = $base->query($sqlCiudades)->fetchAll(PDO::FETCH_OBJ);

// Obtener departamentos para el select
$sqlDepartamentos = "SELECT * FROM departamentos";
$resultadoDepartamentos = $base->query($sqlDepartamentos)->fetchAll(PDO::FETCH_OBJ);

// Insertar nueva Ciudad
if (isset($_POST['inserta'])) {
    $nombreCiudad = $_POST['nombre_ciudad'];
    $departamentoId = $_POST['departamento_id'];

    // Verificar si la Ciudad ya existe
    $sqlVerificar = "SELECT COUNT(*) as total FROM ciudades WHERE nombre_ciudad = :nombre_ciudad";
    $resultadoVerificar = $base->prepare($sqlVerificar);
    $resultadoVerificar->execute(array(":nombre_ciudad" => $nombreCiudad));
    $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

    if ($row['total'] > 0) {
        $_SESSION['message'] = "¡Error! La Ciudad \"$nombreCiudad\" ya existe.";
        $_SESSION['message_type'] = 'error';
    } else {
        // Ciudad no existe, proceder con la inserción
        $sqlInsert = "INSERT INTO ciudades (nombre_ciudad, departamento_id) VALUES (:nombre_ciudad, :departamento_id)";
        $resultadoInsert = $base->prepare($sqlInsert);
        $resultadoInsert->execute(array(":nombre_ciudad" => $nombreCiudad, ":departamento_id" => $departamentoId));

        $_SESSION['message'] = "La Ciudad \"$nombreCiudad\" ha sido registrada correctamente.";
        $_SESSION['message_type'] = 'success';
    }
    header("Location: ciudades.php");
    exit();
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

    $_SESSION['message'] = "La Ciudad \"$nombreCiudad\" ha sido actualizada correctamente.";
    $_SESSION['message_type'] = 'success';
    header("Location: ciudades.php");
    exit();
}

// Eliminar Ciudad - Parte 1: Mostrar Formulario
if (isset($_GET['eliminar'])) {
    $idCiudadEliminar = $_GET['eliminar'];
}

// Confirmar y realizar eliminación - Parte 2: Verificar Contraseña
if (isset($_POST['verificar_contraseña'])) {
    $adminPassword = $_POST['admin_password'];
    $idCiudadEliminar = $_POST['id_ciudad_eliminar'];

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
            let confirmDelete = confirm("¿Está seguro de eliminar la ciudad?");
            if (confirmDelete) {
                // Enviar la solicitud de eliminación
                let formData = new FormData();
                formData.append('confirmar_eliminar', true);
                formData.append('id_ciudad_eliminar', '<?php echo htmlspecialchars($idCiudadEliminar); ?>');
                
                fetch('ciudades.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    alert(result); // Mostrar mensaje de resultado
                    window.location.href = 'ciudades.php'; // Redirigir después de la eliminación
                });
            }
        </script>
        <?php
    } else {
        // Contraseña incorrecta
        echo '<script>alert("Contraseña incorrecta"); window.location.href = "ciudades.php";</script>';
    }
    exit();
}

// Confirmar y realizar eliminación - Parte 3: Eliminar Ciudad
if (isset($_POST['confirmar_eliminar'])) {
    $idCiudadEliminar = $_POST['id_ciudad_eliminar'];

    // Proceder con la eliminación
    $sqlEliminarConfirmado = "DELETE FROM ciudades WHERE id_ciudad = :id_ciudad";
    $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
    $resultadoEliminarConfirmado->execute(array(":id_ciudad" => $idCiudadEliminar));

    echo "La Ciudad ha sido eliminada correctamente.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Ciudades</title>
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
            <div id="ciudades" class="section active">
                <h1><span class="color">Gestionar Ciudades</span></h1>
                <div class="crud-container">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                        <table align="center" border="" bordercolor="orange" class="crud-table">
                            <tr>
                                <th>ID Ciudad</th>
                                <th>Nombre Ciudad</th>
                                <th>Departamento</th>
                                <th>Acciones</th>
                            </tr>
                        <tbody id="crud-table-body">
                            <?php foreach ($resultadoCiudades as $ciudad) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ciudad->id_ciudad); ?></td>
                                    <td><?php echo htmlspecialchars($ciudad->nombre_ciudad); ?></td>
                                    <td><?php echo htmlspecialchars($ciudad->nombre_departamento); ?></td>
                                    <td>
                                        <a href="?editar=<?php echo htmlspecialchars($ciudad->id_ciudad); ?>" class="buttonA" style="margin-right: 10px;">Editar</a>
                                        <a href="?eliminar=<?php echo htmlspecialchars($ciudad->id_ciudad); ?>" class="buttonE" style="margin-left: 5px;">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($CiudadEditar) : ?>
                            <tr>
                                <form method="post" action="">
                                    <td><input type="hidden" name="id_ciudad" value="<?php echo htmlspecialchars($CiudadEditar->id_ciudad); ?>"></td>
                                    <td><input type="text" name="nombre_ciudad" value="<?php echo htmlspecialchars($CiudadEditar->nombre_ciudad); ?>"></td>
                                    <td>
                                        <select name="departamento_id">
                                            <?php foreach ($resultadoDepartamentos as $departamento) : ?>
                                                <option value="<?php echo htmlspecialchars($departamento->id_departamento); ?>" <?php echo $departamento->id_departamento == $CiudadEditar->departamento_id ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($departamento->nombre_departamento); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="actualiza" class="button">Actualizar</button>
                                    </td>
                                </form>
                            </tr>
                            <?php else : ?>
                            <tr>
                                <form method="post" action="">
                                    <td></td>
                                    <td><input type="text" name="nombre_ciudad" placeholder="Nombre de la Ciudad" class="inputAgregar"></td>
                                    <td>
                                        <select name="departamento_id">
                                            <option value="" disabled selected>Seleccione un Departamento</option>
                                            <?php foreach ($resultadoDepartamentos as $departamento) : ?>
                                                <option value="<?php echo htmlspecialchars($departamento->id_departamento); ?>">
                                                    <?php echo htmlspecialchars($departamento->nombre_departamento); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="inserta" class="button">Agregar Ciudad</button>
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
                    <!-- Formulario de Contraseña para eliminar Ciudad -->
                    <?php if (isset($_GET['eliminar']) && !isset($_POST['verificar_contraseña'])): ?>
                    <div id="confirm-delete" style="padding: 20px;">
                        <form id="delete-form" method="post" action="">
                            <input type="hidden" name="id_ciudad_eliminar" value="<?php echo htmlspecialchars($idCiudadEliminar); ?>">
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
