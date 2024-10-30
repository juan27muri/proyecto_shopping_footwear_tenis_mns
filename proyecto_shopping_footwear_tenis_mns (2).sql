-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-09-2024 a las 18:08:08
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto_shopping_footwear_tenis_mns`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carritos`
--

CREATE TABLE `carritos` (
  `id_carrito` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carritos`
--

INSERT INTO `carritos` (`id_carrito`, `usuario_id`) VALUES
(1, 555),
(4, 888),
(2, 999),
(3, 1069721065);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorías`
--

CREATE TABLE `categorías` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion_categoria` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorías`
--

INSERT INTO `categorías` (`id_categoria`, `nombre_categoria`, `descripcion_categoria`) VALUES
(1, 'Hombre', 'Calzado para hombre'),
(2, 'Mujer', 'Calzado para Mujer'),
(3, 'Niños', 'Calzado para niños'),
(4, 'Deportivos', 'Calzado para Deportes'),
(5, 'Botas', 'Calzado de trabajo'),
(6, 'Chanclas', 'Chanclas para todo genero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudades`
--

CREATE TABLE `ciudades` (
  `id_ciudad` int(11) NOT NULL,
  `nombre_ciudad` varchar(100) NOT NULL,
  `departamento_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ciudades`
--

INSERT INTO `ciudades` (`id_ciudad`, `nombre_ciudad`, `departamento_id`) VALUES
(1, 'Ibague', 1),
(2, 'Medellin', 2),
(3, 'Cali', 3),
(4, 'Soacha', 4),
(6, 'Espinal', 1),
(7, 'Bogota', 13),
(8, 'Neiva', NULL),
(9, 'Barranquilla', 8),
(10, 'Pasto', NULL),
(11, 'Bucaramanga', 10),
(12, 'Cucuta', 11),
(13, 'Villavicencio', 12),
(14, 'Pereira', 6),
(16, 'Armenia', 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id_departamento` int(11) NOT NULL,
  `nombre_departamento` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id_departamento`, `nombre_departamento`) VALUES
(1, 'Tolima'),
(2, 'Antioquia'),
(3, 'Valle del Cauca'),
(4, 'Cundinamarca'),
(5, 'Huila'),
(6, 'Risaralda'),
(8, 'Atlantico'),
(9, 'Nariño'),
(10, 'Santander'),
(11, 'Norte de Santander'),
(12, 'Meta'),
(13, 'Capital'),
(14, 'Quindio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_carrito`
--

CREATE TABLE `detalles_carrito` (
  `id_detalle_carrito` int(11) NOT NULL,
  `carrito_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `talla` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_pedido`
--

CREATE TABLE `detalles_pedido` (
  `id_detalle_pedido` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `talla` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalles_pedido`
--

INSERT INTO `detalles_pedido` (`id_detalle_pedido`, `pedido_id`, `producto_id`, `cantidad`, `talla`, `precio`) VALUES
(1, 7, 222, 1, '', 120000.00),
(2, 9, 555, 1, '', 200000.00),
(3, 10, 555, 1, '', 200000.00),
(4, 11, 444, 5, '', 110000.00),
(5, 12, 333, 1, '', 160000.00),
(6, 12, 111, 1, '', 300000.00),
(7, 13, 222, 1, '', 120000.00),
(8, 14, 222, 1, '', 120000.00),
(9, 15, 111, 2, '', 300000.00),
(10, 16, 111, 1, '', 300000.00),
(11, 33, 111, 1, '35', 300000.00),
(12, 34, 111, 1, '36', 300000.00),
(13, 35, 111, 1, '35', 300000.00),
(14, 35, 111, 1, '40', 300000.00),
(15, 36, 333, 1, '', 160000.00),
(16, 36, 444, 1, '', 110000.00),
(17, 37, 111, 1, '35', 300000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL,
  `nombre_marca` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id_marca`, `nombre_marca`) VALUES
(1, 'Adidas'),
(2, 'Nike'),
(3, 'Puma'),
(4, 'Skechers'),
(5, 'Fila'),
(6, 'New Balance'),
(7, 'Converse'),
(9, 'Caterpillar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `numero_pedido` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `fecha_pedido` datetime NOT NULL,
  `total_pedido` decimal(10,2) NOT NULL,
  `tipo_pago` varchar(50) DEFAULT NULL,
  `estado_pedido` enum('Procesando','Enviado','Entregado','Cancelado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`numero_pedido`, `usuario_id`, `fecha_pedido`, `total_pedido`, `tipo_pago`, `estado_pedido`) VALUES
(7, 555, '2024-08-20 14:32:09', 120000.00, 'Efectivo', 'Procesando'),
(9, 555, '2024-08-20 14:33:32', 200000.00, 'Efectivo', 'Procesando'),
(10, 555, '2024-08-20 14:37:38', 200000.00, 'Efectivo', 'Procesando'),
(11, 555, '2024-08-20 14:48:17', 550000.00, 'Efectivo', 'Procesando'),
(12, 999, '2024-08-20 15:02:46', 460000.00, 'efectivo', 'Procesando'),
(13, 555, '2024-08-21 14:30:52', 120000.00, 'efectivo', 'Procesando'),
(14, 555, '2024-08-21 14:36:40', 120000.00, 'Efectivo', 'Procesando'),
(15, 555, '2024-09-02 22:38:43', 600000.00, 'Efectivo', 'Procesando'),
(16, 555, '2024-09-03 14:39:08', 300000.00, 'Efectivo', 'Procesando'),
(33, 555, '2024-09-03 22:57:36', 300000.00, 'Efectivo', 'Procesando'),
(34, 555, '2024-09-12 15:37:50', 300000.00, 'Efectivo', 'Procesando'),
(35, 555, '2024-09-14 23:54:23', 600000.00, 'Efectivo', 'Procesando'),
(36, 1069721065, '2024-09-16 17:19:51', 270000.00, 'Contraentrega', 'Procesando'),
(37, 888, '2024-09-17 10:48:12', 300000.00, 'Contraentrega', 'Entregado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `codigo_producto` int(11) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `descripcion_producto` text DEFAULT NULL,
  `precio_producto` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `tallas` varchar(255) NOT NULL,
  `imagen_producto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`codigo_producto`, `nombre_producto`, `descripcion_producto`, `precio_producto`, `stock`, `categoria_id`, `marca_id`, `tallas`, `imagen_producto`) VALUES
(111, 'Tenis Skechers Mujer Skech Lite Pro', 'Tenis para Mujer', 300000.00, 21, 2, 4, '35, 36, 37, 38, 39, 40, 41, 42, 43', 'tenis skechers mujer.avif'),
(222, 'Nike Air Force Blancos', 'Calazado para hombre color blanco', 120000.00, 77, 1, 2, '35, 36, 37, 38, 39, 40, 41, 42, 43', 'nike air force.png'),
(333, 'Adidas Tenis Galaxy 6 Running', 'Tenis para Hombre', 160000.00, 48, 1, 1, '35, 36, 37, 38, 39, 40, 41, 42, 43', 'adidas tenis.avif'),
(444, 'Zapatillas FIla para Niño', 'Calazado para Niño', 110000.00, 41, 3, 5, '33, 34, 35, 36, 37, 38', 'fila para niños.jpg'),
(555, 'Zapatilla Converse Chuck Taylor Plataforma Cuero', 'Zapatilla para Mujer', 200000.00, 49, 2, 7, '35, 36, 37, 38, 39, 40, 41, 42, 43', 'converse mujer.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `soporte`
--

CREATE TABLE `soporte` (
  `numero_soporte` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo_soporte` enum('Queja','Rembolso','Cancelar Pedido','Otro') NOT NULL,
  `fecha_soporte` datetime NOT NULL,
  `descripcion_soporte` text NOT NULL,
  `estado_soporte` enum('Pendiente','En proceso','Resuelto','Rechazado') NOT NULL,
  `respuesta_admin` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `soporte`
--

INSERT INTO `soporte` (`numero_soporte`, `usuario_id`, `tipo_soporte`, `fecha_soporte`, `descripcion_soporte`, `estado_soporte`, `respuesta_admin`) VALUES
(7, 555, 'Rembolso', '2024-09-14 22:24:00', 'necesito hacer un rembolso en el pedido 11', 'Resuelto', 'su rembolso ha sido realizado'),
(8, 555, 'Otro', '2024-09-14 22:30:18', 'wdsdsd', 'Pendiente', ''),
(9, 1069721065, 'Queja', '2024-09-16 17:20:39', 'muy caro todo malditos ricachones', 'Resuelto', 'bobohp'),
(10, 888, 'Otro', '2024-09-17 10:50:01', 'Gracias por el pedido me gusto', 'Resuelto', 'de nada, un placer');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `apellido_usuario` varchar(100) NOT NULL,
  `email_usuario` varchar(100) NOT NULL,
  `contraseña_usuario` varchar(100) NOT NULL,
  `direccion_usuario` varchar(255) DEFAULT NULL,
  `telefono_usuario` varchar(20) DEFAULT NULL,
  `rol_id` int(11) DEFAULT NULL,
  `ciudad_id` int(11) DEFAULT NULL,
  `departamento_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `apellido_usuario`, `email_usuario`, `contraseña_usuario`, `direccion_usuario`, `telefono_usuario`, `rol_id`, `ciudad_id`, `departamento_id`) VALUES
(555, 'Pepe', 'Murillo', 'cliente@gmail.com', '$2y$10$X95lDGeAHY5nNhJvOK90wu6a5vHTSDNgU8ugHj158Uexw.tNw./XW', 'barrio modelia', '9999', 2, 1, 1),
(777, 'xokas', 'llanos', 'ibai@gmail.com', '$2y$10$zRTO/Qg5tc64Kgh/32E1cOfi8dBgf2GZzLTGXBXgSOy.iLmrPoAVO', '', '', 1, 1, 1),
(888, 'pepito', 'perez', 'pepito@gmail.com', '$2y$10$vOSeXgkK87f.jp2p9AQ8duCoaDAhpPs1HnOSwk9AcgTpwJp7V2Hm.', 'ddsd', '999999', 2, 1, 1),
(999, 'cliente', 'Tafur', 'Tafur@gmail.com', '$2y$10$B.6UK4Sc7jH1VZ.oOcfuLOaPBKPNkd1HreyLjfl/MXDKhFZCKrvnS', 'Comuna 13', '777777', 2, 13, 12),
(1234567, 'Pipe', 'Lolo', 'pipecracklolo@gmail.com', '$2y$10$kLyIQX0WI7jfVACJXs//weOTvxsW.2mnQyK6KQ3sGxr9RkJvyOPeC', 'mi casa', '333333', 2, 13, 12),
(1069721065, 'johan alejandro', 'carretero sanchez', 'johancarretero098@gmail.com', '$2y$10$J/QbwR5tTYH4uhJsCavkQedxj2CZU4.3MusGA8WhQUGNNGuBd8nuO', 'mi casa', '3014441981', 2, 1, 1),
(1105463216, 'Juan Felipe', 'Nuñez Vasquez', 'junapipeflow27@gmail.com', '$2y$10$kOpVA9zrpIm/VjPNIqCipe6XgS2CmaD1aazv.lx37ceH1cOBTEGM.', '', '3213132327', 1, 1, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `categorías`
--
ALTER TABLE `categorías`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`id_ciudad`),
  ADD KEY `departamento_id` (`departamento_id`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id_departamento`);

--
-- Indices de la tabla `detalles_carrito`
--
ALTER TABLE `detalles_carrito`
  ADD PRIMARY KEY (`id_detalle_carrito`),
  ADD UNIQUE KEY `carrito_id` (`carrito_id`,`producto_id`,`talla`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD PRIMARY KEY (`id_detalle_pedido`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id_marca`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`numero_pedido`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`codigo_producto`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `marca_id` (`marca_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `soporte`
--
ALTER TABLE `soporte`
  ADD PRIMARY KEY (`numero_soporte`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email_usuario` (`email_usuario`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `ciudad_id` (`ciudad_id`),
  ADD KEY `departamento_id` (`departamento_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carritos`
--
ALTER TABLE `carritos`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `categorías`
--
ALTER TABLE `categorías`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `id_ciudad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id_departamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `detalles_carrito`
--
ALTER TABLE `detalles_carrito`
  MODIFY `id_detalle_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  MODIFY `id_detalle_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id_marca` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `numero_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `soporte`
--
ALTER TABLE `soporte`
  MODIFY `numero_soporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD CONSTRAINT `carritos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `ciudades`
--
ALTER TABLE `ciudades`
  ADD CONSTRAINT `ciudades_ibfk_1` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id_departamento`);

--
-- Filtros para la tabla `detalles_carrito`
--
ALTER TABLE `detalles_carrito`
  ADD CONSTRAINT `detalles_carrito_ibfk_1` FOREIGN KEY (`carrito_id`) REFERENCES `carritos` (`id_carrito`),
  ADD CONSTRAINT `detalles_carrito_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`codigo_producto`);

--
-- Filtros para la tabla `detalles_pedido`
--
ALTER TABLE `detalles_pedido`
  ADD CONSTRAINT `detalles_pedido_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`numero_pedido`),
  ADD CONSTRAINT `detalles_pedido_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`codigo_producto`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorías` (`id_categoria`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id_marca`);

--
-- Filtros para la tabla `soporte`
--
ALTER TABLE `soporte`
  ADD CONSTRAINT `soporte_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id_rol`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`ciudad_id`) REFERENCES `ciudades` (`id_ciudad`),
  ADD CONSTRAINT `usuarios_ibfk_3` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id_departamento`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
