-- ============================================================
-- SEED / DATOS DE PRUEBA REALES PARA HIS
-- Base de datos: his_db
-- Compatible con MariaDB / MySQL
-- Nota: este script limpia datos transaccionales y catálogos,
--       conserva/reinserta roles, usuario admin y servicios base.
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `hospital_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hospital_db`;

SET FOREIGN_KEY_CHECKS = 0;

-- Limpieza en orden seguro
DELETE FROM `usuario_medico`;
DELETE FROM `usuario_roles`;
DELETE FROM `usuarios`;
DELETE FROM `egresos`;
DELETE FROM `ingresos`;
DELETE FROM `estudios`;
DELETE FROM `consultas`;
DELETE FROM `otros_procedimientos_quirofano`;
DELETE FROM `procquirurgicos`;
DELETE FROM `tipoprocedimiento`;
DELETE FROM `tipoestudios`;
DELETE FROM `laboratorios`;
DELETE FROM `quirofanos`;
DELETE FROM `habitaciones`;
DELETE FROM `consultorios`;
DELETE FROM `departamentos`;
DELETE FROM `medicos`;
DELETE FROM `especialidades`;
DELETE FROM `areas`;
DELETE FROM `hospital`;
DELETE FROM `tipos_servicios`;
DELETE FROM `roles`;

-- Reinicio de AUTO_INCREMENT solo en tablas que lo tienen
ALTER TABLE `areas` AUTO_INCREMENT = 1;
ALTER TABLE `consultas` AUTO_INCREMENT = 1;
ALTER TABLE `consultorios` AUTO_INCREMENT = 1;
ALTER TABLE `habitaciones` AUTO_INCREMENT = 1;
ALTER TABLE `roles` AUTO_INCREMENT = 1;
ALTER TABLE `tipos_servicios` AUTO_INCREMENT = 1;
ALTER TABLE `usuarios` AUTO_INCREMENT = 1;
ALTER TABLE `usuario_medico` AUTO_INCREMENT = 1;
ALTER TABLE `usuario_roles` AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

START TRANSACTION;

-- ============================================================
-- ROLES Y USUARIOS
-- ============================================================

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso total al sistema'),
(2, 'Recepcion', 'Gestion de ingresos y egresos'),
(3, 'Medico', 'Gestion de consultas, estudios y procedimientos'),
(4, 'Laboratorio', 'Gestion de estudios'),
(5, 'Quirofano', 'Gestion de procedimientos quirurgicos');

-- Password hash tomado del script original del proyecto.
-- Usuario admin base: admin@hospital.com
INSERT INTO `usuarios` (`id`, `username`, `correo`, `password_hash`, `nombre`, `estatus`, `fecha_creacion`) VALUES
(1, 'admin', 'admin@hospital.com', '$2y$10$E6HJCei3NLXtaFxJksvEn.dSxy.Y/G6GBInUVzOt4OVq1thOaVaZ2', 'Administrador General', 1, NOW()),
(2, 'recepcion01', 'recepcion@his.com', '$2y$10$E6HJCei3NLXtaFxJksvEn.dSxy.Y/G6GBInUVzOt4OVq1thOaVaZ2', 'Laura Méndez Torres', 1, NOW()),
(3, 'lab01', 'laboratorio@his.com', '$2y$10$E6HJCei3NLXtaFxJksvEn.dSxy.Y/G6GBInUVzOt4OVq1thOaVaZ2', 'Roberto Salinas Gómez', 1, NOW()),
(4, 'qxf01', 'quirofano@his.com', '$2y$10$E6HJCei3NLXtaFxJksvEn.dSxy.Y/G6GBInUVzOt4OVq1thOaVaZ2', 'Mariana Valdés Ruiz', 1, NOW()),
(5, 'med_ana', 'ana.ramirez@his.com', '$2y$10$E6HJCei3NLXtaFxJksvEn.dSxy.Y/G6GBInUVzOt4OVq1thOaVaZ2', 'Dra. Ana Ramírez López', 1, NOW()),
(6, 'med_carlos', 'carlos.hernandez@his.com', '$2y$10$E6HJCei3NLXtaFxJksvEn.dSxy.Y/G6GBInUVzOt4OVq1thOaVaZ2', 'Dr. Carlos Hernández Díaz', 1, NOW());

INSERT INTO `usuario_roles` (`id`, `usuario_id`, `rol_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 4),
(4, 4, 5),
(5, 5, 3),
(6, 6, 3);

-- ============================================================
-- TIPOS DE SERVICIOS BASE
-- IMPORTANTE:
-- Estos 5 servicios quedan como servicios base para que aparezcan
-- como en tu pantalla: Medicina General, Nutrición, Odontología,
-- Psicología y Electrocardiogramas.
-- ============================================================

INSERT INTO `tipos_servicios` (`ID`, `NOMBRESERVICIO`, `DESCRIPCION`, `ESTATUS`) VALUES
(1, 'Medicina General', 'Consulta general y especialidades médicas', 1),
(2, 'Nutrición', 'Servicio de nutrición y control de peso', 1),
(3, 'Odontología', 'Consulta dental', 1),
(4, 'Psicología', 'Terapia psicológica', 1),
(5, 'Electrocardiogramas', 'Servicio de estudios de ECG', 1);

-- ============================================================
-- HOSPITALES
-- ============================================================

INSERT INTO `hospital` (`UNI_ORG`, `NOMUO`, `DIRECCION`, `DIRECTOR`, `TELEFONO`) VALUES
('H001', 'Hospital Integral Saltillo Centro', 'Blvd. Venustiano Carranza 1200, Saltillo, Coahuila', 'Dr. Fernando Garza Ibarra', 8444123456),
('H002', 'Hospital Regional Norte', 'Carretera Saltillo-Monterrey Km 8, Ramos Arizpe, Coahuila', 'Dra. Patricia Villarreal Soto', 8444987654);

-- ============================================================
-- ÁREAS
-- ============================================================

INSERT INTO `areas` (`ID`, `UBICACION`, `NOMBREAREA`, `ID1`, `HOSPITAL_UNI_ORG`) VALUES
(1, 'Planta Baja - Ala A', 'Consulta Externa', 101, 'H001'),
(2, 'Planta Baja - Ala B', 'Urgencias', 102, 'H001'),
(3, 'Primer Piso - Ala C', 'Hospitalización', 201, 'H001'),
(4, 'Segundo Piso - Ala A', 'Laboratorio e Imagenología', 301, 'H001'),
(5, 'Segundo Piso - Ala B', 'Quirófanos', 401, 'H001'),
(6, 'Planta Baja - Ala Norte', 'Consulta Externa Norte', 501, 'H002'),
(7, 'Primer Piso - Ala Norte', 'Hospitalización Norte', 502, 'H002'),
(8, 'Segundo Piso - Ala Norte', 'Quirófanos Norte', 503, 'H002');

-- ============================================================
-- DEPARTAMENTOS
-- ============================================================

INSERT INTO `departamentos` (`ID`, `NOMBREDEPARTAMENTO`, `UBICACION`, `AREAS_ID`) VALUES
(1, 'Admisión y Archivo', 'Planta Baja - Recepción Principal', 1),
(2, 'Enfermería Consulta Externa', 'Planta Baja - Ala A', 1),
(3, 'Urgencias Adultos', 'Planta Baja - Ala B', 2),
(4, 'Hospitalización General', 'Primer Piso - Ala C', 3),
(5, 'Laboratorio Clínico', 'Segundo Piso - Ala A', 4),
(6, 'Imagenología', 'Segundo Piso - Ala A', 4),
(7, 'Cirugía General', 'Segundo Piso - Ala B', 5),
(8, 'Admisión Norte', 'Planta Baja - Ala Norte', 6);

-- ============================================================
-- CONSULTORIOS
-- ============================================================

INSERT INTO `consultorios` (`ID`, `CONSULTORIO`, `UBICACION`, `AREAS_ID`) VALUES
(1, 'Consultorio 101 - Medicina General', 'Planta Baja - Ala A', 1),
(2, 'Consultorio 102 - Nutrición', 'Planta Baja - Ala A', 1),
(3, 'Consultorio 103 - Odontología', 'Planta Baja - Ala A', 1),
(4, 'Consultorio 104 - Psicología', 'Planta Baja - Ala A', 1),
(5, 'Consultorio 201 - Cardiología', 'Planta Baja - Ala A', 1),
(6, 'Consultorio Urgencias 1', 'Planta Baja - Ala B', 2),
(7, 'Consultorio Norte 1', 'Planta Baja - Ala Norte', 6),
(8, 'Consultorio Norte 2', 'Planta Baja - Ala Norte', 6);

-- ============================================================
-- HABITACIONES
-- ============================================================

INSERT INTO `habitaciones` (`ID`, `NOMBREHABITACION`, `UBICACION`, `EQUIPAMIENTO`, `AREAS_ID`) VALUES
(1, 'Habitación 301', 'Primer Piso - Ala C', 'Cama eléctrica, oxígeno, monitor básico', 3),
(2, 'Habitación 302', 'Primer Piso - Ala C', 'Cama eléctrica, oxígeno, baño privado', 3),
(3, 'Habitación 303', 'Primer Piso - Ala C', 'Cama hospitalaria, oxígeno', 3),
(4, 'Habitación 304', 'Primer Piso - Ala C', 'Cama hospitalaria, baño privado', 3),
(5, 'Habitación 305 - Aislamiento', 'Primer Piso - Ala C', 'Cama eléctrica, presión negativa, oxígeno', 3),
(6, 'Habitación Urgencias Observación 1', 'Planta Baja - Ala B', 'Camilla, monitor de signos vitales', 2),
(7, 'Habitación Norte 201', 'Primer Piso - Ala Norte', 'Cama hospitalaria, oxígeno', 7),
(8, 'Habitación Norte 202', 'Primer Piso - Ala Norte', 'Cama eléctrica, oxígeno, baño privado', 7);

-- ============================================================
-- LABORATORIOS
-- ============================================================

INSERT INTO `laboratorios` (`ID`, `NOMBRELABORATORIO`, `UBICACION`, `AREAS_ID`) VALUES
(1, 'Laboratorio de Análisis Clínicos', 'Segundo Piso - Ala A', 4),
(2, 'Laboratorio de Microbiología', 'Segundo Piso - Ala A', 4),
(3, 'Imagenología y Rayos X', 'Segundo Piso - Ala A', 4),
(4, 'Gabinete de Cardiología', 'Planta Baja - Ala A', 1);

-- ============================================================
-- QUIRÓFANOS
-- ============================================================

INSERT INTO `quirofanos` (`ID`, `NOMBREQUIROFANO`, `UBICACION`, `AREAS_ID`) VALUES
(1, 'Quirófano 1 - Cirugía General', 'Segundo Piso - Ala B', 5),
(2, 'Quirófano 2 - Traumatología', 'Segundo Piso - Ala B', 5),
(3, 'Quirófano 3 - Ginecología', 'Segundo Piso - Ala B', 5),
(4, 'Quirófano Norte 1', 'Segundo Piso - Ala Norte', 8);

-- ============================================================
-- ESPECIALIDADES
-- ============================================================

INSERT INTO `especialidades` (`ID`, `ESPECIALIDAD`) VALUES
(1, 'Medicina General'),
(2, 'Cardiología'),
(3, 'Nutrición Clínica'),
(4, 'Odontología'),
(5, 'Psicología Clínica'),
(6, 'Cirugía General'),
(7, 'Traumatología'),
(8, 'Medicina Interna'),
(9, 'Ginecología'),
(10, 'Pediatría');

-- ============================================================
-- MÉDICOS
-- ============================================================

INSERT INTO `medicos` (`EXPEDIENTE`, `APELLIDOPATERNO`, `APELLIDOMATERNO`, `NOMBRE`, `TELEFONOMOVIL`, `TELEFONOCASA`, `ESPECIALIDADES_ID`, `HOSPITAL_UNI_ORG`) VALUES
(100001, 'Ramírez', 'López', 'Ana Sofía', 8441112233, 8444001001, 1, 'H001'),
(100002, 'Hernández', 'Díaz', 'Carlos Alberto', 8442223344, 8444001002, 2, 'H001'),
(100003, 'Morales', 'Reyes', 'Lucía Fernanda', 8443334455, 8444001003, 3, 'H001'),
(100004, 'Torres', 'Campos', 'Javier', 8444445566, 8444001004, 4, 'H001'),
(100005, 'Gómez', 'Navarro', 'María Elena', 8445556677, 8444001005, 5, 'H001'),
(100006, 'Castillo', 'Ruiz', 'Ricardo', 8446667788, 8444001006, 6, 'H001'),
(100007, 'Santos', 'Mendoza', 'Paola', 8447778899, 8444001007, 7, 'H001'),
(100008, 'Vega', 'Salazar', 'Miguel Ángel', 8448889900, 8444001008, 8, 'H002'),
(100009, 'Aguilar', 'Flores', 'Claudia', 8449990011, 8444001009, 9, 'H002'),
(100010, 'Pérez', 'Ibarra', 'Daniel', 8441011122, 8444001010, 10, 'H002');

INSERT INTO `usuario_medico` (`id`, `usuario_id`, `medico_expediente`) VALUES
(1, 5, 100001),
(2, 6, 100002);

-- ============================================================
-- TIPO DE ESTUDIOS
-- ============================================================

INSERT INTO `tipoestudios` (`ID`, `NOMBREESTUDIO`, `REQUISITOSESTUDIO`, `COSTO`, `LABORATORIOS_ID`) VALUES
(1, 'Biometría Hemática', 'Ayuno no requerido', 250.0000, 1),
(2, 'Química Sanguínea 6 Elementos', 'Ayuno mínimo de 8 horas', 420.0000, 1),
(3, 'Examen General de Orina', 'Primera orina de la mañana en frasco estéril', 180.0000, 1),
(4, 'Cultivo Faríngeo', 'No usar antibióticos 72 horas antes, salvo indicación médica', 520.0000, 2),
(5, 'Radiografía de Tórax', 'Retirar objetos metálicos del tórax', 650.0000, 3),
(6, 'Ultrasonido Abdominal', 'Ayuno de 8 horas', 900.0000, 3),
(7, 'Electrocardiograma en Reposo', 'No aplicar crema corporal antes del estudio', 350.0000, 4);

-- ============================================================
-- TIPO DE PROCEDIMIENTO QUIRÚRGICO
-- ============================================================

INSERT INTO `tipoprocedimiento` (`ID`, `NOMBREPROCEDIMIENTO`, `REQUISITOS`, `ESTATUS`) VALUES
(1, 'Apendicectomía', 'Valoración preoperatoria, ayuno de 8 horas, consentimiento informado', 1),
(2, 'Colecistectomía Laparoscópica', 'Ultrasonido previo, laboratorio completo, ayuno de 8 horas', 1),
(3, 'Reducción de Fractura', 'Radiografías, valoración de traumatología, consentimiento informado', 1),
(4, 'Cesárea', 'Valoración ginecológica, laboratorio preoperatorio, consentimiento informado', 1),
(5, 'Herniorrafia Inguinal', 'Valoración preoperatoria, ayuno de 8 horas', 1);

-- ============================================================
-- CONSULTAS
-- ESTATUS sugerido:
-- 1 = Programada, 2 = En proceso, 3 = Finalizada, 4 = Cancelada
-- ============================================================

INSERT INTO `consultas` (`ID`, `FECHACONSULTA`, `ESTATUS`, `CONSULTORIOS_ID`, `TIPOCONSULTA`, `MEDICOS_EXPEDIENTE`, `TIPOSERVICIO_ID`) VALUES
(1, '2026-05-04 09:00:00', 3, 1, 'Primera vez', 100001, 1),
(2, '2026-05-04 10:00:00', 3, 2, 'Control nutricional', 100003, 2),
(3, '2026-05-05 11:30:00', 3, 3, 'Revisión dental', 100004, 3),
(4, '2026-05-06 12:00:00', 2, 4, 'Terapia individual', 100005, 4),
(5, '2026-05-07 08:30:00', 1, 5, 'Valoración cardiológica', 100002, 5),
(6, '2026-05-07 13:00:00', 1, 1, 'Seguimiento', 100001, 1),
(7, '2026-05-08 09:45:00', 1, 2, 'Plan alimenticio', 100003, 2),
(8, '2026-05-08 15:00:00', 4, 3, 'Limpieza dental', 100004, 3),
(9, '2026-05-09 10:30:00', 1, 7, 'Consulta general norte', 100008, 1),
(10, '2026-05-09 16:00:00', 1, 8, 'Consulta pediátrica', 100010, 1);

-- ============================================================
-- ESTUDIOS
-- ============================================================

INSERT INTO `estudios` (`ID`, `TIPOESTUDIOS_ID`, `MEDICOS_EXPEDIENTE`, `FECHAESTUDIO`, `ESTATUS`) VALUES
(1, 1, 100001, '2026-05-04 08:00:00', 3),
(2, 2, 100001, '2026-05-04 08:20:00', 3),
(3, 7, 100002, '2026-05-05 09:10:00', 3),
(4, 5, 100007, '2026-05-05 10:40:00', 2),
(5, 6, 100008, '2026-05-06 11:00:00', 1),
(6, 3, 100010, '2026-05-07 07:40:00', 1),
(7, 4, 100001, '2026-05-07 12:30:00', 1),
(8, 1, 100009, '2026-05-08 08:15:00', 1);

-- ============================================================
-- INGRESOS Y EGRESOS
-- TIPO sugerido:
-- 1 = Urgencias, 2 = Programado, 3 = Referido
-- ============================================================

INSERT INTO `ingresos` (`ID`, `TIPO`, `FECHAINGRESO`, `OBSERVACIONES`, `MEDICOS_EXPEDIENTE`, `HABITACIONES_ID`) VALUES
(1, 1, '2026-05-01 22:15:00', 'Ingreso por dolor abdominal agudo. Se solicita valoración por cirugía.', 100006, 1),
(2, 2, '2026-05-02 07:30:00', 'Ingreso programado para procedimiento quirúrgico.', 100006, 2),
(3, 1, '2026-05-03 18:40:00', 'Ingreso por traumatismo en extremidad inferior.', 100007, 3),
(4, 3, '2026-05-04 14:20:00', 'Paciente referido para observación y estudios complementarios.', 100008, 4),
(5, 1, '2026-05-05 23:10:00', 'Observación en urgencias por cuadro respiratorio.', 100001, 6),
(6, 2, '2026-05-06 09:00:00', 'Ingreso obstétrico programado.', 100009, 7);

INSERT INTO `egresos` (`ID`, `TIPO`, `INGRESOS_ID`, `FECHAEGRESO`, `OBSERVACIONES`, `HABITACIONES_ID`) VALUES
(1, 1, 1, '2026-05-04 12:00:00', 'Alta por mejoría, seguimiento por consulta externa.', 1),
(2, 1, 2, '2026-05-05 16:30:00', 'Alta postoperatoria sin complicaciones.', 2),
(3, 2, 5, '2026-05-06 08:10:00', 'Egreso voluntario con indicaciones médicas.', 6);

-- ============================================================
-- PROCEDIMIENTOS QUIRÚRGICOS
-- ============================================================

INSERT INTO `procquirurgicos` (`ID`, `TIPO`, `FECHAPROCEDIMIENTO`, `ESTATUS`, `QUIROFANOS_ID`, `MEDICOS_EXPEDIENTE`, `TIPOPROCEDIMIENTO_ID`, `ID1`) VALUES
(1, 1, '2026-05-02 09:00:00', 3, 1, 100006, 1, 1001),
(2, 2, '2026-05-03 11:30:00', 3, 1, 100006, 2, 1002),
(3, 1, '2026-05-04 15:00:00', 2, 2, 100007, 3, 1003),
(4, 2, '2026-05-06 10:30:00', 1, 3, 100009, 4, 1004),
(5, 2, '2026-05-08 08:00:00', 1, 4, 100008, 5, 1005);

-- ============================================================
-- OTROS PROCEDIMIENTOS DE QUIRÓFANO
-- ============================================================

INSERT INTO `otros_procedimientos_quirofano` (`ID`, `DESCRIPCION`, `FECHAPROCEDIMIENTO`, `ESTATUS`, `QUIROFANOS_ID`, `MEDICOS_EXPEDIENTE`, `ID1`) VALUES
(1, 'Curación quirúrgica de herida profunda', '2026-05-02 17:00:00', 3, 1, 100006, 2001),
(2, 'Retiro de puntos bajo sedación', '2026-05-04 09:30:00', 3, 2, 100007, 2002),
(3, 'Lavado quirúrgico por infección localizada', '2026-05-06 13:15:00', 2, 1, 100006, 2003),
(4, 'Colocación de catéter venoso central', '2026-05-07 10:00:00', 1, 3, 100008, 2004);

COMMIT;

-- ============================================================
-- Ajuste de AUTO_INCREMENT después de insertar IDs explícitos
-- ============================================================

ALTER TABLE `areas` AUTO_INCREMENT = 9;
ALTER TABLE `consultas` AUTO_INCREMENT = 11;
ALTER TABLE `consultorios` AUTO_INCREMENT = 9;
ALTER TABLE `habitaciones` AUTO_INCREMENT = 9;
ALTER TABLE `roles` AUTO_INCREMENT = 6;
ALTER TABLE `tipos_servicios` AUTO_INCREMENT = 6;
ALTER TABLE `usuarios` AUTO_INCREMENT = 7;
ALTER TABLE `usuario_medico` AUTO_INCREMENT = 3;
ALTER TABLE `usuario_roles` AUTO_INCREMENT = 7;

-- Verificación rápida opcional
SELECT 'hospital' AS tabla, COUNT(*) AS total FROM hospital
UNION ALL SELECT 'areas', COUNT(*) FROM areas
UNION ALL SELECT 'tipos_servicios', COUNT(*) FROM tipos_servicios
UNION ALL SELECT 'medicos', COUNT(*) FROM medicos
UNION ALL SELECT 'consultas', COUNT(*) FROM consultas
UNION ALL SELECT 'estudios', COUNT(*) FROM estudios
UNION ALL SELECT 'ingresos', COUNT(*) FROM ingresos
UNION ALL SELECT 'egresos', COUNT(*) FROM egresos
UNION ALL SELECT 'procquirurgicos', COUNT(*) FROM procquirurgicos
UNION ALL SELECT 'otros_procedimientos_quirofano', COUNT(*) FROM otros_procedimientos_quirofano;
