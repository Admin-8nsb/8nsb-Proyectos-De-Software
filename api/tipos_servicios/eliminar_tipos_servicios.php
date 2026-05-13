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

requireRole("Administrador");

$input = json_decode(file_get_contents("php://input"), true);

$id = $input["id"] ?? null;

if (!$id || !is_numeric($id)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El ID del servicio es obligatorio"
    ]);
}

// Proteger los primeros 5 que creamos nosotros como "base" para que el sistema no se rompa
if ((int)$id <= 5) {
    jsonResponse(403, [
        "ok" => false,
        "message" => "No se pueden eliminar los servicios base del sistema."
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Verificamos si hay consultas usando este servicio
    $sqlCheckConsultas = "SELECT ID FROM CONSULTAS WHERE TIPOSERVICIO_ID = :id LIMIT 1";
    $stmtCheckConsultas = $conn->prepare($sqlCheckConsultas);
    $stmtCheckConsultas->execute([":id" => (int)$id]);

    if ($stmtCheckConsultas->fetch()) {
        jsonResponse(409, [
            "ok" => false,
            "message" => "No se puede eliminar el servicio porque ya tiene consultas registradas."
        ]);
    }

    $sql = "DELETE FROM TIPOS_SERVICIOS WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([":id" => (int)$id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(404, [
            "ok" => false,
            "message" => "El servicio no existe o ya fue eliminado"
        ]);
    }

    jsonResponse(200, [
        "ok" => true,
        "message" => "Servicio eliminado correctamente"
    ]);

} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al eliminar el servicio",
        "error" => $e->getMessage()
    ]);
}
?>
