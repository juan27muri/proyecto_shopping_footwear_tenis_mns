<?php
session_start();

require_once '../config/database.php'; // Ruta correcta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identificacion = $_POST['identificacion'] ?? '';
    $password = $_POST['password'] ?? '';

    // Consulta para obtener el administrador por identificación usando PDO
    $stmt = $base->prepare("SELECT * FROM usuarios WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $identificacion, PDO::PARAM_STR);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar credenciales
    if ($admin && password_verify($password, $admin['contraseña_usuario'])) { // Comparar contraseña encriptada
        if ($admin['rol_id'] == 1) { // Verificar si el rol es de admin
            $_SESSION['admin_id'] = $admin['id_usuario'];
            $_SESSION['admin_nombre'] = $admin['nombre_usuario'];
            $_SESSION['admin_apellido'] = $admin['apellido_usuario'];
            header("Location: admin_dashboard.php"); // Redirigir al dashboard del admin
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
    <div class="form-container sign-up">
    </div>
    <div class="form-container sign-in">
        <form method="POST" action="login_admin.php">
            <h1>Bienvenido</h1>
            <input type="text" name="identificacion" placeholder="Identificación" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Iniciar sesión</button>
            <?php if (isset($error)) : ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form>
    </div>
    <div class="toggle-container">
        <div class="toggle">
            <div class="toggle-panel toggle-left">
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Hola, Administrador!</h1><br>
                Este apartado es únicamente para Administradores
            </div>
        </div>
    </div>
</div>
</body>
</html>
