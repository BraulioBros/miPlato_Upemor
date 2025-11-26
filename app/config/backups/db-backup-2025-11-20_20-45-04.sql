SET FOREIGN_KEY_CHECKS=0;


DROP TABLE IF EXISTS `comidas`;
CREATE TABLE `comidas` (
  `id_comida` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `calorias_por_100g` decimal(7,2) NOT NULL DEFAULT 0.00,
  `id_nutriente` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_comida`),
  KEY `comidas_ibfk_nutriente` (`id_nutriente`),
  CONSTRAINT `comidas_ibfk_nutriente` FOREIGN KEY (`id_nutriente`) REFERENCES `nutrientes` (`id_nutriente`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `comidas` VALUES("2","Pollo a la plancha","","165.00","");
INSERT INTO `comidas` VALUES("6","Pollo en crema","Pollo con crema","400.00","1");




DROP TABLE IF EXISTS `consumos`;
CREATE TABLE `consumos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `comida_id` int(11) NOT NULL,
  `cantidad_gramos` decimal(7,2) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `comida_id` (`comida_id`),
  CONSTRAINT `consumos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `consumos` VALUES("3","4","2","200.00","2025-11-06");
INSERT INTO `consumos` VALUES("10","4","2","120.00","2025-11-07");
INSERT INTO `consumos` VALUES("12","4","2","100.00","2025-11-07");
INSERT INTO `consumos` VALUES("13","4","2","200.00","2025-11-16");
INSERT INTO `consumos` VALUES("14","4","2","300.00","2025-11-17");
INSERT INTO `consumos` VALUES("15","4","2","250.00","2025-11-18");
INSERT INTO `consumos` VALUES("16","4","2","350.00","2025-11-19");
INSERT INTO `consumos` VALUES("17","4","2","159.00","2025-11-20");




DROP TABLE IF EXISTS `estudiantes_detalle`;
CREATE TABLE `estudiantes_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `altura` decimal(5,2) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` enum('M','F') DEFAULT 'M',
  `actividad` decimal(3,2) DEFAULT 1.40,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `estudiantes_detalle_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `estudiantes_detalle` VALUES("1","3","90.00","1.60","2005-06-12","F","1.50");
INSERT INTO `estudiantes_detalle` VALUES("2","7","0.00","0.00","2005-06-12","M","1.40");
INSERT INTO `estudiantes_detalle` VALUES("3","4","79.00","1.80","2005-06-12","M","1.40");
INSERT INTO `estudiantes_detalle` VALUES("4","8","0.00","0.00","2025-11-08","M","1.40");
INSERT INTO `estudiantes_detalle` VALUES("7","12","0.00","0.00","2005-06-25","M","1.40");




DROP TABLE IF EXISTS `nutrientes`;
CREATE TABLE `nutrientes` (
  `id_nutriente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `calorias_por_gramo` decimal(6,2) NOT NULL DEFAULT 0.00,
  `unidad_medida` varchar(30) NOT NULL DEFAULT 'g',
  `tipo` varchar(60) NOT NULL DEFAULT 'macronutriente',
  PRIMARY KEY (`id_nutriente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nutrientes` VALUES("1","Proteína","4.00","g","macronutriente");




DROP TABLE IF EXISTS `nutriologos_detalle`;
CREATE TABLE `nutriologos_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `cedula` varchar(50) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `nutriologos_detalle_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nutriologos_detalle` VALUES("1","4","12345","5566778899","1");
INSERT INTO `nutriologos_detalle` VALUES("2","7","1234567","5566778899","1");
INSERT INTO `nutriologos_detalle` VALUES("3","8","ashdjmfa","5566778899","1");




DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(120) DEFAULT NULL,
  `correo` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','nutriologo','estudiante') NOT NULL DEFAULT 'estudiante',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` VALUES("3","Estudiante","Demo","estu@miplato.local","$2y$10$z9o1iO7oY1YF1e8sQyVQmO6m7o6Zuvzq2pJ6L8i6kB3N3v6s3CwDu","estudiante","2025-11-06 17:36:03");
INSERT INTO `usuarios` VALUES("4","Braulio Yair","Cuevas Mendoza","yair231097@upemor.edu.mx","$2y$10$2knT9LMy63RGvnbqXaPafe7OTyhh3inUo06RjTxZTg6bzqFaqmCVq","admin","2025-11-06 17:37:23");
INSERT INTO `usuarios` VALUES("7","Braulio Yair","Cuevas Mendoza","cmbo231097@upemor.edu.mx","$2y$10$nanxPTDxaw1eWBQnmwq/5uvqqxUMPu5yosyr4x6/RpJKJJ1GF27Ze","nutriologo","2025-11-07 10:05:59");
INSERT INTO `usuarios` VALUES("8","Eunice","Flores","eunice@upemor.edu.mx","$2y$10$kprQMEj6VROiCsfP5bvPBO5t5cCd/cQ/dr5/TQVA6vwhhX.4Dl1ei","nutriologo","2025-11-07 13:19:14");
INSERT INTO `usuarios` VALUES("12","Maria","Mendoza","gomez@upemor.edu.mx","$2y$10$WSkVan8sIcoEc/1yYlY4VOsOuqCu5WDvbnerfxl9WEHtZKyLiVAcO","nutriologo","2025-11-08 12:14:45");


SET FOREIGN_KEY_CHECKS=1;
