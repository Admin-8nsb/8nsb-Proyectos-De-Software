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

$id = $_GET["id"] ?? null;

if ($id === null || !is_numeric($id)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "ID inválido"
    ]);
}

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
            WHERE o.ID = :id
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([":id" => (int)$id]);

    $row = $stmt->fetch();

    if (!$row) {
        jsonResponse(404, [
            "ok" => false,
            "message" => "Registro no encontrado"
        ]);
    }

    jsonResponse(200, [
        "ok" => true,
        "data" => $row
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al obtener registro",
        "error" => $e->getMessage()
    ]);
}
?>
