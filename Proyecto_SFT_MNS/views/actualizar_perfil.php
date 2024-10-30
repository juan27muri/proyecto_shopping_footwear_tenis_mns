<?php
session_start();
require '../config/database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$direccion = $_POST['direccion'];
$departamento_id = $_POST['departamento'];
$ciudad_id = $_POST['ciudad'];

// Verificar si se está intentando cambiar la contraseña
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Verificar si el correo ya está en uso por otro usuario
$sqlVerificarCorreo = "SELECT id_usuario FROM usuarios WHERE email_usuario = :correo AND id_usuario != :usuario_id";
$stmt = $base->prepare($sqlVerificarCorreo);
$stmt->execute([':correo' => $correo, ':usuario_id' => $usuario_id]);

if ($stmt->rowCount() > 0) {
    // Si el correo ya existe, redirigir con un mensaje de error
    header('Location: mi_perfil.php?message=El%20correo%20electrónico%20ya%20está%20en%20uso');
    exit();
}

// Verificar la contraseña actual
$sqlVerificarContrasena = "SELECT contraseña_usuario FROM usuarios WHERE id_usuario = :usuario_id";
$stmt = $base->prepare($sqlVerificarContrasena);
$stmt->execute([':usuario_id' => $usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (password_verify($current_password, $usuario['contraseña_usuario'])) {
    // Verificar si las nuevas contraseñas coinciden
    if ($new_password === $confirm_password) {
        // Encriptar la nueva contraseña
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Actualizar la contraseña en la base de datos
        $sqlActualizar = "UPDATE usuarios SET 
            nombre_usuario = :nombre, 
            apellido_usuario = :apellido, 
            email_usuario = :correo, 
            telefono_usuario = :telefono, 
            direccion_usuario = :direccion, 
            departamento_id = :departamento_id, 
            ciudad_id = :ciudad_id,
            contraseña_usuario = :new_password
            WHERE id_usuario = :usuario_id";

        $stmt = $base->prepare($sqlActualizar);

        if ($stmt->execute([
            ':nombre' => $nombre,
            ':apellido' => $apellido,
            ':correo' => $correo,
            ':telefono' => $telefono,
            ':direccion' => $direccion,
            ':departamento_id' => $departamento_id,
            ':ciudad_id' => $ciudad_id,
            ':new_password' => $new_password_hashed,
            ':usuario_id' => $usuario_id
        ])) {
            // Actualizar la sesión con los nuevos datos
            $_SESSION['nombre_usuario'] = $nombre;
            $_SESSION['apellido_usuario'] = $apellido;
            $_SESSION['correo_usuario'] = $correo;
            $_SESSION['telefono_usuario'] = $telefono;
            $_SESSION['direccion_usuario'] = $direccion;
            $_SESSION['ciudad_usuario'] = $ciudad_id;
            $_SESSION['departamento_usuario'] = $departamento_id;

            header("Location: mi_perfil.php?message=Perfil%20y%20contraseña%20actualizados%20correctamente");
        } else {
            header("Location: mi_perfil.php?message=Error%20al%20actualizar%20el%20perfil");
        }
    } else {
        header("Location: mi_perfil.php?message=Las%20nuevas%20contraseñas%20no%20coinciden");
    }
} else {
    header("Location: mi_perfil.php?message=La%20contraseña%20actual%20es%20incorrecta");
}
exit();
?>
