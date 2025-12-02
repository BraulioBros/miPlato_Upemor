-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-12-2025 a las 16:11:31
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
-- Base de datos: `miplato_upemor`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comidas`
--

CREATE TABLE `comidas` (
  `id_comida` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `calorias_por_100g` decimal(7,2) NOT NULL DEFAULT 0.00,
  `id_nutriente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comidas`
--

INSERT INTO `comidas` (`id_comida`, `nombre`, `descripcion`, `calorias_por_100g`, `id_nutriente`) VALUES
(6, 'Pollo en crema', 'Pollo con crema', 300.00, 1),
(12, 'Pechuga de pollo sin piel', 'Pechuga de pollo a la plancha sin piel ni aderezos', 165.00, 1),
(13, 'Arroz blanco cocido', 'Arroz blanco cocido en agua sin aceite', 130.00, 2),
(14, 'Frijoles de la olla', 'Frijoles cocidos simplemente en agua y sal', 90.00, 2),
(15, 'Ensalada de verduras mixtas', 'Lechuga, jitomate, pepino y zanahoria rallada', 40.00, 4),
(16, 'Manzana roja', 'Manzana fresca con cáscara', 52.00, 2),
(17, 'Yogur natural bajo en grasa', 'Yogur descremado sin azúcar añadida', 60.00, 1),
(18, 'Tortilla de maíz', 'Tortilla de maíz estándar de 100 g', 218.00, 2),
(19, 'Huevo revuelto', 'Huevo de gallina revuelto con mínima grasa', 150.00, 1),
(20, 'Filete de pescado a la plancha', 'Filete de pescado blanco preparado a la plancha', 120.00, 1),
(21, 'Avena cocida en agua', 'Hojuelas de avena cocidas solo en agua', 70.00, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumos`
--

CREATE TABLE `consumos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `comida_id` int(11) NOT NULL,
  `cantidad_gramos` decimal(7,2) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `consumos`
--

INSERT INTO `consumos` (`id`, `usuario_id`, `comida_id`, `cantidad_gramos`, `fecha`) VALUES
(3, 4, 2, 200.00, '2025-11-06'),
(10, 4, 2, 120.00, '2025-11-07'),
(12, 4, 2, 100.00, '2025-11-07'),
(13, 4, 2, 200.00, '2025-11-16'),
(14, 4, 2, 300.00, '2025-11-17'),
(15, 4, 2, 250.00, '2025-11-18'),
(16, 4, 2, 350.00, '2025-11-19'),
(17, 4, 2, 159.00, '2025-11-20'),
(18, 4, 0, 250.00, '2025-11-21'),
(19, 4, 0, 120.00, '2025-11-21'),
(20, 4, 0, 100.00, '2025-11-21'),
(21, 4, 6, 100.00, '2025-11-21'),
(22, 4, 6, 200.00, '2025-11-21'),
(23, 4, 6, 20.00, '2025-11-21'),
(24, 4, 6, 100.00, '2025-11-21'),
(25, 4, 19, 100.00, '2025-11-23'),
(26, 4, 21, 200.00, '2025-11-23'),
(27, 4, 14, 180.00, '2025-11-23'),
(28, 4, 12, 130.00, '2025-11-23'),
(29, 4, 15, 100.00, '2025-11-24'),
(35, 4, 21, 100.00, '2025-11-24'),
(36, 4, 21, 10.00, '2025-11-24'),
(37, 4, 21, 100.00, '2025-11-24'),
(57, 4, 21, 100.00, '2025-11-27'),
(58, 4, 19, 98.00, '2025-11-27'),
(59, 4, 13, 100.00, '2025-11-27'),
(60, 4, 21, 100.00, '2025-11-27'),
(61, 4, 18, 300.00, '2025-11-27'),
(62, 4, 18, 300.00, '2025-11-27'),
(63, 4, 21, 100.00, '2025-11-29'),
(64, 4, 18, 200.00, '2025-11-29'),
(65, 4, 14, 120.00, '2025-11-29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes_detalle`
--

CREATE TABLE `estudiantes_detalle` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `altura` decimal(5,2) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('M','F') DEFAULT 'M',
  `actividad` decimal(3,2) DEFAULT 1.40
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estudiantes_detalle`
--

INSERT INTO `estudiantes_detalle` (`id`, `usuario_id`, `peso`, `altura`, `fecha_nacimiento`, `sexo`, `actividad`) VALUES
(2, 7, 0.00, 0.00, '2005-06-12', 'M', 1.40),
(3, 4, 79.00, 1.80, '2005-06-12', 'M', 1.40),
(4, 8, 0.00, 0.00, '2025-11-08', 'M', 1.40),
(8, 18, 0.00, 0.00, '2005-10-06', 'M', 1.40);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nutrientes`
--

CREATE TABLE `nutrientes` (
  `id_nutriente` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `calorias_por_gramo` decimal(6,2) NOT NULL DEFAULT 0.00,
  `unidad_medida` varchar(30) NOT NULL DEFAULT 'g',
  `tipo` varchar(60) NOT NULL DEFAULT 'macronutriente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `nutrientes`
--

INSERT INTO `nutrientes` (`id_nutriente`, `nombre`, `calorias_por_gramo`, `unidad_medida`, `tipo`) VALUES
(1, 'Proteína', 4.00, 'g', 'macronutriente'),
(2, 'Carbohidratos', 4.00, 'g', 'macronutriente'),
(3, 'Grasas', 9.00, 'g', 'macronutriente'),
(4, 'Fibra dietética', 2.00, 'g', 'macronutriente'),
(5, 'Vitamina C', 0.00, 'mg', 'micronutriente'),
(6, 'Calcio', 0.00, 'mg', 'micronutriente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nutriologos_detalle`
--

CREATE TABLE `nutriologos_detalle` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `cedula` varchar(50) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `nutriologos_detalle`
--

INSERT INTO `nutriologos_detalle` (`id`, `usuario_id`, `cedula`, `telefono`, `completed`) VALUES
(4, 4, '123456789', '7771234567', 1),
(7, 7, '123456', '5566778899', 1),
(11, 48, '12345678', '5566789900', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `correo` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','nutriologo','estudiante') NOT NULL DEFAULT 'estudiante',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `correo`, `password`, `rol`, `creado_en`) VALUES
(4, 'Braulio Yair', 'Cuevas Mendoza', 'yair231097@upemor.edu.mx', '$2y$10$2knT9LMy63RGvnbqXaPafe7OTyhh3inUo06RjTxZTg6bzqFaqmCVq', 'estudiante', '2025-11-06 23:37:23'),
(7, 'Braulio Yair', 'Cuevas Mendoza', 'cmbo231097@upemor.edu.mx', '$2y$10$nanxPTDxaw1eWBQnmwq/5uvqqxUMPu5yosyr4x6/RpJKJJ1GF27Ze', 'nutriologo', '2025-11-07 16:05:59'),
(8, 'Eunice', 'Oropeza', 'eunice@upemor.edu.mx', '$2y$10$kprQMEj6VROiCsfP5bvPBO5t5cCd/cQ/dr5/TQVA6vwhhX.4Dl1ei', 'admin', '2025-11-07 19:19:14'),
(18, 'Maria', 'Molina', 'maria.molina@upemor.edu.mx', '$2y$10$G30aJ46eDLaRbC5SXc2h..1r.3ZzzG6es3Yh8T6K9aIfgPaG1aCee', 'estudiante', '2025-11-24 03:00:25'),
(43, 'Prueba', 'Garcia', 'prueba@gmail.com', '$2y$10$HOTJPs8tKqjolEdg05dFgOLjcr9wswE07k0pZxvtjqUhBxFo3/TRa', 'estudiante', '2025-11-27 23:37:56'),
(45, 'prueba', 'prueba', 'prueba123@gmail.com', '$2y$10$I/h3XumOshuZDBoCVqi5V.LjaQZUiH4CwzUFvcDlz6IPVV9jKDK7O', 'estudiante', '2025-11-29 17:17:45'),
(46, 'braulio', 'prueba', 'braulio@gmail.com', '$2y$10$9CI7cKuGQqx.xDMMisiLNOrgMYpIA69WQTw5ltMVBkpAkglyo23vO', 'nutriologo', '2025-11-29 17:19:02'),
(47, 'prueba', 'prueba', 'prueba1@gmail.com', '$2y$10$.abENZ2h.Jegyx.w4RI9tuQTPxYA2.xc/p6Ezke4MchfeH8s/xYXu', 'nutriologo', '2025-11-29 17:20:51'),
(48, 'prueba', 'prueba', 'prueba1234@gmail.com', '$2y$10$2XGZYneTpqw/4S0PuEQ2y.Bd9S48kPGiX4KRhN9A9L/BLVjjrLvNK', 'nutriologo', '2025-11-29 17:21:59');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comidas`
--
ALTER TABLE `comidas`
  ADD PRIMARY KEY (`id_comida`),
  ADD KEY `comidas_ibfk_nutriente` (`id_nutriente`);

--
-- Indices de la tabla `consumos`
--
ALTER TABLE `consumos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `comida_id` (`comida_id`);

--
-- Indices de la tabla `estudiantes_detalle`
--
ALTER TABLE `estudiantes_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `nutrientes`
--
ALTER TABLE `nutrientes`
  ADD PRIMARY KEY (`id_nutriente`);

--
-- Indices de la tabla `nutriologos_detalle`
--
ALTER TABLE `nutriologos_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comidas`
--
ALTER TABLE `comidas`
  MODIFY `id_comida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `consumos`
--
ALTER TABLE `consumos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `estudiantes_detalle`
--
ALTER TABLE `estudiantes_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `nutrientes`
--
ALTER TABLE `nutrientes`
  MODIFY `id_nutriente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `nutriologos_detalle`
--
ALTER TABLE `nutriologos_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comidas`
--
ALTER TABLE `comidas`
  ADD CONSTRAINT `comidas_ibfk_nutriente` FOREIGN KEY (`id_nutriente`) REFERENCES `nutrientes` (`id_nutriente`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `consumos`
--
ALTER TABLE `consumos`
  ADD CONSTRAINT `consumos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `estudiantes_detalle`
--
ALTER TABLE `estudiantes_detalle`
  ADD CONSTRAINT `estudiantes_detalle_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `nutriologos_detalle`
--
ALTER TABLE `nutriologos_detalle`
  ADD CONSTRAINT `nutriologos_detalle_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
