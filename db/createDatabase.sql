-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema resume
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema resume
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `resume` DEFAULT CHARACTER SET utf8 ;
USE `resume` ;

-- -----------------------------------------------------
-- Table `resume`.`student`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `resume`.`student` (
  `idstudent` INT NOT NULL AUTO_INCREMENT,
  `studentName` VARCHAR(45) NOT NULL,
  `studentGender` VARCHAR(45) NOT NULL,
  `studentEmail` VARCHAR(100) NOT NULL,
  `studentUserName` VARCHAR(45) NOT NULL,
  `studentPassword` VARCHAR(45) NOT NULL,
  `studentPhoto` VARCHAR(45) NULL,
  `studentCreateTime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idstudent`),
  UNIQUE INDEX `studentUserName_UNIQUE` (`studentUserName` ASC),
  UNIQUE INDEX `idstudent_UNIQUE` (`idstudent` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `resume`.`company`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `resume`.`company` (
  `idcompany` INT NOT NULL AUTO_INCREMENT,
  `companyName` VARCHAR(45) NOT NULL,
  `companyUserName` VARCHAR(45) NOT NULL,
  `companyPassword` VARCHAR(45) NOT NULL,
  `companyEmail` VARCHAR(45) NULL,
  PRIMARY KEY (`idcompany`),
  UNIQUE INDEX `companyUserName_UNIQUE` (`companyUserName` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `resume`.`resume`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `resume`.`resume` (
  `idresume` INT NOT NULL,
  `student_idstudent` INT NOT NULL,
  `resumeName` VARCHAR(45) NULL,
  `resumeCreateTime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resumeUpdateTime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idresume`, `student_idstudent`),
  INDEX `fk_resume_student_idx` (`student_idstudent` ASC),
  CONSTRAINT `fk_resume_student`
    FOREIGN KEY (`student_idstudent`)
    REFERENCES `resume`.`student` (`idstudent`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `resume`.`student_like_company`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `resume`.`student_like_company` (
  `student_idstudent` INT NOT NULL,
  `company_idcompany` INT NOT NULL,
  PRIMARY KEY (`student_idstudent`, `company_idcompany`),
  INDEX `fk_student_has_company_company1_idx` (`company_idcompany` ASC),
  INDEX `fk_student_has_company_student1_idx` (`student_idstudent` ASC),
  CONSTRAINT `fk_student_has_company_student1`
    FOREIGN KEY (`student_idstudent`)
    REFERENCES `resume`.`student` (`idstudent`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_has_company_company1`
    FOREIGN KEY (`company_idcompany`)
    REFERENCES `resume`.`company` (`idcompany`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `resume`.`company_like_student`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `resume`.`company_like_student` (
  `student_idstudent` INT NOT NULL,
  `company_idcompany` INT NOT NULL,
  PRIMARY KEY (`student_idstudent`, `company_idcompany`),
  INDEX `fk_student_has_company_company2_idx` (`company_idcompany` ASC),
  INDEX `fk_student_has_company_student2_idx` (`student_idstudent` ASC),
  CONSTRAINT `fk_student_has_company_student2`
    FOREIGN KEY (`student_idstudent`)
    REFERENCES `resume`.`student` (`idstudent`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_has_company_company2`
    FOREIGN KEY (`company_idcompany`)
    REFERENCES `resume`.`company` (`idcompany`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `resume`.`resume_sent_to_company`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `resume`.`resume_sent_to_company` (
  `resume_idresume` INT NOT NULL,
  `resume_student_idstudent` INT NOT NULL,
  `company_idcompany` INT NOT NULL,
  PRIMARY KEY (`resume_idresume`, `resume_student_idstudent`, `company_idcompany`),
  INDEX `fk_resume_has_company_company1_idx` (`company_idcompany` ASC),
  INDEX `fk_resume_has_company_resume1_idx` (`resume_idresume` ASC, `resume_student_idstudent` ASC),
  CONSTRAINT `fk_resume_has_company_resume1`
    FOREIGN KEY (`resume_idresume` , `resume_student_idstudent`)
    REFERENCES `resume`.`resume` (`idresume` , `student_idstudent`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_resume_has_company_company1`
    FOREIGN KEY (`company_idcompany`)
    REFERENCES `resume`.`company` (`idcompany`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
