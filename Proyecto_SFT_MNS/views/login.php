<?php
require '../config/database.php';
session_start();

$mensaje = '';

// Manejo del registro
if (isset($_POST['registro'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $apellido_usuario = $_POST['apellido_usuario'];
    $email_usuario = $_POST['email_usuario'];
    $direccion_usuario = $_POST['direccion_usuario'];
    $telefono_usuario = $_POST['telefono_usuario'];
    $contraseña_usuario = $_POST['contraseña_usuario'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];
    $departamento_id = $_POST['departamento_id'];
    $ciudad_id = $_POST['ciudad_id'];

    // Verificar si las contraseñas coinciden
    if ($contraseña_usuario != $confirmar_contraseña) {
        echo '<script>alert("Las contraseñas no coinciden");</script>';
    } else {
        // Verificar si el ID de usuario ya existe
        $sql = "SELECT COUNT(*) FROM usuarios WHERE id_usuario = :id_usuario";
        $stmt = $base->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo '<script>alert("El número de identificación ya está en uso");</script>';
        } else {
            // Verificar si el correo electrónico ya está registrado
            $sql = "SELECT COUNT(*) FROM usuarios WHERE email_usuario = :email_usuario";
            $stmt = $base->prepare($sql);
            $stmt->execute([':email_usuario' => $email_usuario]);
            $countEmail = $stmt->fetchColumn();

            if ($countEmail > 0) {
                echo '<script>alert("El correo electrónico ya está en uso");</script>';
            } else {
                // Registrar el nuevo usuario
                $sql = "INSERT INTO usuarios (id_usuario, nombre_usuario, apellido_usuario, email_usuario, contraseña_usuario, direccion_usuario, telefono_usuario, rol_id, ciudad_id, departamento_id) 
                        VALUES (:id_usuario, :nombre_usuario, :apellido_usuario, :email_usuario, :contrasena_usuario, :direccion_usuario, :telefono_usuario, :rol_id, :ciudad_id, :departamento_id)";
                $stmt = $base->prepare($sql);
                $stmt->execute([
                    ':id_usuario' => $id_usuario,
                    ':nombre_usuario' => $nombre_usuario,
                    ':apellido_usuario' => $apellido_usuario,
                    ':email_usuario' => $email_usuario,
                    ':contrasena_usuario' => password_hash($contraseña_usuario, PASSWORD_DEFAULT), // Encriptar la contraseña
                    ':direccion_usuario' => $direccion_usuario,
                    ':telefono_usuario' => $telefono_usuario,
                    ':rol_id' => 2, // Asignar el rol de cliente
                    ':ciudad_id' => $ciudad_id,
                    ':departamento_id' => $departamento_id
                ]);
                echo '<script>alert("Has sido registrado correctamente");</script>';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_request'])) {
    $departamento_id = $_POST['departamento_id'];

    $sql = "SELECT id_ciudad, nombre_ciudad FROM ciudades WHERE departamento_id = :departamento_id";
    $stmt = $base->prepare($sql);
    $stmt->execute([':departamento_id' => $departamento_id]);

    $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ciudades);
    exit();
}

// Manejo del inicio de sesión
if (isset($_POST['login'])) {
    $id_usuario = $_POST['identificacion'];
    $contraseña = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
    $stmt = $base->prepare($sql);
    $stmt->execute([':id_usuario' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contraseña, $usuario['contraseña_usuario'])) {
        if ($usuario['rol_id'] == 2) { // Verificar si el rol es de cliente
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            header("Location: index_cliente.php"); // Redirigir al dashboard del cliente
            exit();
        } else {
            $error = 'Identificación o contraseña incorrectos';
        }
    } else {
        $error = 'Identificación o contraseña incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/login_style1.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
    <title>Login</title>
</head>
<body>
<div class="container" id="container">
    <?php if ($mensaje): ?>
        <div class="alert">
            <?= $mensaje ?>
        </div>
    <?php endif; ?>
    <div class="form-container sign-up">
        <form method="POST" action="login.php" id="registerForm">
            <h1>Registrate</h1>
            <input type="number" name="id_usuario" placeholder="Numero de Identificación" required>
            <input type="text" name="nombre_usuario" placeholder="Nombre" required>
            <input type="text" name="apellido_usuario" placeholder="Apellido" required>
            <input type="email" name="email_usuario" placeholder="Email" required>
            <input type="text" name="direccion_usuario" placeholder="Direccion">
            <select id="departamento_id" name="departamento_id" onchange="cargarCiudades()" required>
                <!-- Opciones de departamentos -->
                <option value="" disabled selected>Departamento</option>
                <?php
                $resultadoDepartamentos = $base->query("SELECT id_departamento, nombre_departamento FROM departamentos")->fetchAll(PDO::FETCH_OBJ);
                foreach ($resultadoDepartamentos as $departamento) {
                    echo "<option value='$departamento->id_departamento'>$departamento->nombre_departamento</option>";
                }
                ?>
            </select>
            <select id="ciudad_id" name="ciudad_id" required>
                <!-- Opciones de ciudades se cargarán aquí -->
                <option value="" disabled selected>Ciudad</option>
            </select>
            <input type="number" name="telefono_usuario" placeholder="Telefono">
            <input type="password" name="contraseña_usuario" placeholder="Contraseña" required>
            <input type="password" name="confirmar_contraseña" placeholder="Confirmar contraseña" required>
            <button type="submit" name="registro">Registrarse</button>
        </form>
    </div>
    <div class="form-container sign-in">
        <form method="POST" action="login.php">
            <h1>Bienvenido</h1><br>
            <input type="text" name="identificacion" placeholder="Identificación" required>
            <input type="password" name="password" placeholder="Contraseña" required><br>
            <button type="submit" name="login">Iniciar sesión</button>
            <?php if (isset($error)) : ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form><br><br>
        <form action="login_admin.php">
            <button class="botonxd">Iniciar sesión como Administrador</button>
        </form>
    </div>
    <div class="toggle-container">
        <div class="toggle">
            <div class="toggle-panel toggle-left">
                <h1>Registrate!</h1>
                Una vez registrado podrás iniciar sesión
                <br><button class="hidden" id="login">Iniciar sesión</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Hola, amigo!</h1>
                Este apartado es para poder hacer la creación de tu cuenta
                <br><button class="hidden" id="register">Registrarse</button>
            </div>
        </div>
    </div>
</div>
<script>
    function cargarCiudades() {
        var departamentoId = document.getElementById("departamento_id").value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "", true);
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
            }
        };

        xhr.send("ajax_request=true&departamento_id=" + departamentoId);
    }

    document.getElementById("registerForm").addEventListener("submit", function(event) {
        var password = document.querySelector('input[name="contraseña_usuario"]').value;
        var confirmPassword = document.querySelector('input[name="confirmar_contraseña"]').value;

        if (password !== confirmPassword) {
            event.preventDefault(); // Prevenir el envío del formulario
            alert("Las contraseñas no coinciden");
        }
    });
</script>
<script src="../js/login.js"></script>
</body>
</html>
