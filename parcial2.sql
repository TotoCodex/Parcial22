-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-07-2024 a las 03:32:34
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `parcial2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tienda`
--

CREATE TABLE `tienda` (
  `producto_id` int(11) NOT NULL,
  `producto_marca` varchar(20) NOT NULL,
  `producto_precio` int(11) NOT NULL,
  `producto_tipo` varchar(20) NOT NULL,
  `producto_modelo` varchar(20) NOT NULL,
  `producto_color` varchar(20) NOT NULL,
  `producto_stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tienda`
--

INSERT INTO `tienda` (`producto_id`, `producto_marca`, `producto_precio`, `producto_tipo`, `producto_modelo`, `producto_color`, `producto_stock`) VALUES
(8, 'Epson', 777, 'Impresora', '1102pw', 'rosa', 172),
(9, 'Hp', 3333, 'Cartucho', '1102pw', 'rosa', 0),
(11, 'Asus', 12344, 'Impresora', 'Legion', 'rosa', 20),
(12, 'Hp', 12344, 'Impresora', 'Legion', 'rosa', 40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `u_id` int(11) NOT NULL,
  `u_mail` varchar(30) NOT NULL,
  `u_usuario` varchar(20) NOT NULL,
  `u_contraseña` varchar(20) NOT NULL,
  `u_perfil` varchar(20) NOT NULL,
  `u_foto` varchar(20) DEFAULT NULL,
  `u_fecha_de_alta` varchar(20) NOT NULL,
  `u_fecha_de_baja` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`u_id`, `u_mail`, `u_usuario`, `u_contraseña`, `u_perfil`, `u_foto`, `u_fecha_de_alta`, `u_fecha_de_baja`) VALUES
(15, 'totito@gmail.com', 'tobias23', '2wwda22', 'admin', NULL, '29 / Jun / 2024', NULL),
(16, 'totoals@hotmail.com', 'sadasda', '2wwda22', 'admin', NULL, '29 / Jun / 2024', NULL),
(17, 'as@yahopo.com', 'loquito', '1234', 'empleado', NULL, '30 / Jun / 2024', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `v_mail` varchar(100) NOT NULL,
  `v_marca` varchar(20) NOT NULL,
  `v_tipo` varchar(20) NOT NULL,
  `v_modelo` varchar(20) NOT NULL,
  `v_stock` int(11) NOT NULL,
  `v_id` int(11) NOT NULL,
  `v_fecha` varchar(20) NOT NULL,
  `v_pedido` int(11) NOT NULL,
  `v_color` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`v_mail`, `v_marca`, `v_tipo`, `v_modelo`, `v_stock`, `v_id`, `v_fecha`, `v_pedido`, `v_color`) VALUES
('micasfaf@hotmail', 'Epson', 'Impresora', '1102pw', 2, 16, '29 / Jun / 2024', 47234, 'rosa'),
('micasfaf@hotmail', 'Epson', 'Impresora', '1102pw', 2, 17, '30 / Jun / 2024', 11471, 'rosa'),
('toto@gmail.com', 'Epson', 'Impresora', '1102pw', 20, 18, '30 / Jun / 2024', 31195, 'rosa'),
('toto@gmail.com', 'Asus', 'Impresora', 'Legion', 20, 19, '30 / Jun / 2024', 42196, 'rosa');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tienda`
--
ALTER TABLE `tienda`
  ADD PRIMARY KEY (`producto_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`u_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`v_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tienda`
--
ALTER TABLE `tienda`
  MODIFY `producto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `v_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
