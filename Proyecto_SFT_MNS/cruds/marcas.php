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
        header("Location: marcas.php");
    } else {
        $pagina = $_GET["pagina"];
    }
} else {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;
$sqlTotal = "SELECT * FROM marcas";
$resultadoTotal = $base->prepare($sqlTotal);
$resultadoTotal->execute();
$numFilas = $resultadoTotal->rowCount();
$totalPaginas = ceil($numFilas / $registrosPorPagina);

// Obtener registros de la tabla
$sqlMarcas = "SELECT * FROM marcas LIMIT $inicio, $registrosPorPagina";
$resultadoMarcas = $base->query($sqlMarcas)->fetchAll(PDO::FETCH_OBJ);

// Insertar nueva Marca
if (isset($_POST['inserta'])) {
    $nombreMarca = $_POST['nombre_marca'];

    // Verificar si la Marca ya existe
    $sqlVerificar = "SELECT COUNT(*) as total FROM marcas WHERE nombre_marca = :nombre_marca";
    $resultadoVerificar = $base->prepare($sqlVerificar);
    $resultadoVerificar->execute(array(":nombre_marca" => $nombreMarca));
    $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

    if ($row['total'] > 0) {
        $_SESSION['message'] = "¡Error! La Marca \"$nombreMarca\" ya existe.";
        $_SESSION['message_type'] = 'error';
    } else {
        // Marca no existe, proceder con la inserción
        $sqlInsert = "INSERT INTO marcas (nombre_marca) VALUES (:nombre_marca)";
        $resultadoInsert = $base->prepare($sqlInsert);
        $resultadoInsert->execute(array(":nombre_marca" => $nombreMarca));

        $_SESSION['message'] = "La Marca \"$nombreMarca\" ha sido registrada correctamente.";
        $_SESSION['message_type'] = 'success';
    }
    header("Location: marcas.php");
    exit();
}

// Obtener datos de la Marca para editar
$MarcaEditar = null;
if (isset($_GET['editar'])) {
    $idMarcaEditar = $_GET['editar'];
    $sqlEditar = "SELECT * FROM marcas WHERE id_marca = :id_marca";
    $resultadoEditar = $base->prepare($sqlEditar);
    $resultadoEditar->execute(array(":id_marca" => $idMarcaEditar));
    $MarcaEditar = $resultadoEditar->fetch(PDO::FETCH_OBJ);
}

// Actualizar Marca
if (isset($_POST['actualiza'])) {
    $idMarca = $_POST['id_marca'];
    $nombreMarca = $_POST['nombre_marca'];
    $sqlUpdate = "UPDATE marcas SET nombre_marca = :nombre_marca WHERE id_marca = :id_marca";
    $resultadoUpdate = $base->prepare($sqlUpdate);
    $resultadoUpdate->execute(array(":id_marca" => $idMarca, ":nombre_marca" => $nombreMarca));

    $_SESSION['message'] = "La Marca \"$nombreMarca\" ha sido actualizada correctamente.";
    $_SESSION['message_type'] = 'success';
    header("Location: marcas.php");
    exit();
}

// Eliminar Marca - Parte 1: Mostrar Formulario
if (isset($_GET['eliminar'])) {
    $idMarcaEliminar = $_GET['eliminar'];
}

// Confirmar y realizar eliminación - Parte 2: Verificar Contraseña
if (isset($_POST['verificar_contraseña'])) {
    $adminPassword = $_POST['admin_password'];
    $idMarcaEliminar = $_POST['id_marca_eliminar'];

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
            let confirmDelete = confirm("¿Está seguro de eliminar la marca?");
            if (confirmDelete) {
                // Enviar la solicitud de eliminación
                let formData = new FormData();
                formData.append('confirmar_eliminar', true);
                formData.append('id_marca_eliminar', '<?php echo htmlspecialchars($idMarcaEliminar); ?>');
                
                fetch('marcas.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    alert(result); // Mostrar mensaje de resultado
                    window.location.href = 'marcas.php'; // Redirigir después de la eliminación
                });
            }
        </script>
        <?php
    } else {
        // Contraseña incorrecta
        echo '<script>alert("Contraseña incorrecta"); window.location.href = "marcas.php";</script>';
    }
    exit();
}

// Confirmar y realizar eliminación - Parte 3: Eliminar Marca
if (isset($_POST['confirmar_eliminar'])) {
    $idMarcaEliminar = $_POST['id_marca_eliminar'];

    // Proceder con la eliminación
    $sqlEliminarConfirmado = "DELETE FROM marcas WHERE id_marca = :id_marca";
    $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
    $resultadoEliminarConfirmado->execute(array(":id_marca" => $idMarcaEliminar));

    echo "La Marca ha sido eliminada correctamente.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Marcas</title>
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
            <div id="marcas" class="section active">
                <h1><span class="color">Gestionar Marcas</span></h1>
                <div class="crud-container">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                        <table align="center" border="" bordercolor="orange" class="crud-table">
                            <tr>
                                <th>ID Marca</th>
                                <th>Nombre Marca</th>
                                <th>Acciones</th>
                            </tr>
                        <tbody id="crud-table-body">
                            <?php foreach ($resultadoMarcas as $marca) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($marca->id_marca); ?></td>
                                    <td><?php echo htmlspecialchars($marca->nombre_marca); ?></td>
                                    <td>
                                        <a href="?editar=<?php echo htmlspecialchars($marca->id_marca); ?>" class="buttonA" style="margin-right: 10px;">Editar</a>
                                        <a href="?eliminar=<?php echo htmlspecialchars($marca->id_marca); ?>" class="buttonE" style="margin-left: 5px;">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($MarcaEditar) : ?>
                            <tr>
                                <form method="post" action="">
                                    <td><input type="hidden" name="id_marca" value="<?php echo htmlspecialchars($MarcaEditar->id_marca); ?>"></td>
                                    <td><input type="text" name="nombre_marca" value="<?php echo htmlspecialchars($MarcaEditar->nombre_marca); ?>"></td>
                                    <td>
                                        <button type="submit" name="actualiza" class="button">Actualizar</button>
                                    </td>
                                </form>
                            </tr>
                            <?php else : ?>
                            <tr>
                                <form method="post" action="">
                                    <td></td>
                                    <td><input type="text" name="nombre_marca" placeholder="Nombre de la Marca" class="inputAgregar"></td>
                                    <td>
                                        <button type="submit" name="inserta" class="button">Agregar Marca</button>
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
                    <!-- Formulario de Contraseña para eliminar Marca -->
                    <?php if (isset($_GET['eliminar']) && !isset($_POST['verificar_contraseña'])): ?>
                    <div id="confirm-delete" style="padding: 20px;">
                        <form id="delete-form" method="post" action="">
                            <input type="hidden" name="id_marca_eliminar" value="<?php echo htmlspecialchars($idMarcaEliminar); ?>">
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
    <script>
        document.getElementById("logout-button").addEventListener("click", function() {
            document.getElementById("logout-form").submit();
        });
    </script>
</body>
</html>
