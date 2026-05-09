<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    jsonResponse(405, [
        "ok" => false,
        "message" => "Método no permitido"
    ]);
}

requireAnyRole(["Administrador", "Quirofano"]);

$input = json_decode(file_get_contents("php://input"), true);

$id = $input["id"] ?? null;

if ($id === null || !is_numeric($id)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "ID es obligatorio y debe ser numérico"
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $sqlCheckId = "SELECT ID FROM otros_procedimientos_quirofano WHERE ID = :id LIMIT 1";
    $stmtCheckId = $conn->prepare($sqlCheckId);
    $stmtCheckId->execute([":id" => (int)$id]);

    if (!$stmtCheckId->fetch()) {
        jsonResponse(404, [
            "ok" => false,
            "message" => "Registro no encontrado"
        ]);
    }

    $sql = "DELETE FROM otros_procedimientos_quirofano WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([":id" => (int)$id]);

    jsonResponse(200, [
        "ok" => true,
        "message" => "Otros procedimientos eliminado correctamente"
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al eliminar otros procedimientos",
        "error" => $e->getMessage()
    ]);
}
?>
