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
                o.ID,
                o.DESCRIPCION,
                o.FECHAPROCEDIMIENTO,
                o.ESTATUS,
                o.QUIROFANOS_ID,
                q.NOMBREQUIROFANO,
                o.MEDICOS_EXPEDIENTE,
                m.NOMBRE,
                m.APELLIDOPATERNO,
                m.APELLIDOMATERNO,
                o.ID1
            FROM otros_procedimientos_quirofano o
            INNER JOIN quirofanos q ON q.ID = o.QUIROFANOS_ID
            INNER JOIN medicos m ON m.EXPEDIENTE = o.MEDICOS_EXPEDIENTE
            ORDER BY o.ID ASC";

    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll();

    jsonResponse(200, [
        "ok" => true,
        "data" => $data
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al listar otros procedimientos",
        "error" => $e->getMessage()
    ]);
}
?>
