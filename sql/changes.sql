USE miplato_upemor;
ALTER TABLE usuarios ADD COLUMN apellidos VARCHAR(120) NULL AFTER nombre;
ALTER TABLE nutrientes ADD COLUMN id_nutriente VARCHAR(50) NOT NULL AFTER id, ADD COLUMN unidad_medida VARCHAR(30) NOT NULL DEFAULT 'g', ADD COLUMN tipo VARCHAR(60) NOT NULL DEFAULT 'macronutriente';
ALTER TABLE comidas ADD COLUMN id_comida VARCHAR(50) NOT NULL AFTER id, ADD COLUMN descripcion TEXT NULL AFTER nombre;
UPDATE nutrientes SET id_nutriente = CONCAT('NUT-', id) WHERE id_nutriente IS NULL OR id_nutriente='';
UPDATE comidas SET id_comida = CONCAT('COM-', id) WHERE id_comida IS NULL OR id_comida='';
