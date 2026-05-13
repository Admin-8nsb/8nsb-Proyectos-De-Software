-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: hospital_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `areas` (
  `ID` decimal(4,0) NOT NULL,
  `UBICACION` varchar(200) DEFAULT NULL,
  `NOMBREAREA` varchar(100) DEFAULT NULL,
  `ID1` decimal(3,0) NOT NULL,
  `HOSPITAL_UNI_ORG` varchar(5) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `AREAS_HOSPITAL_FK` (`HOSPITAL_UNI_ORG`),
  CONSTRAINT `AREAS_HOSPITAL_FK` FOREIGN KEY (`HOSPITAL_UNI_ORG`) REFERENCES `hospital` (`UNI_ORG`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `areas`
--

LOCK TABLES `areas` WRITE;
/*!40000 ALTER TABLE `areas` DISABLE KEYS */;
INSERT INTO `areas` VALUES (4,'Polo sur','Urgencias',2,'H0001');
/*!40000 ALTER TABLE `areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultas`
--

DROP TABLE IF EXISTS `consultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consultas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FECHACONSULTA` datetime DEFAULT NULL,
  `ESTATUS` decimal(2,0) DEFAULT NULL,
  `CONSULTORIOS_ID` int(11) NOT NULL,
  `TIPOCONSULTA` varchar(50) DEFAULT NULL,
  `MEDICOS_EXPEDIENTE` decimal(6,0) NOT NULL,
  `TIPOSERVICIO_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `CONSULTAS_CONSULTORIOS_FK` (`CONSULTORIOS_ID`),
  KEY `CONSULTAS_MEDICOS_FK` (`MEDICOS_EXPEDIENTE`),
  KEY `CONSULTAS_TIPOSERVICIO_FK` (`TIPOSERVICIO_ID`),
  CONSTRAINT `CONSULTAS_CONSULTORIOS_FK` FOREIGN KEY (`CONSULTORIOS_ID`) REFERENCES `consultorios` (`ID`),
  CONSTRAINT `CONSULTAS_MEDICOS_FK` FOREIGN KEY (`MEDICOS_EXPEDIENTE`) REFERENCES `medicos` (`EXPEDIENTE`),
  CONSTRAINT `CONSULTAS_TIPOSERVICIO_FK` FOREIGN KEY (`TIPOSERVICIO_ID`) REFERENCES `tipos_servicios` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultas`
--

LOCK TABLES `consultas` WRITE;
/*!40000 ALTER TABLE `consultas` DISABLE KEYS */;
INSERT INTO `consultas` VALUES (1,'2026-05-01 09:30:00',1,1,'Primera Vez',999999,2),(2,'2026-05-02 11:15:00',1,1,'Subsecuente',999999,2),(3,'2026-05-03 10:00:00',1,1,'Primera Vez',999999,3),(4,'2026-05-03 14:00:00',1,1,'Urgencia',999999,3),(5,'2026-05-04 16:30:00',1,1,'Primera Vez',999999,4),(6,'2026-05-05 08:00:00',1,1,'Primera Vez',999999,5),(7,'2026-05-05 12:45:00',1,1,'Subsecuente',999999,4),(8,'2026-05-06 09:00:00',1,1,'Primera Vez',999999,2),(9,'2026-05-07 20:05:00',1,7,'Primera Vez',999999,6);
/*!40000 ALTER TABLE `consultas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultorios`
--

DROP TABLE IF EXISTS `consultorios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consultorios` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CONSULTORIO` varchar(50) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `AREAS_ID` decimal(4,0) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `CONSULTORIOS_AREAS_FK` (`AREAS_ID`),
  CONSTRAINT `CONSULTORIOS_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultorios`
--

LOCK TABLES `consultorios` WRITE;
/*!40000 ALTER TABLE `consultorios` DISABLE KEYS */;
INSERT INTO `consultorios` VALUES (1,'Consultorio Nutrición','Piso 1, Ala A',4),(2,'Consultorio Odontología','Planta Baja',4),(3,'Consultorio Psicología','Piso 2, Ala B',4),(4,'Sala Electrocardiogramas','Piso 1, Ala C',4),(5,'Medicina General','Planta Baja',4),(7,'sanchezland','dodododododooo',4),(8,'Consultorio de chapa','Loll',4);
/*!40000 ALTER TABLE `consultorios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departamentos` (
  `ID` decimal(3,0) NOT NULL,
  `NOMBREDEPARTAMENTO` varchar(50) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `AREAS_ID` decimal(4,0) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `DEPARTAMENTOS_AREAS_FK` (`AREAS_ID`),
  CONSTRAINT `DEPARTAMENTOS_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamentos`
--

LOCK TABLES `departamentos` WRITE;
/*!40000 ALTER TABLE `departamentos` DISABLE KEYS */;
INSERT INTO `departamentos` VALUES (-1,'contabilidad','OK',4);
/*!40000 ALTER TABLE `departamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `egresos`
--

DROP TABLE IF EXISTS `egresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `egresos` (
  `ID` decimal(6,0) NOT NULL,
  `TIPO` decimal(2,0) DEFAULT NULL,
  `INGRESOS_ID` decimal(6,0) NOT NULL,
  `FECHAEGRESO` datetime DEFAULT NULL,
  `OBSERVACIONES` varchar(255) DEFAULT NULL,
  `HABITACIONES_ID` decimal(3,0) NOT NULL,
  PRIMARY KEY (`ID`,`HABITACIONES_ID`),
  UNIQUE KEY `EGRESOS__IDX` (`INGRESOS_ID`),
  KEY `EGRESOS_HABITACIONES_FK` (`HABITACIONES_ID`),
  KEY `EGRESOS_INGRESOS_FK` (`INGRESOS_ID`,`HABITACIONES_ID`),
  CONSTRAINT `EGRESOS_HABITACIONES_FK` FOREIGN KEY (`HABITACIONES_ID`) REFERENCES `habitaciones` (`ID`),
  CONSTRAINT `EGRESOS_INGRESOS_FK` FOREIGN KEY (`INGRESOS_ID`, `HABITACIONES_ID`) REFERENCES `ingresos` (`ID`, `HABITACIONES_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `egresos`
--

LOCK TABLES `egresos` WRITE;
/*!40000 ALTER TABLE `egresos` DISABLE KEYS */;
INSERT INTO `egresos` VALUES (2,2,2,'2026-05-05 18:37:00','Pepe fernandez, salio bien',3);
/*!40000 ALTER TABLE `egresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especialidades`
--

DROP TABLE IF EXISTS `especialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `especialidades` (
  `ID` decimal(2,0) NOT NULL,
  `ESPECIALIDAD` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especialidades`
--

LOCK TABLES `especialidades` WRITE;
/*!40000 ALTER TABLE `especialidades` DISABLE KEYS */;
INSERT INTO `especialidades` VALUES (7,'DoWhilerogia');
/*!40000 ALTER TABLE `especialidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudios`
--

DROP TABLE IF EXISTS `estudios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estudios` (
  `ID` decimal(6,0) NOT NULL,
  `TIPOESTUDIOS_ID` decimal(4,0) NOT NULL,
  `MEDICOS_EXPEDIENTE` decimal(6,0) NOT NULL,
  `FECHAESTUDIO` datetime DEFAULT NULL,
  `ESTATUS` decimal(2,0) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ESTUDIOS_MEDICOS_FK` (`MEDICOS_EXPEDIENTE`),
  KEY `ESTUDIOS_TIPOESTUDIOS_FK` (`TIPOESTUDIOS_ID`),
  CONSTRAINT `ESTUDIOS_MEDICOS_FK` FOREIGN KEY (`MEDICOS_EXPEDIENTE`) REFERENCES `medicos` (`EXPEDIENTE`),
  CONSTRAINT `ESTUDIOS_TIPOESTUDIOS_FK` FOREIGN KEY (`TIPOESTUDIOS_ID`) REFERENCES `tipoestudios` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudios`
--

LOCK TABLES `estudios` WRITE;
/*!40000 ALTER TABLE `estudios` DISABLE KEYS */;
INSERT INTO `estudios` VALUES (1,1,999999,'2026-05-05 18:41:00',2);
/*!40000 ALTER TABLE `estudios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habitaciones`
--

DROP TABLE IF EXISTS `habitaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `habitaciones` (
  `ID` decimal(3,0) NOT NULL,
  `NOMBREHABITACION` varchar(50) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `EQUIPAMIENTO` varchar(200) DEFAULT NULL,
  `AREAS_ID` decimal(4,0) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `HABITACIONES_AREAS_FK` (`AREAS_ID`),
  CONSTRAINT `HABITACIONES_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habitaciones`
--

LOCK TABLES `habitaciones` WRITE;
/*!40000 ALTER TABLE `habitaciones` DISABLE KEYS */;
INSERT INTO `habitaciones` VALUES (3,'Habitacion Pepe','Polo norte','Maquina de radiografias',4);
/*!40000 ALTER TABLE `habitaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hospital`
--

DROP TABLE IF EXISTS `hospital`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hospital` (
  `UNI_ORG` varchar(5) NOT NULL,
  `NOMUO` varchar(80) DEFAULT NULL,
  `DIRECCION` varchar(100) DEFAULT NULL,
  `DIRECTOR` varchar(60) DEFAULT NULL,
  `TELEFONO` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`UNI_ORG`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hospital`
--

LOCK TABLES `hospital` WRITE;
/*!40000 ALTER TABLE `hospital` DISABLE KEYS */;
INSERT INTO `hospital` VALUES ('H0001','Hospital Dowhiler','Calle 17, 1776, Ciudad Mirasierra, Saltillo','Dr. Jaime Coronel',8441306241);
/*!40000 ALTER TABLE `hospital` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingresos`
--

DROP TABLE IF EXISTS `ingresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ingresos` (
  `ID` decimal(6,0) NOT NULL,
  `TIPO` decimal(2,0) DEFAULT NULL,
  `FECHAINGRESO` datetime DEFAULT NULL,
  `OBSERVACIONES` varchar(255) DEFAULT NULL,
  `MEDICOS_EXPEDIENTE` decimal(6,0) NOT NULL,
  `HABITACIONES_ID` decimal(3,0) NOT NULL,
  PRIMARY KEY (`ID`,`HABITACIONES_ID`),
  KEY `INGRESOS_MEDICOS_FK` (`MEDICOS_EXPEDIENTE`),
  KEY `INGRESOS_HABITACIONES_FK` (`HABITACIONES_ID`),
  CONSTRAINT `INGRESOS_HABITACIONES_FK` FOREIGN KEY (`HABITACIONES_ID`) REFERENCES `habitaciones` (`ID`),
  CONSTRAINT `INGRESOS_MEDICOS_FK` FOREIGN KEY (`MEDICOS_EXPEDIENTE`) REFERENCES `medicos` (`EXPEDIENTE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingresos`
--

LOCK TABLES `ingresos` WRITE;
/*!40000 ALTER TABLE `ingresos` DISABLE KEYS */;
INSERT INTO `ingresos` VALUES (1,1,'2026-05-05 18:35:00',NULL,999999,3),(2,2,'2026-05-05 18:37:00','Pepe fernandez',999999,3);
/*!40000 ALTER TABLE `ingresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laboratorios`
--

DROP TABLE IF EXISTS `laboratorios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `laboratorios` (
  `ID` decimal(3,0) NOT NULL,
  `NOMBRELABORATORIO` varchar(100) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `AREAS_ID` decimal(4,0) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `LABORATORIOS_AREAS_FK` (`AREAS_ID`),
  CONSTRAINT `LABORATORIOS_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laboratorios`
--

LOCK TABLES `laboratorios` WRITE;
/*!40000 ALTER TABLE `laboratorios` DISABLE KEYS */;
INSERT INTO `laboratorios` VALUES (1,'DoWhileLabs','ok',4);
/*!40000 ALTER TABLE `laboratorios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicos`
--

DROP TABLE IF EXISTS `medicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `medicos` (
  `EXPEDIENTE` decimal(6,0) NOT NULL,
  `APELLIDOPATERNO` varchar(40) DEFAULT NULL,
  `APELLIDOMATERNO` varchar(40) DEFAULT NULL,
  `NOMBRE` varchar(40) DEFAULT NULL,
  `TELEFONOMOVIL` bigint(20) DEFAULT NULL,
  `TELEFONOCASA` bigint(20) DEFAULT NULL,
  `ESPECIALIDADES_ID` decimal(2,0) NOT NULL,
  `HOSPITAL_UNI_ORG` varchar(5) NOT NULL,
  PRIMARY KEY (`EXPEDIENTE`),
  KEY `MEDICOS_ESPECIALIDADES_FK` (`ESPECIALIDADES_ID`),
  KEY `MEDICOS_HOSPITAL_FK` (`HOSPITAL_UNI_ORG`),
  CONSTRAINT `MEDICOS_ESPECIALIDADES_FK` FOREIGN KEY (`ESPECIALIDADES_ID`) REFERENCES `especialidades` (`ID`),
  CONSTRAINT `MEDICOS_HOSPITAL_FK` FOREIGN KEY (`HOSPITAL_UNI_ORG`) REFERENCES `hospital` (`UNI_ORG`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicos`
--

LOCK TABLES `medicos` WRITE;
/*!40000 ALTER TABLE `medicos` DISABLE KEYS */;
INSERT INTO `medicos` VALUES (999999,'Sanchez','Jaramillo','Angel',8421158123,84430948743333333,7,'H0001');
/*!40000 ALTER TABLE `medicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `procquirurgicos`
--

DROP TABLE IF EXISTS `procquirurgicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `procquirurgicos` (
  `ID` decimal(6,0) NOT NULL,
  `TIPO` decimal(3,0) DEFAULT NULL,
  `FECHAPROCEDIMIENTO` datetime DEFAULT NULL,
  `ESTATUS` decimal(2,0) DEFAULT NULL,
  `QUIROFANOS_ID` decimal(4,0) NOT NULL,
  `MEDICOS_EXPEDIENTE` decimal(6,0) NOT NULL,
  `TIPOPROCEDIMIENTO_ID` decimal(3,0) NOT NULL,
  `ID1` decimal(4,0) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `PROCQUIRURGICOS_MEDICOS_FK` (`MEDICOS_EXPEDIENTE`),
  KEY `PROCQUIRURGICOS_QUIROFANOS_FK` (`QUIROFANOS_ID`),
  KEY `PROCQUIRURGICOS_TIPOPROCEDIMIENTO_FK` (`TIPOPROCEDIMIENTO_ID`),
  CONSTRAINT `PROCQUIRURGICOS_MEDICOS_FK` FOREIGN KEY (`MEDICOS_EXPEDIENTE`) REFERENCES `medicos` (`EXPEDIENTE`),
  CONSTRAINT `PROCQUIRURGICOS_QUIROFANOS_FK` FOREIGN KEY (`QUIROFANOS_ID`) REFERENCES `quirofanos` (`ID`),
  CONSTRAINT `PROCQUIRURGICOS_TIPOPROCEDIMIENTO_FK` FOREIGN KEY (`TIPOPROCEDIMIENTO_ID`) REFERENCES `tipoprocedimiento` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `procquirurgicos`
--

LOCK TABLES `procquirurgicos` WRITE;
/*!40000 ALTER TABLE `procquirurgicos` DISABLE KEYS */;
/*!40000 ALTER TABLE `procquirurgicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quirofanos`
--

DROP TABLE IF EXISTS `quirofanos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quirofanos` (
  `ID` decimal(4,0) NOT NULL,
  `NOMBREQUIROFANO` varchar(50) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `AREAS_ID` decimal(4,0) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `QUIROFANOS_AREAS_FK` (`AREAS_ID`),
  CONSTRAINT `QUIROFANOS_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quirofanos`
--

LOCK TABLES `quirofanos` WRITE;
/*!40000 ALTER TABLE `quirofanos` DISABLE KEYS */;
INSERT INTO `quirofanos` VALUES (1,'QuiroWhile','1',4);
/*!40000 ALTER TABLE `quirofanos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_uk` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','Acceso total al sistema'),(2,'Recepcion','Gestion de ingresos y egresos'),(3,'Medico','Gestion de consultas, estudios y procedimientos'),(4,'Laboratorio','Gestion de estudios'),(5,'Quirofano','Gestion de procedimientos quirurgicos');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipoestudios`
--

DROP TABLE IF EXISTS `tipoestudios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipoestudios` (
  `ID` decimal(4,0) NOT NULL,
  `NOMBREESTUDIO` varchar(100) DEFAULT NULL,
  `REQUISITOSESTUDIO` varchar(250) DEFAULT NULL,
  `COSTO` decimal(19,4) DEFAULT NULL,
  `LABORATORIOS_ID` decimal(3,0) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `TIPOESTUDIOS_LABORATORIOS_FK` (`LABORATORIOS_ID`),
  CONSTRAINT `TIPOESTUDIOS_LABORATORIOS_FK` FOREIGN KEY (`LABORATORIOS_ID`) REFERENCES `laboratorios` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipoestudios`
--

LOCK TABLES `tipoestudios` WRITE;
/*!40000 ALTER TABLE `tipoestudios` DISABLE KEYS */;
INSERT INTO `tipoestudios` VALUES (1,'DOWHILER?','Ser dowhiler',10000.0000,1);
/*!40000 ALTER TABLE `tipoestudios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipoprocedimiento`
--

DROP TABLE IF EXISTS `tipoprocedimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipoprocedimiento` (
  `ID` decimal(3,0) NOT NULL,
  `NOMBREPROCEDIMIENTO` varchar(100) DEFAULT NULL,
  `REQUISITOS` varchar(200) DEFAULT NULL,
  `ESTATUS` decimal(1,0) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipoprocedimiento`
--

LOCK TABLES `tipoprocedimiento` WRITE;
/*!40000 ALTER TABLE `tipoprocedimiento` DISABLE KEYS */;
/*!40000 ALTER TABLE `tipoprocedimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_servicios`
--

DROP TABLE IF EXISTS `tipos_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_servicios` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOMBRESERVICIO` varchar(100) DEFAULT NULL,
  `DESCRIPCION` varchar(200) DEFAULT NULL,
  `ESTATUS` decimal(1,0) DEFAULT 1,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_servicios`
--

LOCK TABLES `tipos_servicios` WRITE;
/*!40000 ALTER TABLE `tipos_servicios` DISABLE KEYS */;
INSERT INTO `tipos_servicios` VALUES (1,'Medicina General','Consulta general y especialidades médicas',1),(2,'Nutrición','Servicio de nutrición y control de peso',1),(3,'Odontología','Consulta dental',1),(4,'Psicología','Terapia psicológica',1),(5,'Electrocardiogramas','Servicio de estudios de ECG',1),(6,'juan',NULL,1);
/*!40000 ALTER TABLE `tipos_servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_medico`
--

DROP TABLE IF EXISTS `usuario_medico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario_medico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `medico_expediente` decimal(6,0) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_medico_uk` (`usuario_id`,`medico_expediente`),
  KEY `usuario_medico_medico_fk` (`medico_expediente`),
  CONSTRAINT `usuario_medico_medico_fk` FOREIGN KEY (`medico_expediente`) REFERENCES `medicos` (`EXPEDIENTE`),
  CONSTRAINT `usuario_medico_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_medico`
--

LOCK TABLES `usuario_medico` WRITE;
/*!40000 ALTER TABLE `usuario_medico` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuario_medico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_roles`
--

DROP TABLE IF EXISTS `usuario_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_roles_uk` (`usuario_id`,`rol_id`),
  KEY `usuario_roles_rol_fk` (`rol_id`),
  CONSTRAINT `usuario_roles_rol_fk` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `usuario_roles_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_roles`
--

LOCK TABLES `usuario_roles` WRITE;
/*!40000 ALTER TABLE `usuario_roles` DISABLE KEYS */;
INSERT INTO `usuario_roles` VALUES (1,1,1);
/*!40000 ALTER TABLE `usuario_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_username_uk` (`username`),
  UNIQUE KEY `usuarios_correo_uk` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin','admin@hospital.com','$2y$10$E6HJCei3NLXtaFxJksvEn.dSxy.Y/G6GBInUVzOt4OVq1thOaVaZ2','Administrador General',1,'2026-05-05 18:17:31');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otros_procedimientos_quirofano`
--

DROP TABLE IF EXISTS `otros_procedimientos_quirofano`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otros_procedimientos_quirofano` (
  `ID` decimal(6,0) NOT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `FECHAPROCEDIMIENTO` datetime DEFAULT NULL,
  `ESTATUS` decimal(2,0) DEFAULT NULL,
  `QUIROFANOS_ID` decimal(4,0) NOT NULL,
  `MEDICOS_EXPEDIENTE` decimal(6,0) NOT NULL,
  `ID1` decimal(4,0) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `OTROS_PROC_QUIROFANO_MEDICOS_FK` (`MEDICOS_EXPEDIENTE`),
  KEY `OTROS_PROC_QUIROFANO_QUIROFANOS_FK` (`QUIROFANOS_ID`),
  CONSTRAINT `OTROS_PROC_QUIROFANO_MEDICOS_FK` FOREIGN KEY (`MEDICOS_EXPEDIENTE`) REFERENCES `medicos` (`EXPEDIENTE`),
  CONSTRAINT `OTROS_PROC_QUIROFANO_QUIROFANOS_FK` FOREIGN KEY (`QUIROFANOS_ID`) REFERENCES `quirofanos` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otros_procedimientos_quirofano`
--

LOCK TABLES `otros_procedimientos_quirofano` WRITE;
/*!40000 ALTER TABLE `otros_procedimientos_quirofano` DISABLE KEYS */;
/*!40000 ALTER TABLE `otros_procedimientos_quirofano` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-07 20:07:55