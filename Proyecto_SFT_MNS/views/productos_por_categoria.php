<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir al índice con un mensaje
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
$nombreUsuario = $_SESSION['nombre_usuario']; // Suponiendo que se guarda el nombre en la sesión

// Verificar si la categoría está especificada en la URL
if (!isset($_GET['categoria_id'])) {
    header('Location: index.php?message=Categoría%20no%20especificada');
    exit();
}

$categoria_id = $_GET['categoria_id'];

// Conectar a la base de datos
require '../config/database.php';

// Consulta para obtener los productos de la categoría
$queryProductos = $base->prepare("SELECT * FROM productos WHERE categoria_id = :categoria_id");
$queryProductos->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
$queryProductos->execute();

$productos = $queryProductos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos de Categoría - Shopping Footwear Tenis MNS</title>
    <link rel="stylesheet" href="../css/pagina_principal_cliente1.css">
    <link rel="icon" href="../imagenes/favicon.png" type="image/x-icon">
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
                <span>Bienvenido: <?php echo htmlspecialchars($nombreUsuario); ?></span>
                <a href="mi_perfil.php">Mi Perfil</a>
                <a href="index_cliente.php">productos</a>
                <a href="soporte.php">Soporte</a>
                <a href="mis_pedidos.php">Ver mis pedidos</a>ㅤ
            </div>ㅤ
            <div class="move-sesion">
                <form id="logout-form" method="POST" action="cerrar_sesion.php">
                    <button type="button" id="logout-button" class="buttonC">Cerrar sesión</button>
                </form>
            </div> <br> 
        </div>
    </header>

    <div class="container">
    <div class="recommended">
        <h2>Productos en esta Categoría</h2>
        <div class="blue-container">
            Descubre nuestros productos en esta categoría
        </div>
        <div align="center" class="mosaic">
            <?php foreach ($productos as $producto): ?>
                <a href="modelo_zapato.php?producto_id=<?php echo htmlspecialchars($producto['codigo_producto']); ?>" class="photo-container">
                    <img src="../cruds/productos/images/<?php echo htmlspecialchars($producto['imagen_producto']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" width="200" height="200">
                    <div class="photo-title"><?php echo htmlspecialchars($producto['nombre_producto']); ?></div>
                    <div class="photo-precio">$<?php echo htmlspecialchars($producto['precio_producto']); ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- Categorías -->
<div class="move-text">
    <br><br><h1>Nuestras Categorías</h1>
</div>
<div class="categories-container">
    <div class="category-item">
        <a href="../views/productos_por_categoria.php?categoria_id=1">
            <img src="../imagenes/imagen7.jpg" alt="Hombre">
            <span>Hombres</span>
        </a>
    </div>
    <div class="category-item">
        <a href="../views/productos_por_categoria.php?categoria_id=2">
            <img src="../imagenes/imagen8.jpg" alt="Mujer">
            <span>Damas</span>
        </a>
    </div>
    <div class="category-item">
        <a href="../views/productos_por_categoria.php?categoria_id=3">
            <img src="../imagenes/imagen9.jpg" alt="Niños">
            <span>Niños</span>
        </a>
    </div>
    <div class="category-item">
        <a href="../views/productos_por_categoria.php?categoria_id=4">
            <img src="../imagenes/imagen10.jpg" alt="Deportivos">
            <span>Deportivos</span>
        </a>
    </div>
    <div class="category-item">
        <a href="../views/productos_por_categoria.php?categoria_id=5">
            <img src="../imagenes/imagen11.jpg" alt="Botas">
            <span>Botas</span>
        </a>
    </div>
    <div class="category-item">
        <a href="../views/productos_por_categoria.php?categoria_id=6">
            <img src="../imagenes/imagen13.jpg" alt="Chanclas">
            <span>Chanclas</span>
        </a>
    </div>
</div>
<footer>
    <div class="container">
        <p>&copy; 2024 Shopping Footwear Tenis MNS. Todos los derechos reservados.</p>
    </div>
</footer>

<script src="../js/pag_principal_cliente.js"></script>
</body>
</html>
