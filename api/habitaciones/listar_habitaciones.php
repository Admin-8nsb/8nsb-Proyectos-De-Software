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

$areaId = $_GET["area_id"] ?? null;
$hospitalId = $_GET["hospital_id"] ?? null;

try {
    $database = new Database();
    $conn = $database->getConnection();

    $sql = "SELECT 
                h.ID,
                h.NOMBREHABITACION,
                h.UBICACION,
                h.EQUIPAMIENTO,
                h.AREAS_ID,
                a.NOMBREAREA,
                a.HOSPITAL_UNI_ORG,
                ho.NOMUO AS HOSPITAL
            FROM HABITACIONES h
            INNER JOIN AREAS a 
                ON a.ID = h.AREAS_ID
            INNER JOIN HOSPITAL ho 
                ON ho.UNI_ORG = a.HOSPITAL_UNI_ORG
            WHERE 1 = 1";

    $params = [];

    if ($areaId !== null && $areaId !== "") {
        $sql .= " AND h.AREAS_ID = :area_id";
        $params[":area_id"] = (int)$areaId;
    }

    if ($hospitalId !== null && $hospitalId !== "") {
        $sql .= " AND a.HOSPITAL_UNI_ORG = :hospital_id";
        $params[":hospital_id"] = $hospitalId;
    }

    $sql .= " ORDER BY h.ID ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $data = $stmt->fetchAll();

    jsonResponse(200, [
        "ok" => true,
        "data" => $data
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al listar habitaciones",
        "error" => $e->getMessage()
    ]);
}
?>