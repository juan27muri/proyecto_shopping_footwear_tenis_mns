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
        header("Location: departamentos.php");
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
    $nombreDepartamento = $_POST['nombre_departamento'];

    // Verificar si el Departamento ya existe
    $sqlVerificar = "SELECT COUNT(*) as total FROM departamentos WHERE nombre_departamento = :nombre_departamento";
    $resultadoVerificar = $base->prepare($sqlVerificar);
    $resultadoVerificar->execute(array(":nombre_departamento" => $nombreDepartamento));
    $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

    if ($row['total'] > 0) {
        $_SESSION['message'] = "¡Error! El Departamento \"$nombreDepartamento\" ya existe.";
        $_SESSION['message_type'] = 'error';
    } else {
        // Departamento no existe, proceder con la inserción
        $sqlInsert = "INSERT INTO departamentos (nombre_departamento) VALUES (:nombre_departamento)";
        $resultadoInsert = $base->prepare($sqlInsert);
        $resultadoInsert->execute(array(":nombre_departamento" => $nombreDepartamento));

        $_SESSION['message'] = "El Departamento \"$nombreDepartamento\" ha sido registrado correctamente.";
        $_SESSION['message_type'] = 'success';
    }
    header("Location: departamentos.php");
    exit();
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

    $_SESSION['message'] = "El Departamento \"$nombreDepartamento\" ha sido actualizado correctamente.";
    $_SESSION['message_type'] = 'success';
    header("Location: departamentos.php");
    exit();
}

// Eliminar Departamento - Parte 1: Mostrar Formulario
if (isset($_GET['eliminar'])) {
    $idDepartamentoEliminar = $_GET['eliminar'];
}

// Confirmar y realizar eliminación - Parte 2: Verificar Contraseña
if (isset($_POST['verificar_contraseña'])) {
    $adminPassword = $_POST['admin_password'];
    $idDepartamentoEliminar = $_POST['id_departamento_eliminar'];

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
            let confirmDelete = confirm("¿Está seguro de eliminar el departamento?");
            if (confirmDelete) {
                // Enviar la solicitud de eliminación
                let formData = new FormData();
                formData.append('confirmar_eliminar', true);
                formData.append('id_departamento_eliminar', '<?php echo htmlspecialchars($idDepartamentoEliminar); ?>');
                
                fetch('departamentos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    alert(result); // Mostrar mensaje de resultado
                    window.location.href = 'departamentos.php'; // Redirigir después de la eliminación
                });
            }
        </script>
        <?php
    } else {
        // Contraseña incorrecta
        echo '<script>alert("Contraseña incorrecta"); window.location.href = "departamentos.php";</script>';
    }
    exit();
}

// Confirmar y realizar eliminación - Parte 3: Eliminar Departamento
if (isset($_POST['confirmar_eliminar'])) {
    $idDepartamentoEliminar = $_POST['id_departamento_eliminar'];

    // Proceder con la eliminación
    $sqlEliminarConfirmado = "DELETE FROM departamentos WHERE id_departamento = :id_departamento";
    $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
    $resultadoEliminarConfirmado->execute(array(":id_departamento" => $idDepartamentoEliminar));

    echo "El Departamento ha sido eliminado correctamente.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Departamentos</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">    </style>
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
            <div id="departamentos" class="section active">
                <h1><span class="color">Gestionar Departamentos</span></h1>
                <div class="crud-container">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                        <table align="center" border="" bordercolor="orange" class="crud-table">
                            <tr>
                                <th>ID Departamento</th>
                                <th>Nombre Departamento</th>
                                <th>Acciones</th>
                            </tr>
                        <tbody id="crud-table-body">
                            <?php foreach ($resultadoDepartamentos as $departamento) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($departamento->id_departamento); ?></td>
                                    <td><?php echo htmlspecialchars($departamento->nombre_departamento); ?></td>
                                    <td>
                                        <a href="?editar=<?php echo htmlspecialchars($departamento->id_departamento); ?>" class="buttonA" style="margin-right: 10px;">Editar</a>
                                        <a href="?eliminar=<?php echo htmlspecialchars($departamento->id_departamento); ?>" class="buttonE" style="margin-left: 5px;">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($DepartamentoEditar) : ?>
                            <tr>
                                <form method="post" action="">
                                    <td><input type="hidden" name="id_departamento" value="<?php echo htmlspecialchars($DepartamentoEditar->id_departamento); ?>"></td>
                                    <td><input type="text" name="nombre_departamento" value="<?php echo htmlspecialchars($DepartamentoEditar->nombre_departamento); ?>"></td>
                                    <td>
                                        <button type="submit" name="actualiza" class="button">Actualizar</button>
                                    </td>
                                </form>
                            </tr>
                            <?php else : ?>
                            <tr>
                                <form method="post" action="">
                                    <td></td>
                                    <td><input type="text" name="nombre_departamento" placeholder="Nombre del Departamento" class="inputAgregar"></td>
                                    <td>
                                        <button type="submit" name="inserta" class="button">Agregar Departamento</button>
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
                    <!-- Formulario de Contraseña para eliminar Departamento -->
                    <?php if (isset($_GET['eliminar']) && !isset($_POST['verificar_contraseña'])): ?>
                    <div id="confirm-delete" style="padding: 20px;">
                        <form id="delete-form" method="post" action="">
                            <input type="hidden" name="id_departamento_eliminar" value="<?php echo htmlspecialchars($idDepartamentoEliminar); ?>">
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
