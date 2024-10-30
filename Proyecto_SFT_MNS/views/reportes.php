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
    <title>Reportes</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <!-- Menú de navegación -->
            <a href="admin_dashboard.php" onclick="showSection('inicio')">Inicio</a>
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
            <div id="reportes" class="section active">
                <h1>Reportes</h1>
                <div class="report-container">
                    <h2>Pedidos por Día</h2>
                    <canvas id="pedidosPorDia"></canvas>

                    <h2>Ganancias por Día</h2>
                    <canvas id="gananciasPorDia"></canvas>

                    <h2>Pedidos por Mes</h2>
                    <canvas id="pedidosPorMes"></canvas>

                    <h2>Ganancias por Mes</h2>
                    <canvas id="gananciasPorMes"></canvas>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Cargar los reportes mediante AJAX
        function cargarReportes() {
            fetch('obtener_reportes.php')
                .then(response => response.json())
                .then(data => {
                    mostrarGraficos(data);
                });
        }

        // Mostrar los gráficos utilizando Chart.js
        function mostrarGraficos(data) {
            // Pedidos por Día
            new Chart(document.getElementById('pedidosPorDia'), {
                type: 'line',
                data: {
                    labels: data.pedidosPorDia.map(item => item.fecha),
                    datasets: [{
                        label: 'Pedidos por Día',
                        data: data.pedidosPorDia.map(item => item.total_pedidos),
                        borderColor: 'blue',
                        fill: false
                    }]
                }
            });

            // Ganancias por Día
            new Chart(document.getElementById('gananciasPorDia'), {
                type: 'line',
                data: {
                    labels: data.gananciasPorDia.map(item => item.fecha),
                    datasets: [{
                        label: 'Ganancias por Día',
                        data: data.gananciasPorDia.map(item => item.total_ganancias),
                        borderColor: 'green',
                        fill: false
                    }]
                }
            });

            // Pedidos por Mes
            new Chart(document.getElementById('pedidosPorMes'), {
                type: 'bar',
                data: {
                    labels: data.pedidosPorMes.map(item => item.fecha),
                    datasets: [{
                        label: 'Pedidos por Mes',
                        data: data.pedidosPorMes.map(item => item.total_pedidos),
                        backgroundColor: 'orange'
                    }]
                }
            });

            // Ganancias por Mes
            new Chart(document.getElementById('gananciasPorMes'), {
                type: 'bar',
                data: {
                    labels: data.gananciasPorMes.map(item => item.fecha),
                    datasets: [{
                        label: 'Ganancias por Mes',
                        data: data.gananciasPorMes.map(item => item.total_ganancias),
                        backgroundColor: 'purple'
                    }]
                }
            });
        }

        // Llamar a la función para cargar los reportes cuando se cargue la página
        document.addEventListener('DOMContentLoaded', cargarReportes);
    </script>
</body>
</html>
