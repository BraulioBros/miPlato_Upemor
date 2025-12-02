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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `comidas` VALUES("6","Pollo en crema","Pollo con crema","300.00","1");
INSERT INTO `comidas` VALUES("12","Pechuga de pollo sin piel","Pechuga de pollo a la plancha sin piel ni aderezos","165.00","1");
INSERT INTO `comidas` VALUES("13","Arroz blanco cocido","Arroz blanco cocido en agua sin aceite","130.00","2");
INSERT INTO `comidas` VALUES("14","Frijoles de la olla","Frijoles cocidos simplemente en agua y sal","90.00","2");
INSERT INTO `comidas` VALUES("15","Ensalada de verduras mixtas","Lechuga, jitomate, pepino y zanahoria rallada","40.00","4");
INSERT INTO `comidas` VALUES("16","Manzana roja","Manzana fresca con cáscara","52.00","2");
INSERT INTO `comidas` VALUES("17","Yogur natural bajo en grasa","Yogur descremado sin azúcar añadida","60.00","1");
INSERT INTO `comidas` VALUES("18","Tortilla de maíz","Tortilla de maíz estándar de 100 g","218.00","2");
INSERT INTO `comidas` VALUES("19","Huevo revuelto","Huevo de gallina revuelto con mínima grasa","150.00","1");
INSERT INTO `comidas` VALUES("20","Filete de pescado a la plancha","Filete de pescado blanco preparado a la plancha","120.00","1");
INSERT INTO `comidas` VALUES("21","Avena cocida en agua","Hojuelas de avena cocidas solo en agua","70.00","2");




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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `consumos` VALUES("3","4","2","200.00","2025-11-06");
INSERT INTO `consumos` VALUES("10","4","2","120.00","2025-11-07");
INSERT INTO `consumos` VALUES("12","4","2","100.00","2025-11-07");
INSERT INTO `consumos` VALUES("13","4","2","200.00","2025-11-16");
INSERT INTO `consumos` VALUES("14","4","2","300.00","2025-11-17");
INSERT INTO `consumos` VALUES("15","4","2","250.00","2025-11-18");
INSERT INTO `consumos` VALUES("16","4","2","350.00","2025-11-19");
INSERT INTO `consumos` VALUES("17","4","2","159.00","2025-11-20");
INSERT INTO `consumos` VALUES("18","4","0","250.00","2025-11-21");
INSERT INTO `consumos` VALUES("19","4","0","120.00","2025-11-21");
INSERT INTO `consumos` VALUES("20","4","0","100.00","2025-11-21");
INSERT INTO `consumos` VALUES("21","4","6","100.00","2025-11-21");
INSERT INTO `consumos` VALUES("22","4","6","200.00","2025-11-21");
INSERT INTO `consumos` VALUES("23","4","6","20.00","2025-11-21");
INSERT INTO `consumos` VALUES("24","4","6","100.00","2025-11-21");
INSERT INTO `consumos` VALUES("25","4","19","100.00","2025-11-23");
INSERT INTO `consumos` VALUES("26","4","21","200.00","2025-11-23");
INSERT INTO `consumos` VALUES("27","4","14","180.00","2025-11-23");
INSERT INTO `consumos` VALUES("28","4","12","130.00","2025-11-23");
INSERT INTO `consumos` VALUES("29","4","15","100.00","2025-11-24");
INSERT INTO `consumos` VALUES("35","4","21","100.00","2025-11-24");
INSERT INTO `consumos` VALUES("36","4","21","10.00","2025-11-24");
INSERT INTO `consumos` VALUES("37","4","21","100.00","2025-11-24");
INSERT INTO `consumos` VALUES("57","4","21","100.00","2025-11-27");
INSERT INTO `consumos` VALUES("58","4","19","98.00","2025-11-27");
INSERT INTO `consumos` VALUES("59","4","13","100.00","2025-11-27");
INSERT INTO `consumos` VALUES("60","4","21","100.00","2025-11-27");
INSERT INTO `consumos` VALUES("61","4","18","300.00","2025-11-27");
INSERT INTO `consumos` VALUES("62","4","18","300.00","2025-11-27");




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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `estudiantes_detalle` VALUES("2","7","0.00","0.00","2005-06-12","M","1.40");
INSERT INTO `estudiantes_detalle` VALUES("3","4","79.00","1.80","2005-06-12","M","1.40");
INSERT INTO `estudiantes_detalle` VALUES("4","8","0.00","0.00","2025-11-08","M","1.40");
INSERT INTO `estudiantes_detalle` VALUES("8","18","","","2005-10-06","M","1.40");




DROP TABLE IF EXISTS `nutrientes`;
CREATE TABLE `nutrientes` (
  `id_nutriente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `calorias_por_gramo` decimal(6,2) NOT NULL DEFAULT 0.00,
  `unidad_medida` varchar(30) NOT NULL DEFAULT 'g',
  `tipo` varchar(60) NOT NULL DEFAULT 'macronutriente',
  PRIMARY KEY (`id_nutriente`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nutrientes` VALUES("1","Proteína","4.00","g","macronutriente");
INSERT INTO `nutrientes` VALUES("2","Carbohidratos","4.00","g","macronutriente");
INSERT INTO `nutrientes` VALUES("3","Grasas","9.00","g","macronutriente");
INSERT INTO `nutrientes` VALUES("4","Fibra dietética","2.00","g","macronutriente");
INSERT INTO `nutrientes` VALUES("5","Vitamina C","0.00","mg","micronutriente");
INSERT INTO `nutrientes` VALUES("6","Calcio","0.00","mg","micronutriente");




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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nutriologos_detalle` VALUES("4","4","123456789","7771234567","1");
INSERT INTO `nutriologos_detalle` VALUES("7","7","123456","5566778899","1");




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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` VALUES("4","Braulio Yair","Cuevas Mendoza","yair231097@upemor.edu.mx","$2y$10$2knT9LMy63RGvnbqXaPafe7OTyhh3inUo06RjTxZTg6bzqFaqmCVq","estudiante","2025-11-06 17:37:23");
INSERT INTO `usuarios` VALUES("7","Braulio Yair","Cuevas Mendoza","cmbo231097@upemor.edu.mx","$2y$10$nanxPTDxaw1eWBQnmwq/5uvqqxUMPu5yosyr4x6/RpJKJJ1GF27Ze","nutriologo","2025-11-07 10:05:59");
INSERT INTO `usuarios` VALUES("8","Eunice","Oropeza","eunice@upemor.edu.mx","$2y$10$kprQMEj6VROiCsfP5bvPBO5t5cCd/cQ/dr5/TQVA6vwhhX.4Dl1ei","admin","2025-11-07 13:19:14");
INSERT INTO `usuarios` VALUES("18","Maria","Molina","maria.molina@upemor.edu.mx","$2y$10$G30aJ46eDLaRbC5SXc2h..1r.3ZzzG6es3Yh8T6K9aIfgPaG1aCee","estudiante","2025-11-23 21:00:25");
INSERT INTO `usuarios` VALUES("43","Prueba","Garcia","prueba@gmail.com","$2y$10$HOTJPs8tKqjolEdg05dFgOLjcr9wswE07k0pZxvtjqUhBxFo3/TRa","estudiante","2025-11-27 17:37:56");
INSERT INTO `usuarios` VALUES("45","prueba","prueba","prueba123@gmail.com","$2y$10$I/h3XumOshuZDBoCVqi5V.LjaQZUiH4CwzUFvcDlz6IPVV9jKDK7O","estudiante","2025-11-29 11:17:45");
INSERT INTO `usuarios` VALUES("46","braulio","prueba","braulio@gmail.com","$2y$10$9CI7cKuGQqx.xDMMisiLNOrgMYpIA69WQTw5ltMVBkpAkglyo23vO","nutriologo","2025-11-29 11:19:02");


SET FOREIGN_KEY_CHECKS=1;
