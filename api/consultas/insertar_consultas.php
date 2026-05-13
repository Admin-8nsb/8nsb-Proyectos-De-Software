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

$fechaConsulta = trim($input["fechaConsulta"] ?? "");
$estatus = $input["estatus"] ?? null;
$consultoriosId = $input["consultoriosId"] ?? null;
$tipoConsulta = trim($input["tipoConsulta"] ?? "");
$medicosExpediente = $input["medicosExpediente"] ?? null;
$tipoServicioId = $input["tipoServicioId"] ?? null;

if (
    $consultoriosId === null || !is_numeric($consultoriosId) ||
    $medicosExpediente === null || !is_numeric($medicosExpediente)
) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El consultorio y médico son obligatorios"
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


    $sqlCheckConsultorio = "SELECT ID
                            FROM CONSULTORIOS
                            WHERE ID = :id
                            LIMIT 1";
    $stmtCheckConsultorio = $conn->prepare($sqlCheckConsultorio);
    $stmtCheckConsultorio->execute([
        ":id" => (int)$consultoriosId
    ]);

    if (!$stmtCheckConsultorio->fetch()) {
        jsonResponse(400, [
            "ok" => false,
            "message" => "El consultorio seleccionado no existe"
        ]);
    }

    $sqlCheckMedico = "SELECT EXPEDIENTE
                       FROM MEDICOS
                       WHERE EXPEDIENTE = :expediente
                       LIMIT 1";
    $stmtCheckMedico = $conn->prepare($sqlCheckMedico);
    $stmtCheckMedico->execute([
        ":expediente" => (int)$medicosExpediente
    ]);

    if (!$stmtCheckMedico->fetch()) {
        jsonResponse(400, [
            "ok" => false,
            "message" => "El médico seleccionado no existe"
        ]);
    }

    $sql = "INSERT INTO CONSULTAS (
                FECHACONSULTA,
                ESTATUS,
                CONSULTORIOS_ID,
                TIPOCONSULTA,
                MEDICOS_EXPEDIENTE,
                TIPOSERVICIO_ID
            ) VALUES (
                :fechaConsulta,
                :estatus,
                :consultoriosId,
                :tipoConsulta,
                :medicosExpediente,
                :tipoServicioId
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":fechaConsulta" => ($fechaConsulta === "" ? null : $fechaConsulta),
        ":estatus" => ($estatus === "" ? null : $estatus),
        ":consultoriosId" => (int)$consultoriosId,
        ":tipoConsulta" => ($tipoConsulta === "" ? null : $tipoConsulta),
        ":medicosExpediente" => (int)$medicosExpediente,
        ":tipoServicioId" => $tipoServicioId ? (int)$tipoServicioId : null
    ]);

    jsonResponse(201, [
        "ok" => true,
        "message" => "Consulta insertada correctamente"
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al insertar consulta",
        "error" => $e->getMessage()
    ]);
}
?>