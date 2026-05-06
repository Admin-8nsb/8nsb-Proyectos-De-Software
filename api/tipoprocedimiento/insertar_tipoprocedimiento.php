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

$nombreProcedimiento = trim($input["nombreProcedimiento"] ?? "");
$requisitos = trim($input["requisitos"] ?? "");
$estatus = $input["estatus"] ?? null;

if (
    $nombreProcedimiento === ""
) {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El nombre del procedimiento es obligatorio"
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


    $sqlCheckNombre = "SELECT ID
                       FROM TIPOPROCEDIMIENTO
                       WHERE UPPER(TRIM(NOMBREPROCEDIMIENTO)) = UPPER(TRIM(:nombreProcedimiento))
                       LIMIT 1";
    $stmtCheckNombre = $conn->prepare($sqlCheckNombre);
    $stmtCheckNombre->execute([
        ":nombreProcedimiento" => $nombreProcedimiento
    ]);

    if ($stmtCheckNombre->fetch()) {
        jsonResponse(409, [
            "ok" => false,
            "message" => "Ya existe un tipo de procedimiento con ese nombre"
        ]);
    }

    $sql = "INSERT INTO TIPOPROCEDIMIENTO (
                NOMBREPROCEDIMIENTO,
                REQUISITOS,
                ESTATUS
            ) VALUES (
                :nombreProcedimiento,
                :nombreProcedimiento,
                :requisitos,
                :estatus
            )";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":nombreProcedimiento" => $nombreProcedimiento,
        ":requisitos" => ($requisitos === "" ? null : $requisitos),
        ":estatus" => ($estatus === "" ? null : $estatus)
    ]);

    jsonResponse(201, [
        "ok" => true,
        "message" => "Tipo de procedimiento insertado correctamente"
    ]);
} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al insertar tipo de procedimiento",
        "error" => $e->getMessage()
    ]);
}
?>