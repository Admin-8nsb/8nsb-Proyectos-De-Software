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

$especialidad = trim($input["especialidad"] ?? "");

if ($especialidad === "") {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El nombre de la especialidad es obligatorio"
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $sqlCheckNombre = "SELECT ID
                       FROM ESPECIALIDADES
                       WHERE UPPER(TRIM(ESPECIALIDAD)) = UPPER(TRIM(:especialidad))
                       LIMIT 1";

    $stmtCheckNombre = $conn->prepare($sqlCheckNombre);
    $stmtCheckNombre->execute([
        ":especialidad" => $especialidad
    ]);

    if ($stmtCheckNombre->fetch()) {
        jsonResponse(409, [
            "ok" => false,
            "message" => "Ya existe una especialidad con ese nombre"
        ]);
    }

    $sqlNextId = "SELECT COALESCE(MAX(ID), 0) + 1 AS nextId FROM ESPECIALIDADES";
    $stmtNextId = $conn->query($sqlNextId);
    $nextId = $stmtNextId->fetch()["nextId"];

    $sql = "INSERT INTO ESPECIALIDADES (
                ID,
                ESPECIALIDAD
            ) VALUES (
                :id,
                :especialidad
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":id" => $nextId,
        ":especialidad" => $especialidad
    ]);

    jsonResponse(201, [
        "ok" => true,
        "message" => "Especialidad insertada correctamente"
    ]);

} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al insertar especialidad",
        "error" => $e->getMessage()
    ]);
}
?>