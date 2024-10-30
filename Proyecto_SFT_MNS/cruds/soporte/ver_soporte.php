<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
require '../../config/database.php';

$numeroSoporte = isset($_GET['numero_soporte']) ? $_GET['numero_soporte'] : '';

if (empty($numeroSoporte)) {
    header('Location: soporte.php?message=No%20se%20ha%20especificado%20ningún%20número%20de%20soporte');
    exit();
}

// Consultar los detalles del soporte
$sqlSoporte = "SELECT s.*, u.nombre_usuario, u.apellido_usuario, u.id_usuario
               FROM soporte s
               LEFT JOIN usuarios u ON s.usuario_id = u.id_usuario
               WHERE s.numero_soporte = :numero_soporte";
$stmtSoporte = $base->prepare($sqlSoporte);
$stmtSoporte->execute([':numero_soporte' => $numeroSoporte]);
$soporte = $stmtSoporte->fetch(PDO::FETCH_OBJ);

if (!$soporte) {
    header('Location: soporte.php?message=No%20se%20encontró%20el%20soporte%20con%20ese%20número');
    exit();
}

// Actualizar respuesta del admin y estado si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $estadoSoporte = $_POST['estado_soporte'];
    $respuestaAdmin = $_POST['respuesta_admin'];

    $sqlUpdate = "UPDATE soporte SET estado_soporte = :estado_soporte, respuesta_admin = :respuesta_admin
                  WHERE numero_soporte = :numero_soporte";
    $stmtUpdate = $base->prepare($sqlUpdate);
    $stmtUpdate->execute([
        ':estado_soporte' => $estadoSoporte,
        ':respuesta_admin' => $respuestaAdmin,
        ':numero_soporte' => $numeroSoporte
    ]);

    // Redirige a soporte.php después de la actualización
    header('Location: soporte.php?message=Soporte%20actualizado%20correctamente');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Petición de Soporte</title>
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
            <div id="soporte" class="section active">
                <h1><span class="color">Detalles del Soporte</span></h1>
                <div class="crud-container">
                    <div class="soporte-details">
                        <p><strong>Número de Soporte:</strong> <?php echo htmlspecialchars($soporte->numero_soporte); ?></p>
                        <p><strong>Tipo de Soporte:</strong> <?php echo htmlspecialchars($soporte->tipo_soporte); ?></p>
                        <p><strong>Descripción del Soporte:</strong> <?php echo htmlspecialchars($soporte->descripcion_soporte); ?></p>
                        <p><strong>Fecha de Soporte:</strong> <?php echo htmlspecialchars($soporte->fecha_soporte); ?></p>
                        <p><strong>Estado:</strong> <?php echo htmlspecialchars($soporte->estado_soporte); ?></p>
                        <p><strong>Respuesta del Admin:</strong> <?php echo htmlspecialchars($soporte->respuesta_admin) ? htmlspecialchars($soporte->respuesta_admin) : 'Sin respuesta'; ?></p>
                        <p><strong>Identificación de Cliente:</strong> <?php echo htmlspecialchars($soporte->id_usuario); ?></p>
                        <p><strong>Nombre Cliente:</strong> <?php echo htmlspecialchars($soporte->nombre_usuario); ?></p>
                        <p><strong>Apellido Cliente:</strong> <?php echo htmlspecialchars($soporte->apellido_usuario); ?></p>
                    </div>

                    <!-- Formulario para actualizar el soporte -->
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="estado_soporte"><strong>Estado:</strong></label>
                            <select id="estado_soporte" name="estado_soporte" required>
                                <option value="Pendiente" <?php echo ($soporte->estado_soporte === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="En Proceso" <?php echo ($soporte->estado_soporte === 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
                                <option value="Resuelto" <?php echo ($soporte->estado_soporte === 'Resuelto') ? 'selected' : ''; ?>>Resuelto</option>
                                <option value="Rechazado" <?php echo ($soporte->estado_soporte === 'Rechazado') ? 'selected' : ''; ?>>Rechazado</option>
                            </select>
                        </div><br>
                        <div class="form-group">
                            <label for="respuesta_admin"><strong>Respuesta del Admin:</strong></label><br>
                            <textarea id="respuesta_admin" name="respuesta_admin" rows="4" required><?php echo htmlspecialchars($soporte->respuesta_admin); ?></textarea>
                        </div>
                        <button type="submit" class="button">Actualizar Soporte</button>
                    </form>
                    
                    <a href="soporte.php" class="button">Volver</a>
                </div>
            </div>
        </main>
    </div>

    <script src="../../js/admin_Dashboard.js"></script>
</body>
</html>
