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

    $exclude_id = $_GET['exclude_id'] ?? null;

    $sql = "SELECT ID, NOMBRESERVICIO, DESCRIPCION, ESTATUS
            FROM TIPOS_SERVICIOS 
            WHERE ESTATUS = 1";

    $params = [];

    if ($exclude_id !== null && is_numeric($exclude_id)) {
        $sql .= " AND ID != :exclude_id";
        $params[':exclude_id'] = (int)$exclude_id;
    }

    $sql .= " ORDER BY NOMBRESERVICIO ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(200, [
        "ok" => true,
        "data" => $data
    ]);

} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al obtener tipos de servicios",
        "error" => $e->getMessage()
    ]);
}
?>
