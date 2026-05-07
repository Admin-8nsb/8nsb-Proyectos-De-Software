-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-05-2026 a las 00:13:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hospital_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `ID` int(11) NOT NULL,
  `UBICACION` varchar(200) DEFAULT NULL,
  `NOMBREAREA` varchar(100) DEFAULT NULL,
  `ID1` int(11) NOT NULL,
  `HOSPITAL_UNI_ORG` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultas`
--

CREATE TABLE `consultas` (
  `ID` int(11) NOT NULL,
  `FECHACONSULTA` datetime DEFAULT NULL,
  `ESTATUS` int(11) DEFAULT NULL,
  `CONSULTORIOS_ID` int(11) NOT NULL,
  `TIPOCONSULTA` varchar(50) DEFAULT NULL,
  `MEDICOS_EXPEDIENTE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consultorios`
--

CREATE TABLE `consultorios` (
  `ID` int(11) NOT NULL,
  `CONSULTORIO` varchar(50) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `AREAS_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `ID` int(11) NOT NULL,
  `NOMBREDEPARTAMENTO` varchar(50) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `AREAS_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `egresos`
--

CREATE TABLE `egresos` (
  `ID` int(11) NOT NULL,
  `TIPO` int(11) DEFAULT NULL,
  `INGRESOS_ID` int(11) NOT NULL,
  `FECHAEGRESO` datetime DEFAULT NULL,
  `OBSERVACIONES` varchar(255) DEFAULT NULL,
  `HABITACIONES_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

CREATE TABLE `especialidades` (
  `ID` int(11) NOT NULL,
  `ESPECIALIDAD` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudios`
--

CREATE TABLE `estudios` (
  `ID` int(11) NOT NULL,
  `TIPOESTUDIOS_ID` int(11) NOT NULL,
  `MEDICOS_EXPEDIENTE` int(11) NOT NULL,
  `FECHAESTUDIO` datetime DEFAULT NULL,
  `ESTATUS` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitaciones`
--

CREATE TABLE `habitaciones` (
  `ID` int(11) NOT NULL,
  `NOMBREHABITACION` varchar(50) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `EQUIPAMIENTO` varchar(200) DEFAULT NULL,
  `AREAS_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hospital`
--

CREATE TABLE `hospital` (
  `UNI_ORG` varchar(5) NOT NULL,
  `NOMUO` varchar(80) DEFAULT NULL,
  `DIRECCION` varchar(100) DEFAULT NULL,
  `DIRECTOR` varchar(60) DEFAULT NULL,
  `TELEFONO` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `ID` int(11) NOT NULL,
  `TIPO` int(11) DEFAULT NULL,
  `FECHAINGRESO` datetime DEFAULT NULL,
  `OBSERVACIONES` varchar(255) DEFAULT NULL,
  `MEDICOS_EXPEDIENTE` int(11) NOT NULL,
  `HABITACIONES_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `laboratorios`
--

CREATE TABLE `laboratorios` (
  `ID` int(11) NOT NULL,
  `NOMBRELABORATORIO` varchar(100) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `AREAS_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicos`
--

CREATE TABLE `medicos` (
  `EXPEDIENTE` int(11) NOT NULL,
  `APELLIDOPATERNO` varchar(40) DEFAULT NULL,
  `APELLIDOMATERNO` varchar(40) DEFAULT NULL,
  `NOMBRE` varchar(40) DEFAULT NULL,
  `TELEFONOMOVIL` bigint(20) DEFAULT NULL,
  `TELEFONOCASA` bigint(20) DEFAULT NULL,
  `ESPECIALIDADES_ID` int(11) NOT NULL,
  `HOSPITAL_UNI_ORG` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `procquirurgicos`
--

CREATE TABLE `procquirurgicos` (
  `ID` int(11) NOT NULL,
  `TIPO` int(11) DEFAULT NULL,
  `FECHAPROCEDIMIENTO` datetime DEFAULT NULL,
  `ESTATUS` int(11) DEFAULT NULL,
  `QUIROFANOS_ID` int(11) NOT NULL,
  `MEDICOS_EXPEDIENTE` int(11) NOT NULL,
  `TIPOPROCEDIMIENTO_ID` int(11) NOT NULL,
  `ID1` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `quirofanos`
--

CREATE TABLE `quirofanos` (
  `ID` int(11) NOT NULL,
  `NOMBREQUIROFANO` varchar(50) DEFAULT NULL,
  `UBICACION` varchar(100) DEFAULT NULL,
  `AREAS_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoestudios`
--

CREATE TABLE `tipoestudios` (
  `ID` int(11) NOT NULL,
  `NOMBREESTUDIO` varchar(100) DEFAULT NULL,
  `REQUISITOSESTUDIO` varchar(250) DEFAULT NULL,
  `COSTO` decimal(19,4) DEFAULT NULL,
  `LABORATORIOS_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoprocedimiento`
--

CREATE TABLE `tipoprocedimiento` (
  `ID` int(11) NOT NULL,
  `NOMBREPROCEDIMIENTO` varchar(100) DEFAULT NULL,
  `REQUISITOS` varchar(200) DEFAULT NULL,
  `ESTATUS` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estatus` tinyint(4) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_medico`
--

CREATE TABLE `usuario_medico` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `medico_expediente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_roles`
--

CREATE TABLE `usuario_roles` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `AREAS_HOSPITAL_FK` (`HOSPITAL_UNI_ORG`);

--
-- Indices de la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CONSULTAS_CONSULTORIOS_FK` (`CONSULTORIOS_ID`),
  ADD KEY `CONSULTAS_MEDICOS_FK` (`MEDICOS_EXPEDIENTE`);

--
-- Indices de la tabla `consultorios`
--
ALTER TABLE `consultorios`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `CONSULTORIOS_AREAS_FK` (`AREAS_ID`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `DEPARTAMENTOS_AREAS_FK` (`AREAS_ID`);

--
-- Indices de la tabla `egresos`
--
ALTER TABLE `egresos`
  ADD PRIMARY KEY (`ID`,`HABITACIONES_ID`),
  ADD UNIQUE KEY `EGRESOS__IDX` (`INGRESOS_ID`),
  ADD KEY `EGRESOS_HABITACIONES_FK` (`HABITACIONES_ID`),
  ADD KEY `EGRESOS_INGRESOS_FK` (`INGRESOS_ID`,`HABITACIONES_ID`);

--
-- Indices de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `estudios`
--
ALTER TABLE `estudios`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ESTUDIOS_MEDICOS_FK` (`MEDICOS_EXPEDIENTE`),
  ADD KEY `ESTUDIOS_TIPOESTUDIOS_FK` (`TIPOESTUDIOS_ID`);

--
-- Indices de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `HABITACIONES_AREAS_FK` (`AREAS_ID`);

--
-- Indices de la tabla `hospital`
--
ALTER TABLE `hospital`
  ADD PRIMARY KEY (`UNI_ORG`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`ID`,`HABITACIONES_ID`),
  ADD KEY `INGRESOS_MEDICOS_FK` (`MEDICOS_EXPEDIENTE`),
  ADD KEY `INGRESOS_HABITACIONES_FK` (`HABITACIONES_ID`);

--
-- Indices de la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `LABORATORIOS_AREAS_FK` (`AREAS_ID`);

--
-- Indices de la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD PRIMARY KEY (`EXPEDIENTE`),
  ADD KEY `MEDICOS_ESPECIALIDADES_FK` (`ESPECIALIDADES_ID`),
  ADD KEY `MEDICOS_HOSPITAL_FK` (`HOSPITAL_UNI_ORG`);

--
-- Indices de la tabla `procquirurgicos`
--
ALTER TABLE `procquirurgicos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `PROCQUIRURGICOS_MEDICOS_FK` (`MEDICOS_EXPEDIENTE`),
  ADD KEY `PROCQUIRURGICOS_QUIROFANOS_FK` (`QUIROFANOS_ID`),
  ADD KEY `PROCQUIRURGICOS_TIPOPROCEDIMIENTO_FK` (`TIPOPROCEDIMIENTO_ID`);

--
-- Indices de la tabla `quirofanos`
--
ALTER TABLE `quirofanos`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `QUIROFANOS_AREAS_FK` (`AREAS_ID`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_nombre_uk` (`nombre`);

--
-- Indices de la tabla `tipoestudios`
--
ALTER TABLE `tipoestudios`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `TIPOESTUDIOS_LABORATORIOS_FK` (`LABORATORIOS_ID`);

--
-- Indices de la tabla `tipoprocedimiento`
--
ALTER TABLE `tipoprocedimiento`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuarios_username_uk` (`username`),
  ADD UNIQUE KEY `usuarios_correo_uk` (`correo`);

--
-- Indices de la tabla `usuario_medico`
--
ALTER TABLE `usuario_medico`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_medico_uk` (`usuario_id`,`medico_expediente`),
  ADD KEY `usuario_medico_medico_fk` (`medico_expediente`);

--
-- Indices de la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_roles_uk` (`usuario_id`,`rol_id`),
  ADD KEY `usuario_roles_rol_fk` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `consultas`
--
ALTER TABLE `consultas`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `consultorios`
--
ALTER TABLE `consultorios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `egresos`
--
ALTER TABLE `egresos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estudios`
--
ALTER TABLE `estudios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `medicos`
--
ALTER TABLE `medicos`
  MODIFY `EXPEDIENTE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `procquirurgicos`
--
ALTER TABLE `procquirurgicos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `quirofanos`
--
ALTER TABLE `quirofanos`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipoestudios`
--
ALTER TABLE `tipoestudios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipoprocedimiento`
--
ALTER TABLE `tipoprocedimiento`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario_medico`
--
ALTER TABLE `usuario_medico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `areas`
--
ALTER TABLE `areas`
  ADD CONSTRAINT `AREAS_HOSPITAL_FK` FOREIGN KEY (`HOSPITAL_UNI_ORG`) REFERENCES `hospital` (`UNI_ORG`);

--
-- Filtros para la tabla `consultas`
--
ALTER TABLE `consultas`
  ADD CONSTRAINT `CONSULTAS_CONSULTORIOS_FK` FOREIGN KEY (`CONSULTORIOS_ID`) REFERENCES `consultorios` (`ID`),
  ADD CONSTRAINT `CONSULTAS_MEDICOS_FK` FOREIGN KEY (`MEDICOS_EXPEDIENTE`) REFERENCES `medicos` (`EXPEDIENTE`);

--
-- Filtros para la tabla `consultorios`
--
ALTER TABLE `consultorios`
  ADD CONSTRAINT `CONSULTORIOS_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`);

--
-- Filtros para la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD CONSTRAINT `DEPARTAMENTOS_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`);

--
-- Filtros para la tabla `egresos`
--
ALTER TABLE `egresos`
  ADD CONSTRAINT `EGRESOS_HABITACIONES_FK` FOREIGN KEY (`HABITACIONES_ID`) REFERENCES `habitaciones` (`ID`),
  ADD CONSTRAINT `EGRESOS_INGRESOS_FK` FOREIGN KEY (`INGRESOS_ID`,`HABITACIONES_ID`) REFERENCES `ingresos` (`ID`, `HABITACIONES_ID`);

--
-- Filtros para la tabla `estudios`
--
ALTER TABLE `estudios`
  ADD CONSTRAINT `ESTUDIOS_MEDICOS_FK` FOREIGN KEY (`MEDICOS_EXPEDIENTE`) REFERENCES `medicos` (`EXPEDIENTE`),
  ADD CONSTRAINT `ESTUDIOS_TIPOESTUDIOS_FK` FOREIGN KEY (`TIPOESTUDIOS_ID`) REFERENCES `tipoestudios` (`ID`);

--
-- Filtros para la tabla `habitaciones`
--
ALTER TABLE `habitaciones`
  ADD CONSTRAINT `HABITACIONES_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`);

--
-- Filtros para la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD CONSTRAINT `INGRESOS_HABITACIONES_FK` FOREIGN KEY (`HABITACIONES_ID`) REFERENCES `habitaciones` (`ID`),
  ADD CONSTRAINT `INGRESOS_MEDICOS_FK` FOREIGN KEY (`MEDICOS_EXPEDIENTE`) REFERENCES `medicos` (`EXPEDIENTE`);

--
-- Filtros para la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  ADD CONSTRAINT `LABORATORIOS_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`);

--
-- Filtros para la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD CONSTRAINT `MEDICOS_ESPECIALIDADES_FK` FOREIGN KEY (`ESPECIALIDADES_ID`) REFERENCES `especialidades` (`ID`),
  ADD CONSTRAINT `MEDICOS_HOSPITAL_FK` FOREIGN KEY (`HOSPITAL_UNI_ORG`) REFERENCES `hospital` (`UNI_ORG`);

--
-- Filtros para la tabla `procquirurgicos`
--
ALTER TABLE `procquirurgicos`
  ADD CONSTRAINT `PROCQUIRURGICOS_MEDICOS_FK` FOREIGN KEY (`MEDICOS_EXPEDIENTE`) REFERENCES `medicos` (`EXPEDIENTE`),
  ADD CONSTRAINT `PROCQUIRURGICOS_QUIROFANOS_FK` FOREIGN KEY (`QUIROFANOS_ID`) REFERENCES `quirofanos` (`ID`),
  ADD CONSTRAINT `PROCQUIRURGICOS_TIPOPROCEDIMIENTO_FK` FOREIGN KEY (`TIPOPROCEDIMIENTO_ID`) REFERENCES `tipoprocedimiento` (`ID`);

--
-- Filtros para la tabla `quirofanos`
--
ALTER TABLE `quirofanos`
  ADD CONSTRAINT `QUIROFANOS_AREAS_FK` FOREIGN KEY (`AREAS_ID`) REFERENCES `areas` (`ID`);

--
-- Filtros para la tabla `tipoestudios`
--
ALTER TABLE `tipoestudios`
  ADD CONSTRAINT `TIPOESTUDIOS_LABORATORIOS_FK` FOREIGN KEY (`LABORATORIOS_ID`) REFERENCES `laboratorios` (`ID`);

--
-- Filtros para la tabla `usuario_medico`
--
ALTER TABLE `usuario_medico`
  ADD CONSTRAINT `usuario_medico_medico_fk` FOREIGN KEY (`medico_expediente`) REFERENCES `medicos` (`EXPEDIENTE`),
  ADD CONSTRAINT `usuario_medico_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_roles`
--
ALTER TABLE `usuario_roles`
  ADD CONSTRAINT `usuario_roles_rol_fk` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `usuario_roles_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Acceso total al sistema'),
('Recepcion', 'Gestion de ingresos y egresos'),
('Medico', 'Gestion de consultas, estudios y procedimientos'),
('Laboratorio', 'Gestion de estudios'),
('Quirofano', 'Gestion de procedimientos quirurgicos');