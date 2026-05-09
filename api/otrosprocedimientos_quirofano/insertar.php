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

requireAnyRole(["Administrador", "Quirofano"]);

$input = json_decode(file_get_contents("php://input"), true);

$id = $input["id"] ?? null;
$descripcion = trim($input["descripcion"] ?? "");
$fechaProcedimiento = trim($input["fechaProcedimiento"] ?? "");
$estatus = $input["estatus"] ?? null;
$quirofanosId = $input["quirofanosId"] ?? null;
$medicosExpediente = $input["medicosExpediente"] ?? null;
$id1 = $input["id1"] ?? null;

if (
    $id === null || !is_numeric($id) ||
    $descripcion === "" ||
    $quirofanosId === null || !is_numeric($quirofanosId) ||
    $medicosExpediente === null || !is_numeric($medicosExpediente) ||
    $id1 === null || !is_numeric($id1)
) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "ID, descripción, quirófano, médico e ID1 son obligatorios"
    ]);
}

if ($estatus !== null && $estatus !== "" && !is_numeric($estatus)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El estatus debe ser numérico"
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    $sqlCheckId = "SELECT ID FROM otros_procedimientos_quirofano WHERE ID = :id LIMIT 1";
    $stmtCheckId = $conn->prepare($sqlCheckId);
    $stmtCheckId->execute([":id" => (int)$id]);

    if ($stmtCheckId->fetch()) {
        jsonResponse(409, [
            "ok" => false,
            "message" => "Ya existe un registro con ese ID"
        ]);
    }

    $sqlCheckQuirofano = "SELECT ID FROM quirofanos WHERE ID = :id LIMIT 1";
    $stmtCheckQuirofano = $conn->prepare($sqlCheckQuirofano);
    $stmtCheckQuirofano->execute([":id" => (int)$quirofanosId]);

    if (!$stmtCheckQuirofano->fetch()) {
        jsonResponse(400, [
            "ok" => false,
            "message" => "El quirófano seleccionado no existe"
        ]);
    }

    $sqlCheckMedico = "SELECT EXPEDIENTE FROM medicos WHERE EXPEDIENTE = :expediente LIMIT 1";
    $stmtCheckMedico = $conn->prepare($sqlCheckMedico);
    $stmtCheckMedico->execute([":expediente" => (int)$medicosExpediente]);

    if (!$stmtCheckMedico->fetch()) {
        jsonResponse(400, [
            "ok" => false,
            "message" => "El médico seleccionado no existe"
        ]);
    }

    $sql = "INSERT INTO otros_procedimientos_quirofano (
                ID,
                DESCRIPCION,
                FECHAPROCEDIMIENTO,
                ESTATUS,
                QUIROFANOS_ID,
                MEDICOS_EXPEDIENTE,
                ID1
            ) VALUES (
                :id,
                :descripcion,
                :fechaProcedimiento,
                :estatus,
                :quirofanosId,
                :medicosExpediente,
                :id1
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":id" => (int)$id,
        ":descripcion" => $descripcion,
        ":fechaProcedimiento" => ($fechaProcedimiento === "" ? null : $fechaProcedimiento),
        ":estatus" => ($estatus === "" ? null : $estatus),
        ":quirofanosId" => (int)$quirofanosId,
        ":medicosExpediente" => (int)$medicosExpediente,
        ":id1" => (int)$id1
    ]);

    jsonResponse(201, [
        "ok" => true,
        "message" => "Otros procedimientos registrado correctamente"
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al registrar otros procedimientos",
        "error" => $e->getMessage()
    ]);
}
?>
