<?php
session_start();
require '../config/database.php';

// AJAX handler para cargar ciudades
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_request'])) {
    $departamento_id = $_POST['departamento_id'];

    $sql = "SELECT id_ciudad, nombre_ciudad FROM ciudades WHERE departamento_id = :departamento_id";
    $stmt = $base->prepare($sql);
    $stmt->execute([':departamento_id' => $departamento_id]);

    $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ciudades);
    exit();
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}

// Obtener datos del usuario
$sqlUsuario = "SELECT 
    nombre_usuario, 
    apellido_usuario, 
    email_usuario, 
    direccion_usuario, 
    telefono_usuario, 
    ciudad_id, 
    departamento_id
FROM usuarios 
WHERE id_usuario = :usuario_id";
$stmt = $base->prepare($sqlUsuario);
$stmt->execute([':usuario_id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die('Usuario no encontrado.');
}

$nombreUsuario = $usuario['nombre_usuario'] ?? '';
$apellidoUsuario = $usuario['apellido_usuario'] ?? '';
$correoUsuario = $usuario['email_usuario'] ?? '';
$telefonoUsuario = $usuario['telefono_usuario'] ?? '';
$direccionUsuario = $usuario['direccion_usuario'] ?? '';
$ciudadIdUsuario = $usuario['ciudad_id'] ?? '';
$departamentoIdUsuario = $usuario['departamento_id'] ?? '';

// Obtener departamentos para el select
$sqlDepartamentos = "SELECT * FROM departamentos";
$departamentos = $base->query($sqlDepartamentos)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta</title>
    <link rel="stylesheet" href="../css/mi_perfil1.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <div class="header-container">
            <nav>
                <ul>
                    <li><a href="index_cliente.php">Productos</a></li>
                    <li><a href="mi_perfil.php" class="active">Mi Cuenta</a></li>
                    <li><a href="Carrito.php"><div class="move-car"><img src="../imagenes/carrito.png" alt="" width="30px" height="30px"></a></li>
                </ul>
            </nav>
        </div>
    </header> <br> <br>

    <main>
        <section class="account-section">
            <h1>Mi Cuenta</h1>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($nombreUsuario . ' ' . $apellidoUsuario); ?></h2>
                <p>Correo Electrónico: <?php echo htmlspecialchars($correoUsuario); ?></p>
                <p>Teléfono: <?php echo htmlspecialchars($telefonoUsuario); ?></p>
                <p>Dirección: <?php echo htmlspecialchars($direccionUsuario); ?></p>
            </div>

            <div class="update-form">
                <h2>Actualizar Información</h2>
                <form action="actualizar_perfil.php" method="POST" enctype="multipart/form-data">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombreUsuario); ?>" required>

                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellidoUsuario); ?>" required>

                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($correoUsuario); ?>" required>

                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefonoUsuario); ?>" required>

                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccionUsuario); ?>" required>

                    <label for="departamento">Departamento:</label>
                    <select id="departamento_id" name="departamento" required>
                        <?php foreach ($departamentos as $departamento): ?>
                            <option value="<?php echo htmlspecialchars($departamento['id_departamento']); ?>" 
                                <?php if ($departamento['id_departamento'] == $departamentoIdUsuario) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($departamento['nombre_departamento']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="ciudad">Ciudad:</label>
                    <select id="ciudad_id" name="ciudad" required>
                        <!-- Las ciudades se cargarán aquí mediante AJAX -->
                    </select>

                    <!-- Campos para cambiar contraseña -->
        <h3>Cambiar Contraseña</h3>
        <label for="current_password">Contraseña Actual:</label>
        <input type="password" id="current_password" name="current_password">

        <label for="new_password">Nueva Contraseña:</label>
        <input type="password" id="new_password" name="new_password">

        <label for="confirm_password">Confirmar Nueva Contraseña:</label>
        <input type="password" id="confirm_password" name="confirm_password">

        <button type="submit">Actualizar</button>
                </form>
            </div>
        </section>
    </main>

    <footer> <br>
        <div class="container">
            <p>&copy; 2024 Shopping Footwear Tenis MNS. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        function cargarCiudades() {
            var departamentoId = document.getElementById("departamento_id").value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "mi_perfil.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var ciudades = JSON.parse(xhr.responseText);
                    var ciudadSelect = document.getElementById("ciudad_id");
                    ciudadSelect.innerHTML = "";

                    ciudades.forEach(function(ciudad) {
                        var option = document.createElement("option");
                        option.value = ciudad.id_ciudad;
                        option.text = ciudad.nombre_ciudad;
                        ciudadSelect.appendChild(option);
                    });

                    // Seleccionar la ciudad del usuario si está disponible
                    if (ciudadIdUsuario) {
                        ciudadSelect.value = ciudadIdUsuario;
                    }
                }
            };

            xhr.send("ajax_request=true&departamento_id=" + departamentoId);
        }

        document.getElementById("departamento_id").addEventListener("change", cargarCiudades);

        // Inicializar la carga de ciudades al cargar la página
        window.onload = function() {
            cargarCiudades();

            // Mostrar mensaje de éxito si está presente en la URL
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            
            if (message) {
                alert(decodeURIComponent(message));
            }
        };
    </script>
</body>
</html>
