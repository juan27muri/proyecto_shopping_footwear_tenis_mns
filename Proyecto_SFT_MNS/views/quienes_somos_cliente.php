<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir al índice con un mensaje
    header('Location: ../index.php?message=No%20has%20iniciado%20sesión');
    exit();
}
$nombreUsuario = $_SESSION['nombre_usuario']; // Suponiendo que se guarda el nombre en la sesión

// Conectar a la base de datos
require '../config/database.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Footwear Tenis MNS</title> 
    <link rel="stylesheet" href="../css/quienes_somos_cliente.css">
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
                <button id="search-button">Buscar</button>
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



    <nav>
        <div class="container">
            <ul>
              <h2>Nuestras marcas patrocinantes</h2>
            </ul>
        </div>
    </nav>
    

    <div class="container">
            <div class="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="../imagenes/nike.png" width="800" height="200" alt="Producto 1">
                    </div>
                    <div class="carousel-item">
                        <img src="../imagenes/adidas.png" alt="Producto 2" width="1000" height="200">
                    </div>
                    <div class="carousel-item">
                        <img src="../imagenes/balance.png" alt="Producto 3" width="1000" height="200">
                    </div>
                    <div class="carousel-item">
                        <img src="../imagenes/fila.png" alt="Producto 4">
                    </div>
                    <div class="carousel-item">
                        <img src="../imagenes/converse.png" alt="Producto 5" width="400" height="100">
                    </div>
                    <div class="carousel-item">
                        <img src="../imagenes/skechers.png" alt="Producto 5" width="400" height="100">
                    </div>
                </div>
                <button class="carousel-control prev" onclick="prevSlide()">&#10094;</button>
                <button class="carousel-control next" onclick="nextSlide()">&#10095;</button>
            </div>
    <main>
        <div class="about-us fade-in">
            <div>
                <h2>Sobre Nosotros</h2> <br>
                <p>
                En Shopping Footwear Tenis MNS, nos apasiona el calzado y creemos que cada paso cuenta. Nos dedicamos a ofrecer una amplia selección de tenis y zapatos de las mejores marcas del mercado, garantizando siempre calidad y estilo. Desde modelos clásicos hasta las últimas tendencias, trabajamos para que encuentres el calzado perfecto que se ajuste a tu estilo de vida.
                    Nuestro compromiso es brindar a nuestros clientes una experiencia de compra única, con un servicio personalizado y un catálogo diverso que se adapta a todas las necesidades. Ya sea que busques comodidad, rendimiento o moda, en Shopping Footwear Tenis MNS encontrarás el par ideal para cada ocasión.
                </p>
            </div>
            <div class="images-grid-shoe">
            <img src="https://png.pngtree.com/thumb_back/fw800/background/20230527/pngtree-nike-running-shoes-on-black-background-with-colorful-splashes-image_2671040.jpg" alt="Descripción de la imagen 1">
        </div>
        </div>


    

        <div class="main-content"> 
            <div class="square-container fade-in">
                <h3>Vision Global</h3> <br>
                <p>Convertirnos en la tienda de calzado líder en el mercado, reconocida por nuestra innovación y excelencia en la experiencia del cliente, llevando las últimas tendencias de calzado a cada rincón del mundo.</p>
            </div>
            <div class="square-container fade-in">
                <h3>Vision Sostenible</h3> <br>
                <p>Ser pioneros en la industria del calzado sostenible, ofreciendo productos de alta calidad que respeten el medio ambiente y contribuyan a un futuro más verde para las generaciones venideras.</p>
            </div>
            <div class="square-container fade-in">
                <h3>Vision de Comunidad</h3> <br>
                <p>Crear una comunidad global de amantes del calzado, donde la moda, la comodidad y el rendimiento se combinen para inspirar confianza y estilo en cada paso.</p>
            </div>
            <div class="square-container fade-in">
                <h3>Mision de calidad</h3><br>
                <p>Proporcionar a nuestros clientes una amplia gama de calzado de las mejores marcas, con un enfoque en la calidad, la comodidad y el diseño, para satisfacer las necesidades de cada cliente, sin importar su estilo o estilo de vida</p>
            </div>
            <div class="square-container fade-in">
                <h3>Mision de servicio</h3><br>
                <p>Ofrecer una experiencia de compra inigualable, basada en un servicio al cliente excepcional, entrega rápida y eficiente, y una plataforma fácil de usar que permita a nuestros clientes encontrar su par perfecto sin esfuerzo.</p>
            </div>
            <div class="square-container fade-in">
                <h3>Mision de innovación</h3><br>
                <p>Innovar continuamente en nuestras ofertas de productos y servicios, utilizando la tecnología y el análisis de tendencias para anticiparnos a las necesidades de nuestros clientes y mantenernos a la vanguardia en la industria del calzado.</p>
            </div>
        </div>

        <div class="extra-content">
    <div class="side-container"></div>
    
    <div class="center-container">
        <h3>Contenido Central</h3>
        <br>
        <p> En Shopping Footwear Tenis MNS, nos destacamos por nuestra fiabilidad y compromiso con nuestros clientes. 
            Nos esforzamos por ofrecer precios justos sin comprometer la calidad de nuestros productos, 
            asegurando que siempre encuentres el calzado que necesitas sin afectar tu presupuesto. 
            Tu satisfacción es nuestra prioridad, y trabajamos para garantizar una experiencia de compra segura y accesible para todos.</p>
        
        <div class="images-grid">
            <img src="../imagenes/dinero.png" alt="Descripción de la imagen 1">
            <img src="../imagenes/candado.png" alt="Descripción de la imagen 1">
        </div>
    </div>
    
    <div class="side-container"></div>
</div>


        <div class="map-container">
            <h3>Nuestra Ubicación</h3>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!3m2!1ses!2sco!4v1723749872232!5m2!1ses!2sco!6m8!1m7!1scRw-6njlw7OXZebi1GES0Q!2m2!1d4.44347362635642!2d-75.24044371644432!3f213.6272487526915!4f14.921018987914735!5f0.7820865974627469"
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
             </iframe>
        </div>





        
    </main>

    <footer>
        <p>&copy; 2024 Mi Tienda. Todos los derechos reservados.</p>
    </footer>

    <script src="../js/quienes_somos1.js"></script>
</body>
</html>