CREATE  DATABASE `lukes`;

CREATE TABLE `lukes`.`users` (
  `email` VARCHAR(45) NOT NULL,
  `fullname` VARCHAR(45) NULL,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`email`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC));
