<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    jsonResponse(405, [
        "ok" => false,
        "message" => "Método no permitido"
    ]);
}

requireLogin();

$hospital_id = $_GET["hospital_id"] ?? '';
$medico_id = $_GET["medico_id"] ?? '';
$fecha_desde = $_GET["fecha_desde"] ?? '';
$fecha_hasta = $_GET["fecha_hasta"] ?? '';

try {
    $database = new Database();
    $conn = $database->getConnection();

    /*
      Buscar consultas de Medicina General o Familiar por nombre del servicio,
      no por ID, para que funcione aunque el ID cambie en la base de datos.
    */
    $where = " WHERE (
                    UPPER(TRIM(ts.NOMBRESERVICIO)) LIKE '%MEDICINA GENERAL%'
                    OR UPPER(TRIM(ts.NOMBRESERVICIO)) LIKE '%MEDICINA FAMILIAR%'
               ) ";

    $params = [];

    if ($hospital_id !== '') {
        $where .= " AND a.HOSPITAL_UNI_ORG = :hospital_id ";
        $params[":hospital_id"] = $hospital_id;
    }

    if ($medico_id !== '') {
        $where .= " AND c.MEDICOS_EXPEDIENTE = :medico_id ";
        $params[":medico_id"] = $medico_id;
    }

    if ($fecha_desde !== '') {
        $where .= " AND c.FECHACONSULTA >= :fecha_desde ";
        $params[":fecha_desde"] = $fecha_desde . " 00:00:00";
    }

    if ($fecha_hasta !== '') {
        $where .= " AND c.FECHACONSULTA <= :fecha_hasta ";
        $params[":fecha_hasta"] = $fecha_hasta . " 23:59:59";
    }

    // Resumen por médico y consultorio
    $sqlResumen = "SELECT 
                        m.EXPEDIENTE,
                        m.NOMBRE,
                        m.APELLIDOPATERNO,
                        m.APELLIDOMATERNO,
                        co.ID AS CONSULTORIO_ID,
                        co.CONSULTORIO,
                        ts.NOMBRESERVICIO,
                        COUNT(*) AS total_consultas
                   FROM CONSULTAS c
                   INNER JOIN MEDICOS m 
                        ON m.EXPEDIENTE = c.MEDICOS_EXPEDIENTE
                   INNER JOIN CONSULTORIOS co 
                        ON co.ID = c.CONSULTORIOS_ID
                   INNER JOIN AREAS a 
                        ON a.ID = co.AREAS_ID
                   INNER JOIN TIPOS_SERVICIOS ts 
                        ON ts.ID = c.TIPOSERVICIO_ID
                   $where
                   GROUP BY 
                        m.EXPEDIENTE,
                        m.NOMBRE,
                        m.APELLIDOPATERNO,
                        m.APELLIDOMATERNO,
                        co.ID,
                        co.CONSULTORIO,
                        ts.NOMBRESERVICIO
                   ORDER BY total_consultas DESC";

    $stmtResumen = $conn->prepare($sqlResumen);
    $stmtResumen->execute($params);
    $resumen = $stmtResumen->fetchAll();

    // Registros detallados
    $sqlDetalle = "SELECT 
                        c.ID,
                        c.FECHACONSULTA,
                        c.TIPOCONSULTA,
                        c.ESTATUS,
                        ts.ID AS TIPOSERVICIO_ID,
                        ts.NOMBRESERVICIO,
                        m.EXPEDIENTE AS MEDICO_EXPEDIENTE,
                        m.NOMBRE AS MEDICO_NOMBRE,
                        m.APELLIDOPATERNO AS MEDICO_PATERNO,
                        m.APELLIDOMATERNO AS MEDICO_MATERNO,
                        co.ID AS CONSULTORIO_ID,
                        co.CONSULTORIO AS NOMBRECONSULTORIO,
                        a.ID AS AREA_ID,
                        a.NOMBREAREA,
                        a.HOSPITAL_UNI_ORG
                   FROM CONSULTAS c
                   INNER JOIN MEDICOS m 
                        ON m.EXPEDIENTE = c.MEDICOS_EXPEDIENTE
                   INNER JOIN CONSULTORIOS co 
                        ON co.ID = c.CONSULTORIOS_ID
                   INNER JOIN AREAS a 
                        ON a.ID = co.AREAS_ID
                   INNER JOIN TIPOS_SERVICIOS ts 
                        ON ts.ID = c.TIPOSERVICIO_ID
                   $where
                   ORDER BY c.FECHACONSULTA DESC";

    $stmtDetalle = $conn->prepare($sqlDetalle);
    $stmtDetalle->execute($params);
    $registros = $stmtDetalle->fetchAll();

    jsonResponse(200, [
        "ok" => true,
        "data" => [
            "resumen" => $resumen,
            "registros" => $registros,
            "total" => count($registros)
        ]
    ]);

} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error en el servidor",
        "error" => $e->getMessage()
    ]);
}
?>