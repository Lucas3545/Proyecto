CREATE  DATABASE `lukes`;

CREATE TABLE `lukes`.`users` (
  `email` VARCHAR(45) NOT NULL,
  `fullname` VARCHAR(45) NULL,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC));
  
  CREATE TABLE `lukes`.`tarjetas` (
  `register-vencimiento` VARCHAR(45) NOT NULL,
  `register-txt` VARCHAR(45) NULL,
  `register-nombre` VARCHAR(45) NOT NULL,
  `register-numero` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`register-numero`),
  UNIQUE INDEX `txt_UNIQUE` (`register-txt` ASC);