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

try {
    $database = new Database();
    $conn = $database->getConnection();

    $sql = "SELECT 
                e.ID,
                e.HABITACIONES_ID,
                h.NOMBREHABITACION,
                h.AREAS_ID,
                a.NOMBREAREA,
                a.HOSPITAL_UNI_ORG,
                hosp.NOMUO AS HOSPITAL,
                e.TIPO,
                e.INGRESOS_ID,
                e.FECHAEGRESO,
                e.OBSERVACIONES
            FROM EGRESOS e
            INNER JOIN HABITACIONES h ON h.ID = e.HABITACIONES_ID
            INNER JOIN AREAS a ON a.ID = h.AREAS_ID
            INNER JOIN HOSPITAL hosp ON hosp.UNI_ORG = a.HOSPITAL_UNI_ORG
            ORDER BY e.ID ASC, e.HABITACIONES_ID ASC";

    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll();

    jsonResponse(200, [
        "ok" => true,
        "data" => $data
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al listar egresos",
        "error" => $e->getMessage()
    ]);
}
?>