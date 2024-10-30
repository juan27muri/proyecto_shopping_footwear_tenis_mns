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
        header("Location: categorias.php");
    } else {
        $pagina = $_GET["pagina"];
    }
} else {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registrosPorPagina;
$sqlTotal = "SELECT * FROM categorías";
$resultadoTotal = $base->prepare($sqlTotal);
$resultadoTotal->execute();
$numFilas = $resultadoTotal->rowCount();
$totalPaginas = ceil($numFilas / $registrosPorPagina);

// Obtener registros de la tabla
$sqlCategorias = "SELECT * FROM categorías LIMIT $inicio, $registrosPorPagina";
$resultadoCategorias = $base->query($sqlCategorias)->fetchAll(PDO::FETCH_OBJ);

// Insertar nueva Categoría
if (isset($_POST['inserta'])) {
    $nombreCategoria = $_POST['nombre_categoria'];
    $descripcionCategoria = $_POST['descripcion_categoria'];

    // Verificar si la Categoría ya existe
    $sqlVerificar = "SELECT COUNT(*) as total FROM categorías WHERE nombre_categoria = :nombre_categoria";
    $resultadoVerificar = $base->prepare($sqlVerificar);
    $resultadoVerificar->execute(array(":nombre_categoria" => $nombreCategoria));
    $row = $resultadoVerificar->fetch(PDO::FETCH_ASSOC);

    if ($row['total'] > 0) {
        $_SESSION['message'] = "¡Error! La Categoría \"$nombreCategoria\" ya existe.";
        $_SESSION['message_type'] = 'error';
    } else {
        // Categoría no existe, proceder con la inserción
        $sqlInsert = "INSERT INTO categorías (nombre_categoria, descripcion_categoria) VALUES (:nombre_categoria, :descripcion_categoria)";
        $resultadoInsert = $base->prepare($sqlInsert);
        $resultadoInsert->execute(array(":nombre_categoria" => $nombreCategoria, ":descripcion_categoria" => $descripcionCategoria));

        $_SESSION['message'] = "La Categoría \"$nombreCategoria\" ha sido registrada correctamente.";
        $_SESSION['message_type'] = 'success';
    }
    header("Location: categorias.php");
    exit();
}

// Obtener datos de la Categoría para editar
$CategoriaEditar = null;
if (isset($_GET['editar'])) {
    $idCategoriaEditar = $_GET['editar'];
    $sqlEditar = "SELECT * FROM categorías WHERE id_categoria = :id_categoria";
    $resultadoEditar = $base->prepare($sqlEditar);
    $resultadoEditar->execute(array(":id_categoria" => $idCategoriaEditar));
    $CategoriaEditar = $resultadoEditar->fetch(PDO::FETCH_OBJ);
}

// Actualizar Categoría
if (isset($_POST['actualiza'])) {
    $idCategoria = $_POST['id_categoria'];
    $nombreCategoria = $_POST['nombre_categoria'];
    $descripcionCategoria = $_POST['descripcion_categoria'];
    $sqlUpdate = "UPDATE categorías SET nombre_categoria = :nombre_categoria, descripcion_categoria = :descripcion_categoria WHERE id_categoria = :id_categoria";
    $resultadoUpdate = $base->prepare($sqlUpdate);
    $resultadoUpdate->execute(array(":id_categoria" => $idCategoria, ":nombre_categoria" => $nombreCategoria, ":descripcion_categoria" => $descripcionCategoria));

    $_SESSION['message'] = "La Categoría \"$nombreCategoria\" ha sido actualizada correctamente.";
    $_SESSION['message_type'] = 'success';
    header("Location: categorias.php");
    exit();
}

// Eliminar Categoría - Parte 1: Mostrar Formulario
if (isset($_GET['eliminar'])) {
    $idCategoriaEliminar = $_GET['eliminar'];
}

// Confirmar y realizar eliminación - Parte 2: Verificar Contraseña
if (isset($_POST['verificar_contraseña'])) {
    $adminPassword = $_POST['admin_password'];
    $idCategoriaEliminar = $_POST['id_categoria_eliminar'];

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
            let confirmDelete = confirm("¿Está seguro de eliminar la categoría?");
            if (confirmDelete) {
                // Enviar la solicitud de eliminación
                let formData = new FormData();
                formData.append('confirmar_eliminar', true);
                formData.append('id_categoria_eliminar', '<?php echo htmlspecialchars($idCategoriaEliminar); ?>');
                
                fetch('categorias.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    alert(result); // Mostrar mensaje de resultado
                    window.location.href = 'categorias.php'; // Redirigir después de la eliminación
                });
            }
        </script>
        <?php
    } else {
        // Contraseña incorrecta
        echo '<script>alert("Contraseña incorrecta"); window.location.href = "categorias.php";</script>';
    }
    exit();
}

// Confirmar y realizar eliminación - Parte 3: Eliminar Categoría
if (isset($_POST['confirmar_eliminar'])) {
    $idCategoriaEliminar = $_POST['id_categoria_eliminar'];

    // Proceder con la eliminación
    $sqlEliminarConfirmado = "DELETE FROM categorias WHERE id_categoria = :id_categoria";
    $resultadoEliminarConfirmado = $base->prepare($sqlEliminarConfirmado);
    $resultadoEliminarConfirmado->execute(array(":id_categoria" => $idCategoriaEliminar));

    echo "La Categoría ha sido eliminada correctamente.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Categorías</title>
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
            <div id="categorias" class="section active">
                <h1><span class="color">Gestionar Categorías</span></h1>
                <div class="crud-container">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                        <table align="center" border="" bordercolor="orange" class="crud-table">
                            <tr>
                                <th>ID Categoría</th>
                                <th>Nombre Categoría</th>
                                <th>Descripción Categoría</th>
                                <th>Acciones</th>
                            </tr>
                        <tbody id="crud-table-body">
                            <?php foreach ($resultadoCategorias as $categoria) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($categoria->id_categoria); ?></td>
                                    <td><?php echo htmlspecialchars($categoria->nombre_categoria); ?></td>
                                    <td><?php echo htmlspecialchars($categoria->descripcion_categoria); ?></td>
                                    <td>
                                        <a href="?editar=<?php echo htmlspecialchars($categoria->id_categoria); ?>" class="buttonA" style="margin-right: 10px;">Editar</a>
                                        <a href="?eliminar=<?php echo htmlspecialchars($categoria->id_categoria); ?>" class="buttonE" style="margin-left: 5px;">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if ($CategoriaEditar) : ?>
                            <tr>
                                <form method="post" action="">
                                    <td><input type="hidden" name="id_categoria" value="<?php echo htmlspecialchars($CategoriaEditar->id_categoria); ?>"></td>
                                    <td><input type="text" name="nombre_categoria" value="<?php echo htmlspecialchars($CategoriaEditar->nombre_categoria); ?>"></td>
                                    <td><input type="text" name="descripcion_categoria" value="<?php echo htmlspecialchars($CategoriaEditar->descripcion_categoria); ?>"></td>
                                    <td>
                                        <button type="submit" name="actualiza" class="button">Actualizar</button>
                                    </td>
                                </form>
                            </tr>
                            <?php else : ?>
                            <tr>
                                <form method="post" action="">
                                    <td></td>
                                    <td><input type="text" name="nombre_categoria" placeholder="Nombre de la Categoría" class="inputAgregar"></td>
                                    <td><input type="text" name="descripcion_categoria" placeholder="Descripción de la Categoría" class="inputAgregar"></td>
                                    <td>
                                        <button type="submit" name="inserta" class="button">Agregar Categoría</button>
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
                    <!-- Formulario de Contraseña para eliminar Categoría -->
                    <?php if (isset($_GET['eliminar']) && !isset($_POST['verificar_contraseña'])): ?>
                    <div id="confirm-delete" style="padding: 20px;">
                        <form id="delete-form" method="post" action="">
                            <input type="hidden" name="id_categoria_eliminar" value="<?php echo htmlspecialchars($idCategoriaEliminar); ?>">
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
