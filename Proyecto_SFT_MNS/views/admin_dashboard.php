<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    // Redirigir al índice con un mensaje
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador</title>
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
            <form id="logout-form" method="POST" action="cerrar_sesion.php">
                <button type="button" id="logout-button" class="buttonC">Cerrar sesión</button>
            </form>
        </div>
    </header>
    
    <div class="admin-container">
        <div class="sidebar" id="sidebar">
            <a href="#inicio" onclick="showSection('inicio')">Inicio</a>
            <a href="../cruds/roles.php" onclick="showSection('roles')">Crud Roles</a>
            <a href="../cruds/departamentos.php" onclick="showSection('departamentos')">Crud Departamentos</a>
            <a href="../cruds/ciudades.php" onclick="showSection('ciudades')">Crud Ciudades</a>
            <a href="../cruds/categorias.php" onclick="showSection('categorias')">Crud Categorias</a>
            <a href="../cruds/marcas.php" onclick="showSection('marcas')">Crud Marcas</a>
            <a href="../cruds/productos/productos.php" onclick="showSection('productos')">Crud Productos</a>
            <a href="../cruds/usuarios/usuarios.php" onclick="showSection('usuarios')">Crud Usuarios</a>
            <a href="../cruds/pedidos/pedidos.php" onclick="showSection('usuarios')">Pedidos</a>
            <a href="../cruds/soporte/soporte.php" onclick="showSection('usuarios')">Soporte</a>
            <a href="reportes.php" onclick="showSection('reportes')">Reportes</a>
        </div>

        <main id="main-content">
            <div id="inicio" class="section active">
                <h1><span class="color">Bienvenido al Dashboard del Administrador</span></h1>
                <div class="dashboard-grid">
                    <div class="dashboard-card" onclick="window.location.href='../cruds/roles.php'">
                        <h3>Gestionar Roles</h3>
                    </div>
                    <div class="dashboard-card_1" onclick="window.location.href='../cruds/departamentos.php'">
                        <h3>Gestionar Departamentos</h3>
                    </div>
                    <div class="dashboard-card_2" onclick="window.location.href='../cruds/ciudades.php'">
                        <h3>Gestionar Ciudades</h3>
                    </div>
                    <div class="dashboard-card_3" onclick="window.location.href='../cruds/categorias.php'">
                        <h3>Gestionar Categorias</h3>
                    </div>
                    <div class="dashboard-card_4" onclick="window.location.href='../cruds/marcas.php'">
                        <h3>Gestionar Marcas</h3>
                    </div>
                    <div class="dashboard-card_5" onclick="window.location.href='../cruds/productos/productos.php'">
                        <h3>Gestionar Productos</h3>
                    </div>
                    <div class="dashboard-card_6" onclick="window.location.href='../cruds/usuarios/usuarios.php'">
                        <h3>Gestionar Usuarios</h3>
                    </div>
                    <div class="dashboard-card_7" onclick="window.location.href='../cruds/pedidos/pedidos.php'">
                        <h3>Gestionar Pedidos</h3>
                    </div>
                    <div class="dashboard-card_8" onclick="window.location.href='../cruds/soporte/soporte.php'">
                        <h3>Gestionar Soportes</h3>
                    </div>
                    <div class="dashboard-card_8" onclick="window.location.href='reportes.php'">
                        <h3>Reportes</h3>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/admin_Dashboard.js"></script>
</body>
</html>