<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
require '../../config/database.php';

// Paginación
$registrosPorPagina = 6;
$pagina = isset($_GET["pagina"]) ? $_GET["pagina"] : 1;
$inicio = ($pagina - 1) * $registrosPorPagina;

// Filtro de búsqueda
$searchFilter = isset($_GET['search']) ? $_GET['search'] : '';

// Consulta para contar el número total de soportes (con o sin filtro)
$sqlTotal = "SELECT COUNT(*) FROM soporte 
              WHERE numero_soporte LIKE :search";
$stmtTotal = $base->prepare($sqlTotal);
$stmtTotal->execute([
    ':search' => '%' . $searchFilter . '%'
]);
$numFilas = $stmtTotal->fetchColumn();
$totalPaginas = ceil($numFilas / $registrosPorPagina);

// Obtener registros de la tabla (con o sin filtro)
$sqlSoportes = "SELECT * FROM soporte 
                WHERE numero_soporte LIKE :search 
                ORDER BY estado_soporte = 'pendiente' DESC, fecha_soporte DESC
                LIMIT $inicio, $registrosPorPagina";
$stmtSoportes = $base->prepare($sqlSoportes);
$stmtSoportes->execute([
    ':search' => '%' . $searchFilter . '%'
]);
$resultadoSoportes = $stmtSoportes->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Soportes</title>
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
            <a href="../../cruds/ciudades.php" onclick="showSection('ciudades')">Crud Ciudades</a>
            <a href="../../cruds/categorias.php" onclick="showSection('categorias')">Crud Categorías</a>
            <a href="../../cruds/marcas.php" onclick="showSection('marcas')">Crud Marcas</a>
            <a href="../../cruds/productos/productos.php" onclick="showSection('productos')">Crud Productos</a>
            <a href="../../cruds/usuarios/usuarios.php" onclick="showSection('usuarios')">Crud Usuarios</a>
            <a href="../../cruds/pedidos/pedidos.php" onclick="showSection('pedidos')">Pedidos</a>
            <a href="../../cruds/soporte/soporte.php" onclick="showSection('soporte')">Soporte</a>
            <a href="../../views/reportes.php" onclick="showSection('reportes')">Reportes</a>

        </div>

        <main id="main-content">
            <div id="soportes" class="section active">
                <h1><span class="color">Gestionar Soportes</span></h1>
                <div class="crud-container">
                <form method="get" action="">
                    <input type="text" name="search" placeholder="Buscar por número de soporte" value="<?php echo htmlspecialchars($searchFilter); ?>">
                    <button type="submit" class="button">Buscar</button>
                </form><br>
                    <table align="center" border="" bordercolor="orange" class="crud-table">
                        <tr>
                            <th>Número de Soporte</th>
                            <th>Tipo de Soporte</th>
                            <th>Fecha de Soporte</th>
                            <th>Estado Soporte</th>
                            <th>Acciones</th>
                        </tr>
                        <tbody id="crud-table-body">
                            <?php foreach ($resultadoSoportes as $soporte) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($soporte->numero_soporte); ?></td>
                                    <td><?php echo htmlspecialchars($soporte->tipo_soporte); ?></td>
                                    <td><?php echo htmlspecialchars($soporte->fecha_soporte); ?></td>
                                    <td><?php echo htmlspecialchars($soporte->estado_soporte); ?></td>
                                    <td>
                                        <a href="ver_soporte.php?numero_soporte=<?php echo htmlspecialchars($soporte->numero_soporte); ?>" class="button">Ver Petición</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="pagination">
                        <?php if ($pagina > 1) : ?>
                            <li><a href="?pagina=<?php echo $pagina - 1; ?>&search=<?php echo htmlspecialchars($searchFilter); ?>" class="button">Anterior</a></li>
                        <?php endif; ?>

                        <?php if ($pagina < $totalPaginas) : ?>
                            <li><a href="?pagina=<?php echo $pagina + 1; ?>&search=<?php echo htmlspecialchars($searchFilter); ?>" class="button">Siguiente</a></li>
                        <?php endif; ?>
                    </div>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener el mensaje de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        
        if (message) {
            // Mostrar el mensaje en una alerta
            alert(decodeURIComponent(message));
        }
    });
    </script>
</body>
</html>
