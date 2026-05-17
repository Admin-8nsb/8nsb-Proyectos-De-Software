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
$fecha_desde = $_GET["fecha_desde"] ?? '';
$fecha_hasta = $_GET["fecha_hasta"] ?? '';

try {
    $database = new Database();
    $conn = $database->getConnection();

    /*
     * INGRESOS
     */
    $whereI = " WHERE 1=1 ";
    $paramsI = [];

    if ($hospital_id !== '') {
        $whereI .= " AND a.HOSPITAL_UNI_ORG = :hospital_id ";
        $paramsI[":hospital_id"] = $hospital_id;
    }

    if ($fecha_desde !== '') {
        $whereI .= " AND i.FECHAINGRESO >= :fecha_desde ";
        $paramsI[":fecha_desde"] = $fecha_desde . " 00:00:00";
    }

    if ($fecha_hasta !== '') {
        $whereI .= " AND i.FECHAINGRESO <= :fecha_hasta ";
        $paramsI[":fecha_hasta"] = $fecha_hasta . " 23:59:59";
    }

    $sqlIngresos = "SELECT 
                        a.NOMBREAREA, 
                        COUNT(*) AS total_ingresos
                    FROM INGRESOS i
                    INNER JOIN HABITACIONES h 
                        ON h.ID = i.HABITACIONES_ID
                    INNER JOIN AREAS a 
                        ON a.ID = h.AREAS_ID
                    $whereI
                    GROUP BY a.ID, a.NOMBREAREA";

    $stmtI = $conn->prepare($sqlIngresos);
    $stmtI->execute($paramsI);
    $resumenIngresos = $stmtI->fetchAll();


    /*
     * EGRESOS
     */
    $whereE = " WHERE 1=1 ";
    $paramsE = [];

    if ($hospital_id !== '') {
        $whereE .= " AND a.HOSPITAL_UNI_ORG = :hospital_id ";
        $paramsE[":hospital_id"] = $hospital_id;
    }

    if ($fecha_desde !== '') {
        $whereE .= " AND e.FECHAEGRESO >= :fecha_desde ";
        $paramsE[":fecha_desde"] = $fecha_desde . " 00:00:00";
    }

    if ($fecha_hasta !== '') {
        $whereE .= " AND e.FECHAEGRESO <= :fecha_hasta ";
        $paramsE[":fecha_hasta"] = $fecha_hasta . " 23:59:59";
    }

    $sqlEgresos = "SELECT 
                       a.NOMBREAREA, 
                       COUNT(*) AS total_egresos
                   FROM EGRESOS e
                   INNER JOIN HABITACIONES h 
                       ON h.ID = e.HABITACIONES_ID
                   INNER JOIN AREAS a 
                       ON a.ID = h.AREAS_ID
                   $whereE
                   GROUP BY a.ID, a.NOMBREAREA";

    $stmtE = $conn->prepare($sqlEgresos);
    $stmtE->execute($paramsE);
    $resumenEgresos = $stmtE->fetchAll();


    /*
     * DETALLE COMBINADO
     */
    $whereDI = " WHERE 1=1 ";
    $whereDE = " WHERE 1=1 ";
    $paramsD = [];

    if ($hospital_id !== '') {
        $whereDI .= " AND a.HOSPITAL_UNI_ORG = :hospital_id_i ";
        $whereDE .= " AND a.HOSPITAL_UNI_ORG = :hospital_id_e ";
        $paramsD[":hospital_id_i"] = $hospital_id;
        $paramsD[":hospital_id_e"] = $hospital_id;
    }

    if ($fecha_desde !== '') {
        $whereDI .= " AND i.FECHAINGRESO >= :fecha_desde_i ";
        $whereDE .= " AND e.FECHAEGRESO >= :fecha_desde_e ";
        $paramsD[":fecha_desde_i"] = $fecha_desde . " 00:00:00";
        $paramsD[":fecha_desde_e"] = $fecha_desde . " 00:00:00";
    }

    if ($fecha_hasta !== '') {
        $whereDI .= " AND i.FECHAINGRESO <= :fecha_hasta_i ";
        $whereDE .= " AND e.FECHAEGRESO <= :fecha_hasta_e ";
        $paramsD[":fecha_hasta_i"] = $fecha_hasta . " 23:59:59";
        $paramsD[":fecha_hasta_e"] = $fecha_hasta . " 23:59:59";
    }

    $sqlDetalle = "SELECT 
                       'Ingreso' AS TIPO_MOV, 
                       i.FECHAINGRESO AS FECHA, 
                       a.NOMBREAREA, 
                       h.NOMBREHABITACION
                   FROM INGRESOS i
                   INNER JOIN HABITACIONES h 
                       ON h.ID = i.HABITACIONES_ID
                   INNER JOIN AREAS a 
                       ON a.ID = h.AREAS_ID
                   $whereDI

                   UNION ALL

                   SELECT 
                       'Egreso' AS TIPO_MOV, 
                       e.FECHAEGRESO AS FECHA, 
                       a.NOMBREAREA, 
                       h.NOMBREHABITACION
                   FROM EGRESOS e
                   INNER JOIN HABITACIONES h 
                       ON h.ID = e.HABITACIONES_ID
                   INNER JOIN AREAS a 
                       ON a.ID = h.AREAS_ID
                   $whereDE

                   ORDER BY FECHA DESC";

    $stmtD = $conn->prepare($sqlDetalle);
    $stmtD->execute($paramsD);
    $registros = $stmtD->fetchAll();

    jsonResponse(200, [
        "ok" => true,
        "data" => [
            "ingresos_por_area" => $resumenIngresos,
            "egresos_por_area" => $resumenEgresos,
            "registros" => $registros
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