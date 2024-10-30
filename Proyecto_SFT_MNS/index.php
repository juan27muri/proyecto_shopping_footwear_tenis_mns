<?php
// Conectar a la base de datos
require 'config/database.php';

// Consulta para obtener productos para el carrusel (limitar a los primeros 5)
$queryCarrusel = $base->prepare("SELECT codigo_producto, nombre_producto, precio_producto, imagen_producto FROM productos LIMIT 5");
$queryCarrusel->execute();
$productosCarrusel = $queryCarrusel->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener productos para la sección "productos que te pueden interesar" (por ejemplo, otros 5 productos al azar)
$queryInteres = $base->prepare("SELECT codigo_producto, nombre_producto, precio_producto, imagen_producto FROM productos ORDER BY RAND() LIMIT 10");
$queryInteres->execute();
$productosInteres = $queryInteres->fetchAll(PDO::FETCH_ASSOC);

// Manejar búsqueda
$productosBusqueda = [];
$searchFilter = ''; // Variable para almacenar el texto de búsqueda

if (isset($_GET['search'])) {
    $searchFilter = trim($_GET['search']);
    if ($searchFilter !== '') {
        $sql = "SELECT * FROM productos WHERE nombre_producto LIKE :query OR descripcion_producto LIKE :query";
        $stmt = $base->prepare($sql);
        $stmt->execute([':query' => '%' . $searchFilter . '%']);
        $productosBusqueda = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Footwear Tenis MNS</title> 
    <link rel="stylesheet" href="css/pagina_principal2.css">
    <link rel="icon" href="imagenes/favicon.png" type="image/x-icon">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>ㅤShopping Footwear Tenis MNS</h1> <br> 
            </div>
            <div class="search-bar">
                ㅤㅤㅤ<form method="get" action="">
                    <input type="text" name="search" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($searchFilter); ?>">
                    <button type="submit" class="button">Buscar</button>
                </form>
                </div> <br> <br></div><br> <br>
            <div class="user-options">
                <a href="views/login.php">ㅤㅤIniciar Sesion</a>
                <a href="admin_dashboard.html"></a>
            </div>
        </div>
    </header>
    <div class="container">
        <nav>
            <div class="container">
                <ul>
                    <img src="https://w7.pngwing.com/pngs/403/367/png-transparent-adidas-logo-adidas-logo-cdr-angle-text.png" alt="" width="60" height="40"> <br>ㅤㅤㅤ
                    <img src="https://e7.pngegg.com/pngimages/14/927/png-clipart-swoosh-nike-nike-angle-triangle.png" alt="" width="60" height="40">      ㅤㅤㅤ
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTQnJTRO8uTVTxTbKeRDLo2Q5tCDtFSk995CQ&usqp=CAU" alt="" width="60" height="40">ㅤㅤㅤ
                    <img src="https://e7.pngegg.com/pngimages/848/331/png-clipart-brand-logo-skechers-shoe-sneakers-asics-logo-blue-text.png" alt="" width="60" height="40">    ㅤㅤㅤ
                    <img src="https://e7.pngegg.com/pngimages/256/226/png-clipart-logo-new-balance-brand-shoe-trademark-new-balance-logo.png" alt="" width="60" height="40">    ㅤㅤㅤ
                    <img src="https://w7.pngwing.com/pngs/889/412/png-transparent-converse-logo-converse-chuck-taylor-all-stars-sneakers-shoe-clothing-brand-text-fashion-logo.png" alt="" width="100" height="40">ㅤㅤㅤ
                </ul>
            </div>
        </nav>
        <br><div align="center" id="search-results" class="mosaic">
            <?php if (!empty($productosBusqueda)): ?>
                <?php foreach ($productosBusqueda as $producto): ?>
                    <div class="mosaic">
                        <a href="views/modelo_zapato_NoLogin.php?producto_id=<?php echo htmlspecialchars($producto['codigo_producto']); ?>" class="photo-container">
                            <img src="cruds/productos/images/<?php echo htmlspecialchars($producto['imagen_producto']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" width="100" height="auto">
                            <div class="photo-title"><?php echo htmlspecialchars($producto['nombre_producto']); ?></div>
                            <p>$<?php echo htmlspecialchars($producto['precio_producto']); ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php elseif (isset($_GET['search']) && $searchFilter !== ''): ?>
                <p>No se encontraron productos.</p>
            <?php endif; ?>
        </div>
        <main>
            <div class="container">
                <!-- Carrusel de productos -->
                <div class="carousel">
                    <div class="carousel-inner">
                        <?php
                        $active = 'active'; // Para la primera imagen
                        foreach ($productosCarrusel as $producto) {
                            echo '<div class="carousel-item ' . $active . '">';
                            echo '<img src="cruds/productos/images/' . htmlspecialchars($producto['imagen_producto']) . '" alt="' . htmlspecialchars($producto['nombre_producto']) . '" width="300" height="100"">';
                            echo '<h2>' . htmlspecialchars($producto['nombre_producto']) . '</h2>';
                            echo '<p>$' . htmlspecialchars($producto['precio_producto']) . '</p> <br>';
                            echo '<div class="product"><a href="views/modelo_zapato_NoLogin.php?producto_id=' . htmlspecialchars($producto['codigo_producto']) . '" class="button-link">Ver Producto</a></div>';
                            echo '</div>';
                            $active = ''; // Quitar la clase active después del primer producto
                        }
                        ?>
                    </div>
                    <button class="carousel-control prev" onclick="prevSlide()">&#10094;</button>
                    <button class="carousel-control next" onclick="nextSlide()">&#10095;</button>
                </div>
                
                <!-- Productos que te pueden interesar -->
                <div class="recommended">
                    <h2>Productos que te pueden interesar</h2>
                    <div class="blue-container">
                        Descubre nuestros productos más populares
                    </div>
                    <div align="center" class="mosaic">
                        <?php
                        foreach ($productosInteres as $producto) {
                            echo '<a href="views/modelo_zapato_NoLogin.php?producto_id=' . htmlspecialchars($producto['codigo_producto']) . '" class="photo-container">';
                            echo '<img src="cruds/productos/images/' . htmlspecialchars($producto['imagen_producto']) . '" alt="' . htmlspecialchars($producto['nombre_producto']) . '" width="200" height="200">';
                            echo '<div class="photo-title">' . htmlspecialchars($producto['nombre_producto']) . '</div>';
                            echo '<div class="photo-precio">$' . htmlspecialchars($producto['precio_producto']) . '</div>';
                            echo '</a>';
                        }
                        ?>
                    </div> <br>
                </div>

                <!-- Sección ¿Quiénes somos? -->
                <div class="about-us">
                    <div>
                        <h2>¿Quiénes somos?</h2>
                        <p>Somos una tienda dedicada a ofrecer los mejores zapatos para todos los gustos. Nuestra pasión por la moda y la comodidad nos impulsa a seleccionar cuidadosamente cada producto que ofrecemos a nuestros clientes.</p>
                        <br>
                        <a href="views/quienes_somos.php"><div class="move"><div class="product"><button>¡Conoce mas de nosotros!</button></div></a></div>
                    </div>
                    <img src="imagenes/imagen6.jpg" alt="¿Quiénes somos?">
                </div>

                <!-- Categorías -->
                <!-- Categorías -->
<div class="move-text">
    <br><br><h1>Nuestras Categorías</h1>
</div>
<div class="categories-container">
    <div class="category-item">
        <a href="views/productos_por_categoria_NoLogin.php?categoria_id=1">
            <img src="imagenes/imagen7.jpg" alt="Hombre">
            <span>Hombres</span>
        </a>
    </div>
    <div class="category-item">
        <a href="views/productos_por_categoria_NoLogin.php?categoria_id=2">
            <img src="imagenes/imagen8.jpg" alt="Mujer">
            <span>Damas</span>
        </a>
    </div>
    <div class="category-item">
        <a href="views/productos_por_categoria_NoLogin.php?categoria_id=3">
            <img src="imagenes/imagen9.jpg" alt="Niños">
            <span>Niños</span>
        </a>
    </div>
    <div class="category-item">
        <a href="views/productos_por_categoria_NoLogin.php?categoria_id=4">
            <img src="imagenes/imagen10.jpg" alt="Deportivos">
            <span>Deportivos</span>
        </a>
    </div>
    <div class="category-item">
        <a href="views/productos_por_categoria_NoLogin.php?categoria_id=5">
            <img src="imagenes/imagen11.jpg" alt="Botas">
            <span>Botas</span>
        </a>
    </div>
    <div class="category-item">
        <a href="views/productos_por_categoria_NoLogin.php?categoria_id=6">
            <img src="imagenes/imagen13.jpg" alt="Chanclas">
            <span>Chanclas</span>
        </a>
    </div>
</div>
<footer>
        <div class="container">
            <p>&copy; 2024 Shopping Footwear Tenis MNS. Todos los derechos reservados.</p>
        </div>
    </footer>

            </div>
        </main>
    </div>
    <script src="js/pag_principal.js"></script>
</body>
</html>
