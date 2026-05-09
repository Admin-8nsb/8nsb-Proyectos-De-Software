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

requireAnyRole(["Administrador", "Quirofano"]);

$input = json_decode(file_get_contents("php://input"), true);

$id = $input["id"] ?? null;
$descripcion = trim($input["descripcion"] ?? "");
$fechaProcedimiento = trim($input["fechaProcedimiento"] ?? "");
$estatus = $input["estatus"] ?? null;
$quirofanosId = $input["quirofanosId"] ?? null;
$medicosExpediente = $input["medicosExpediente"] ?? null;
$id1 = $input["id1"] ?? null;

if ($id === null || !is_numeric($id)) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "ID es obligatorio"
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

    if (!$stmtCheckId->fetch()) {
        jsonResponse(404, [
            "ok" => false,
            "message" => "Registro no encontrado"
        ]);
    }

    if ($quirofanosId !== null && is_numeric($quirofanosId)) {
        $sqlCheckQuirofano = "SELECT ID FROM quirofanos WHERE ID = :id LIMIT 1";
        $stmtCheckQuirofano = $conn->prepare($sqlCheckQuirofano);
        $stmtCheckQuirofano->execute([":id" => (int)$quirofanosId]);

        if (!$stmtCheckQuirofano->fetch()) {
            jsonResponse(400, [
                "ok" => false,
                "message" => "El quirófano seleccionado no existe"
            ]);
        }
    }

    if ($medicosExpediente !== null && is_numeric($medicosExpediente)) {
        $sqlCheckMedico = "SELECT EXPEDIENTE FROM medicos WHERE EXPEDIENTE = :expediente LIMIT 1";
        $stmtCheckMedico = $conn->prepare($sqlCheckMedico);
        $stmtCheckMedico->execute([":expediente" => (int)$medicosExpediente]);

        if (!$stmtCheckMedico->fetch()) {
            jsonResponse(400, [
                "ok" => false,
                "message" => "El médico seleccionado no existe"
            ]);
        }
    }

    $updateFields = [];
    $params = [":id" => (int)$id];

    if ($descripcion !== "") {
        $updateFields[] = "DESCRIPCION = :descripcion";
        $params[":descripcion"] = $descripcion;
    }

    if ($fechaProcedimiento !== "") {
        $updateFields[] = "FECHAPROCEDIMIENTO = :fechaProcedimiento";
        $params[":fechaProcedimiento"] = $fechaProcedimiento;
    }

    if ($estatus !== null && $estatus !== "") {
        $updateFields[] = "ESTATUS = :estatus";
        $params[":estatus"] = (int)$estatus;
    }

    if ($quirofanosId !== null && is_numeric($quirofanosId)) {
        $updateFields[] = "QUIROFANOS_ID = :quirofanosId";
        $params[":quirofanosId"] = (int)$quirofanosId;
    }

    if ($medicosExpediente !== null && is_numeric($medicosExpediente)) {
        $updateFields[] = "MEDICOS_EXPEDIENTE = :medicosExpediente";
        $params[":medicosExpediente"] = (int)$medicosExpediente;
    }

    if ($id1 !== null && is_numeric($id1)) {
        $updateFields[] = "ID1 = :id1";
        $params[":id1"] = (int)$id1;
    }

    if (empty($updateFields)) {
        jsonResponse(400, [
            "ok" => false,
            "message" => "No hay campos para actualizar"
        ]);
    }

    $sql = "UPDATE otros_procedimientos_quirofano SET " . implode(", ", $updateFields) . " WHERE ID = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    jsonResponse(200, [
        "ok" => true,
        "message" => "Otros procedimientos actualizado correctamente"
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al actualizar otros procedimientos",
        "error" => $e->getMessage()
    ]);
}
?>
