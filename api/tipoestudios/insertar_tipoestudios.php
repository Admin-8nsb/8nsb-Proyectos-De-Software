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

$nombreEstudio = trim($input["nombreEstudio"] ?? "");
$requisitosEstudio = trim($input["requisitosEstudio"] ?? "");
$costo = $input["costo"] ?? null;
$laboratoriosId = $input["laboratoriosId"] ?? null;

if (
    $nombreEstudio === "" ||
    $laboratoriosId === null || !is_numeric($laboratoriosId)
) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El nombre del estudio y el laboratorio son obligatorios"
    ]);
}

if ($costo !== null && $costo !== "" && !is_numeric($costo)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El costo debe ser numérico"
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();


    $sqlCheckLaboratorio = "SELECT ID
                            FROM LABORATORIOS
                            WHERE ID = :id
                            LIMIT 1";
    $stmtCheckLaboratorio = $conn->prepare($sqlCheckLaboratorio);
    $stmtCheckLaboratorio->execute([
        ":id" => (int)$laboratoriosId
    ]);

    if (!$stmtCheckLaboratorio->fetch()) {
        jsonResponse(400, [
            "ok" => false,
            "message" => "El laboratorio seleccionado no existe"
        ]);
    }

    $sqlCheckNombre = "SELECT ID
                       FROM TIPOESTUDIOS
                       WHERE UPPER(TRIM(NOMBREESTUDIO)) = UPPER(TRIM(:nombreEstudio))
                         AND LABORATORIOS_ID = :laboratoriosId
                       LIMIT 1";
    $stmtCheckNombre = $conn->prepare($sqlCheckNombre);
    $stmtCheckNombre->execute([
        ":nombreEstudio" => $nombreEstudio,
        ":laboratoriosId" => (int)$laboratoriosId
    ]);

    if ($stmtCheckNombre->fetch()) {
        jsonResponse(409, [
            "ok" => false,
            "message" => "Ya existe un tipo de estudio con ese nombre en ese laboratorio"
        ]);
    }

    $sqlNextId = "SELECT COALESCE(MAX(ID), 0) + 1 AS nextId FROM TIPOESTUDIOS";
    $stmtNextId = $conn->query($sqlNextId);
    $nextId = $stmtNextId->fetch()["nextId"];

    $sql = "INSERT INTO TIPOESTUDIOS (
                ID,
                NOMBREESTUDIO,
                REQUISITOSESTUDIO,
                COSTO,
                LABORATORIOS_ID
            ) VALUES (
                :id,
                :nombreEstudio,
                :requisitosEstudio,
                :costo,
                :laboratoriosId
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":id" => $nextId,
        ":nombreEstudio" => $nombreEstudio,
        ":requisitosEstudio" => ($requisitosEstudio === "" ? null : $requisitosEstudio),
        ":costo" => $costo,
        ":laboratoriosId" => (int)$laboratoriosId
    ]);

    jsonResponse(201, [
        "ok" => true,
        "message" => "Tipo de estudio insertado correctamente"
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al insertar tipo de estudio",
        "error" => $e->getMessage()
    ]);
}
?>