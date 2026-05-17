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

$nombreLaboratorio = trim($input["nombreLaboratorio"] ?? "");
$ubicacion = trim($input["ubicacion"] ?? "");
$areasId = $input["areasId"] ?? null;

if (
    $nombreLaboratorio === "" ||
    $areasId === null || !is_numeric($areasId)
) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El nombre del laboratorio y área son obligatorios"
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();


    $sqlCheckArea = "SELECT ID
                     FROM AREAS
                     WHERE ID = :id
                     LIMIT 1";
    $stmtCheckArea = $conn->prepare($sqlCheckArea);
    $stmtCheckArea->execute([
        ":id" => (int)$areasId
    ]);

    if (!$stmtCheckArea->fetch()) {
        jsonResponse(400, [
            "ok" => false,
            "message" => "El área seleccionada no existe"
        ]);
    }

    $sqlCheckNombre = "SELECT ID
                       FROM LABORATORIOS
                       WHERE UPPER(TRIM(NOMBRELABORATORIO)) = UPPER(TRIM(:nombreLaboratorio))
                         AND AREAS_ID = :areasId
                       LIMIT 1";
    $stmtCheckNombre = $conn->prepare($sqlCheckNombre);
    $stmtCheckNombre->execute([
        ":nombreLaboratorio" => $nombreLaboratorio,
        ":areasId" => (int)$areasId
    ]);

    if ($stmtCheckNombre->fetch()) {
        jsonResponse(409, [
            "ok" => false,
            "message" => "Ya existe un laboratorio con ese nombre en esa área"
        ]);
    }

    $sqlNextId = "SELECT COALESCE(MAX(ID), 0) + 1 AS nextId FROM LABORATORIOS";
    $stmtNextId = $conn->query($sqlNextId);
    $nextId = $stmtNextId->fetch()["nextId"];

    $sql = "INSERT INTO LABORATORIOS (
                ID,
                NOMBRELABORATORIO,
                UBICACION,
                AREAS_ID
            ) VALUES (
                :id,
                :nombreLaboratorio,
                :ubicacion,
                :areasId
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":id" => $nextId,
        ":nombreLaboratorio" => $nombreLaboratorio,
        ":ubicacion" => ($ubicacion === "" ? null : $ubicacion),
        ":areasId" => (int)$areasId
    ]);

    jsonResponse(201, [
        "ok" => true,
        "message" => "Laboratorio insertado correctamente"
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al insertar laboratorio",
        "error" => $e->getMessage()
    ]);
}
?>