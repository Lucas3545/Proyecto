CREATE  DATABASE `lukes`;

CREATE TABLE `lukes`.`users` (
  `email` VARCHAR(45) NOT NULL,
  `fullname` VARCHAR(45) NULL,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC));
  
  CREATE TABLE `lukes`.`cards` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `numero` VARCHAR(20) NOT NULL COMMENT 'Número de tarjeta de crédito',
  `nombre_tarjeta` VARCHAR(100) NOT NULL COMMENT 'Nombre del titular de la tarjeta',
  `vencimiento` VARCHAR(7) NOT NULL COMMENT 'Fecha de vencimiento (MM/YYYY)',
  `cvv` VARCHAR(4) NOT NULL COMMENT 'Código de seguridad CVV',
  `banco` VARCHAR(50) NULL COMMENT 'Banco emisor',
  `red_de_pago` VARCHAR(20) NULL COMMENT 'Visa, Mastercard, etc.',
  `email_usuario` VARCHAR(45) NULL COMMENT 'Usuario asociado a la tarjeta',
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email_usuario`)
);

CREATE TABLE `lukes`.`reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fecha` DATE NOT NULL COMMENT 'Fecha de la reserva',
  `nombre` VARCHAR(100) NOT NULL COMMENT 'Nombre del huésped',
  `email` VARCHAR(100) NOT NULL COMMENT 'Correo del huésped',
  `estado` ENUM('confirmada', 'cancelada') DEFAULT 'confirmada' COMMENT 'Estado de la reserva',
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE INDEX `idx_fecha` (`fecha`),
  INDEX `idx_email_reserva` (`email`)
) COMMENT 'Tabla de reservas del calendario';

CREATE TABLE `lukes`.`Chatbot` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(100) NOT NULL,
  `email_usuario` VARCHAR(45) NULL,
  `rol` ENUM('system', 'user', 'assistant') NOT NULL,
  `mensaje` TEXT NOT NULL,
  `modelo` VARCHAR(60) NULL,
  `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_chatbot_session` (`session_id`),
  KEY `idx_chatbot_email` (`email_usuario`),
  KEY `idx_chatbot_fecha` (`fecha_registro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de mensajes del chatbot';

CREATE TABLE `lukes`.`reservas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` DATE NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `estado` ENUM('confirmada', 'cancelada') NOT NULL DEFAULT 'confirmada',
  `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_reservas_fecha` (`fecha`),
  KEY `idx_reservas_email` (`email`),
  KEY `idx_reservas_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
