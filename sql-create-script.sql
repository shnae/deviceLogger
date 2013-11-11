create database devicelogger;
CREATE  TABLE IF NOT EXISTS `devicelogger`.`devices` (
  `iddevices` INT(11) NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(45) NULL DEFAULT NULL ,
  `Description` VARCHAR(45) NULL DEFAULT NULL ,
  `HostName` VARCHAR(45) NULL DEFAULT NULL ,
  `Make` VARCHAR(45) NULL DEFAULT NULL ,
  `Model` VARCHAR(45) NULL DEFAULT NULL ,
  `Url` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`iddevices`) ,
  UNIQUE INDEX `deviceName_UNIQUE` (`Name` ASC) ,
  UNIQUE INDEX `iddevices_UNIQUE` (`iddevices` ASC) )
ENGINE = InnoDB
AUTO_INCREMENT = 19
DEFAULT CHARACTER SET = latin1;

CREATE  TABLE IF NOT EXISTS `devicelogger`.`devicemetrics` (
  `iddevicemetrics` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `xmlTag` VARCHAR(45) NOT NULL ,
  `device` INT(11) NOT NULL ,
  PRIMARY KEY (`iddevicemetrics`) ,
  INDEX `fk_deviceMetrics_devices1` (`device` ASC) ,
  INDEX `iddevices_idx` (`device` ASC) ,
  CONSTRAINT `iddevices`
    FOREIGN KEY (`device` )
    REFERENCES `devicelogger`.`devices` (`iddevices` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 22
DEFAULT CHARACTER SET = latin1;

CREATE  TABLE IF NOT EXISTS `devicelogger`.`datapoints` (
  `iddatapoints` INT(11) NOT NULL AUTO_INCREMENT ,
  `metric` INT(11) NOT NULL ,
  `device` INT(11) NOT NULL ,
  `datapoint` DECIMAL(5,2) NOT NULL ,
  `timestamp` DATETIME NOT NULL ,
  PRIMARY KEY (`iddatapoints`) ,
  INDEX `fk_datapoints_deviceMetrics` (`metric` ASC) ,
  INDEX `fk_datapoints_devices1` (`device` ASC) ,
  INDEX `metric_idx` (`metric` ASC) ,
  INDEX `device_idx` (`device` ASC) ,
  CONSTRAINT `iddevicemetrics`
    FOREIGN KEY (`metric` )
    REFERENCES `devicelogger`.`devicemetrics` (`iddevicemetrics` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `device`
    FOREIGN KEY (`device` )
    REFERENCES `devicelogger`.`devicemetrics` (`device` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 142318
DEFAULT CHARACTER SET = latin1;

CREATE  TABLE IF NOT EXISTS `devicelogger`.`settings` (
  `name` VARCHAR(30) NOT NULL ,
  `value` VARCHAR(45) NOT NULL ,
  `description` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`name`) ,
  UNIQUE INDEX `settings_UNIQUE` (`name` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

use devicelogger;
insert into settings (name,value,description) VALUES ('devicePingInterval', 60, 'time between queries');
insert into settings (name,value,description) VALUES ('reInitializeInterval', 20, 'times to loop over device pings before getting new devices or settings from the database');

CREATE USER 'devicelogger'@'localhost' IDENTIFIED BY  'devicelogger';

GRANT USAGE ON * . * TO  'devicelogger'@'localhost' IDENTIFIED BY  'devicelogger' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

GRANT ALL PRIVILEGES ON  `devicelogger` . * TO  'devicelogger'@'localhost';

