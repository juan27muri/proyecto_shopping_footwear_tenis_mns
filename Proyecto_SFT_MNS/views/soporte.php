<?php
session_start();
require '../config/database.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre_usuario'];

// Obtener los soportes del cliente actual
$usuario_id = $_SESSION['usuario_id'];
$query_soportes = "SELECT numero_soporte, tipo_soporte, fecha_soporte, descripcion_soporte, estado_soporte, respuesta_admin
                   FROM soporte
                   WHERE usuario_id = :usuario_id
                   ORDER BY fecha_soporte DESC";
$stmt_soportes = $base->prepare($query_soportes);
$stmt_soportes->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_soportes->execute();
$soportes = $stmt_soportes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Footwear Tenis MNS - Soporte</title> 
    <link rel="stylesheet" href="../css/soporte.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
    <style>
        
        
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>ㅤShopping Footwear Tenis MNS</h1> <br> 
            </div>
            <div class="search-bar">
            ㅤㅤㅤ<input type="text" id="search-bar" placeholder="Buscar productos...">ㅤ
                <form action="index_cliente.php"><button id="search-button">Buscar</button></form>
                <a href="carrito.php">
                    <div class="move-car">
                        <img src="../imagenes/carrito.png" alt="Carrito" width="30px" height="30px">
                    
                </a>
            </div>

            <div class="user-options">
                <span>Usuario: <?php echo htmlspecialchars($nombreUsuario); ?></span>
                <a href="mi_perfil.php">Mi Perfil</a>
                <a href="soporte.php">Soporte</a>
                <a href="index_cliente.php">Productos</a>
            </div>ㅤ
            <div class="move-sesion">
                <form id="logout-form" method="POST" action="cerrar_sesion.php">
                    <button type="button" id="logout-button" class="buttonC">Cerrar sesión</button>
                </form>
            </div> <br> 
        </div>
    </header><br>

    <div class="support-container">
        <!-- Formulario de soporte -->
        <div class="support-form">
            <h2>Formulario de Soporte</h2>
            <form action="../functions_cliente/procesar_soporte.php" method="POST">
                <label for="tipo_soporte">Tipo de Soporte:</label>
                <select id="tipo_soporte" name="tipo_soporte" required>
                    <option value="Queja">Queja</option>
                    <option value="Rembolso">Rembolso</option>
                    <option value="Cancelar Pedido">Cancelar Pedido</option>
                    <option value="Otro">Otro</option>
                </select>

                <label for="descripcion_soporte">Descripción del Soporte:</label>
                <textarea id="descripcion_soporte" name="descripcion_soporte" rows="4" required></textarea>

                <input type="submit" value="Enviar Soporte">
            </form>
            <!-- Mensaje de nota -->
            <p> <b>NOTA:</b> Si tienes un inconveniente con un pedido, digita el número del pedido en la descripción.</p>
        </div>
<br><br>
        <!-- Lista de soportes -->
        <div class="support-list">
            <h2>Historial de Soportes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Número de Soporte</th>
                        <th>Tipo de Soporte</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($soportes)): ?>
                        <tr>
                            <td colspan="5">No se encontraron soportes.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($soportes as $soporte): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($soporte['numero_soporte']); ?></td>
                                <td><?php echo htmlspecialchars($soporte['tipo_soporte']); ?></td>
                                <td><?php echo htmlspecialchars($soporte['fecha_soporte']); ?></td>
                                <td><?php echo htmlspecialchars($soporte['estado_soporte']); ?></td>
                                <td>
                                    <button class="view-request-button" onclick="toggleDetails('<?php echo htmlspecialchars($soporte['numero_soporte']); ?>')">Ver Petición</button>
                                    <div id="details-<?php echo htmlspecialchars($soporte['numero_soporte']); ?>" class="details-container">
                                        <p><strong>Tipo de Soporte:</strong> <?php echo htmlspecialchars($soporte['tipo_soporte']); ?></p><br>
                                        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($soporte['descripcion_soporte']); ?></p><br>
                                        <p><strong>Fecha:</strong> <?php echo htmlspecialchars($soporte['fecha_soporte']); ?></p><br>
                                        <p><strong>Estado:</strong> <?php echo htmlspecialchars($soporte['estado_soporte']); ?></p><br>
                                        <p><strong>Respuesta del Administrador:</strong> <?php echo htmlspecialchars($soporte['respuesta_admin']) ?: 'Sin respuesta'; ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../js/pag_principal_cliente.js"></script>
    <script>


        function toggleDetails(soporteId) {
            const details = document.getElementById('details-' + soporteId);
            if (details.classList.contains('active')) {
                details.classList.remove('active');
            } else {
                details.classList.add('active');
            }
        }

        const urlParams = new URLSearchParams(window.location.search);
        const mensaje = urlParams.get('mensaje');
        if (mensaje) {
            alert(mensaje);
        }
    </script>

    <footer>
        <div class="container">
            <p>&copy; 2024 Shopping Footwear Tenis MNS. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>
