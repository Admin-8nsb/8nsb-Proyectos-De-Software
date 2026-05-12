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

$hospitalId = !empty($_GET["hospital_id"]) ? $_GET["hospital_id"] : null;
$quirofanoId = !empty($_GET["quirofano_id"]) ? $_GET["quirofano_id"] : null;
$tipoAtencion = !empty($_GET["tipo_atencion"]) ? $_GET["tipo_atencion"] : null;
$tipoProcedimientoId = !empty($_GET["tipo_procedimiento_id"]) ? $_GET["tipo_procedimiento_id"] : null;
$fechaDesde = !empty($_GET["fecha_desde"]) ? $_GET["fecha_desde"] : null;
$fechaHasta = !empty($_GET["fecha_hasta"]) ? $_GET["fecha_hasta"] : null;

// if ($hospitalId !== null) {
//     jsonResponse(400, ["ok" => false, "message" => "El hospital debe ser numérico"]);
// }

if ($quirofanoId !== null && $quirofanoId !== "" && !is_numeric($quirofanoId)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El quirófano debe ser numérico"
    ]);
}

if ($tipoAtencion !== null && $tipoAtencion !== "" && !is_numeric($tipoAtencion)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El tipo de atención debe ser numérico"
    ]);
}

if ($tipoProcedimientoId !== null && $tipoProcedimientoId !== "" && !is_numeric($tipoProcedimientoId)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El tipo de procedimiento debe ser numérico"
    ]);
}

if ($tipoAtencion !== null && $tipoAtencion !== "" && !in_array((int)$tipoAtencion, [1, 2], true)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El tipo de atención no está disponible para este reporte"
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $baseFrom = " FROM PROCQUIRURGICOS p
                  INNER JOIN QUIROFANOS q ON q.ID = p.QUIROFANOS_ID
                  INNER JOIN AREAS a ON a.ID = q.AREAS_ID
                  INNER JOIN HOSPITAL h ON h.UNI_ORG = a.HOSPITAL_UNI_ORG
                  INNER JOIN TIPOPROCEDIMIENTO tp ON tp.ID = p.TIPOPROCEDIMIENTO_ID
                  INNER JOIN MEDICOS m ON m.EXPEDIENTE = p.MEDICOS_EXPEDIENTE";

    $where = ["1=1"];
    $params = [];

    $where[] = "p.TIPO IN (1, 2)";

    if (!empty($hospitalId)) {
        $where[] = "a.HOSPITAL_UNI_ORG = :hospital_id";
        $params[":hospital_id"] = (string)$hospitalId;
    }

    if (!empty($quirofanoId)) {
        $where[] = "p.QUIROFANOS_ID = :quirofano_id";
        $params[":quirofano_id"] = (int)$quirofanoId;
    }

    if (!empty($tipoAtencion)) {
        $where[] = "p.TIPO = :tipo_atencion";
        $params[":tipo_atencion"] = (int)$tipoAtencion;
    }

    if (!empty($tipoProcedimientoId)) {
        $where[] = "p.TIPOPROCEDIMIENTO_ID = :tipo_procedimiento_id";
        $params[":tipo_procedimiento_id"] = (int)$tipoProcedimientoId;
    }

    if (!empty($fechaDesde)) {
        $where[] = "DATE(p.FECHAPROCEDIMIENTO) >= :fecha_desde";
        $params[":fecha_desde"] = $fechaDesde;
    }

    if (!empty($fechaHasta)) {
        $where[] = "DATE(p.FECHAPROCEDIMIENTO) <= :fecha_hasta";
        $params[":fecha_hasta"] = $fechaHasta;
    }

    $whereSql = " WHERE " . implode(" AND ", $where);

    $sqlResumen = "SELECT
                        COUNT(*) AS total,
                        SUM(CASE WHEN p.TIPO = 1 THEN 1 ELSE 0 END) AS partos,
                        SUM(CASE WHEN p.TIPO = 2 THEN 1 ELSE 0 END) AS cirugias
                   " . $baseFrom . $whereSql;

    $stmtResumen = $conn->prepare($sqlResumen);
    $stmtResumen->execute($params);
    $resumen = $stmtResumen->fetch(PDO::FETCH_ASSOC) ?: [
        "total" => 0,
        "partos" => 0,
        "cirugias" => 0
    ];

    $sqlDetallado = "SELECT
                        DATE(p.FECHAPROCEDIMIENTO) AS FECHA,
                        p.TIPO AS TIPO_ATENCION_ID,
                        CASE
                            WHEN p.TIPO = 1 THEN 'Partos'
                            WHEN p.TIPO = 2 THEN 'Cirugías'
                            ELSE 'Sin clasificar'
                        END AS TIPO_ATENCION,
                        q.ID AS QUIROFANOS_ID,
                        q.NOMBREQUIROFANO,
                        tp.ID AS TIPOPROCEDIMIENTO_ID,
                        tp.NOMBREPROCEDIMIENTO,
                        COUNT(*) AS total
                     " . $baseFrom . $whereSql . "
                     GROUP BY DATE(p.FECHAPROCEDIMIENTO), p.TIPO, q.ID, q.NOMBREQUIROFANO, tp.ID, tp.NOMBREPROCEDIMIENTO
                     ORDER BY FECHA DESC, q.NOMBREQUIROFANO ASC, tp.NOMBREPROCEDIMIENTO ASC";

    $stmtDetallado = $conn->prepare($sqlDetallado);
    $stmtDetallado->execute($params);
    $detallado = $stmtDetallado->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(200, [
        "ok" => true,
        "data" => [
            "resumen" => [
                "total" => (int)$resumen["total"],
                "partos" => (int)$resumen["partos"],
                "cirugias" => (int)$resumen["cirugias"]
            ],
            "detallado" => $detallado
        ]
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al generar reporte de procedimientos quirúrgicos",
        "error" => $e->getMessage()
    ]);
}
?>