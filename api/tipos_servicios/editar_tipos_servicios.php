<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    jsonResponse(405, [
        "ok" => false,
        "message" => "Método no permitido"
    ]);
}

requireRole("Administrador");

$input = json_decode(file_get_contents("php://input"), true);

$id = $input["id"] ?? null;
$nombreServicio = trim($input["nombreservicio"] ?? "");
$descripcion = trim($input["descripcion"] ?? "");

if (!$id || !is_numeric($id) || $nombreServicio === "") {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El ID y nombre del servicio son obligatorios"
    ]);
}

// Proteger el ID 1 (Medicina General)
if ((int)$id === 1) {
    jsonResponse(403, [
        "ok" => false,
        "message" => "No se puede editar el servicio base (Medicina General)"
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $sqlCheck = "SELECT ID FROM TIPOS_SERVICIOS WHERE UPPER(TRIM(NOMBRESERVICIO)) = UPPER(TRIM(:nombreservicio)) AND ID != :id LIMIT 1";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([
        ":nombreservicio" => $nombreServicio,
        ":id" => (int)$id
    ]);

    if ($stmtCheck->fetch()) {
        jsonResponse(409, [
            "ok" => false,
            "message" => "Ya existe otro servicio con ese nombre"
        ]);
    }

    $sql = "UPDATE TIPOS_SERVICIOS SET NOMBRESERVICIO = :nombreservicio, DESCRIPCION = :descripcion WHERE ID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":nombreservicio" => $nombreServicio,
        ":descripcion" => ($descripcion === "" ? null : $descripcion),
        ":id" => (int)$id
    ]);

    if ($stmt->rowCount() === 0) {
        $sqlCheckExists = "SELECT ID FROM TIPOS_SERVICIOS WHERE ID = :id";
        $stmtExists = $conn->prepare($sqlCheckExists);
        $stmtExists->execute([":id" => (int)$id]);
        if (!$stmtExists->fetch()) {
            jsonResponse(404, [
                "ok" => false,
                "message" => "El servicio no existe"
            ]);
        }
    }

    jsonResponse(200, [
        "ok" => true,
        "message" => "Servicio actualizado correctamente"
    ]);

} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al actualizar el servicio",
        "error" => $e->getMessage()
    ]);
}
?>
