<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(405, [
        "ok" => false,
        "message" => "Método no permitido"
    ]);
}

requireRole("Administrador");

$input = json_decode(file_get_contents("php://input"), true);

$nombreServicio = trim($input["nombreservicio"] ?? "");
$descripcion = trim($input["descripcion"] ?? "");

if ($nombreServicio === "") {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El nombre del servicio es obligatorio"
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $sqlCheck = "SELECT ID FROM TIPOS_SERVICIOS WHERE UPPER(TRIM(NOMBRESERVICIO)) = UPPER(TRIM(:nombreservicio)) LIMIT 1";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([":nombreservicio" => $nombreServicio]);

    if ($stmtCheck->fetch()) {
        jsonResponse(409, [
            "ok" => false,
            "message" => "Ya existe un servicio con ese nombre"
        ]);
    }

    $sql = "INSERT INTO TIPOS_SERVICIOS (NOMBRESERVICIO, DESCRIPCION, ESTATUS) VALUES (:nombreservicio, :descripcion, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":nombreservicio" => $nombreServicio,
        ":descripcion" => ($descripcion === "" ? null : $descripcion)
    ]);

    jsonResponse(201, [
        "ok" => true,
        "message" => "Servicio creado correctamente"
    ]);

} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al insertar el servicio",
        "error" => $e->getMessage()
    ]);
}
?>
