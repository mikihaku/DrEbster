CREATE SCHEMA `drebster` DEFAULT CHARACTER SET utf8 ;

CREATE TABLE `drebster`.`schedule` (
                                       `ID` INT NOT NULL,
                                       `start` INT(12) UNSIGNED NOT NULL,
                                       `finish` INT(12) UNSIGNED NOT NULL,
                                       `name` TEXT NOT NULL,
                                       `teacher` VARCHAR(250) NOT NULL,
                                       `room_number` VARCHAR(10) NOT NULL,
                                       `room_name` VARCHAR(250) NOT NULL,
                                       `elective` TINYINT NULL,
                                       PRIMARY KEY (`ID`),
                                       INDEX `start` (`start` ASC),
                                       INDEX `finish` (`finish` ASC));

ALTER TABLE `drebster`.`schedule` CHANGE COLUMN `ID` `ID` INT(11) NOT NULL AUTO_INCREMENT ;