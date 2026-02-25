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

CREATE TABLE `lukes`.`Chatbot` 
