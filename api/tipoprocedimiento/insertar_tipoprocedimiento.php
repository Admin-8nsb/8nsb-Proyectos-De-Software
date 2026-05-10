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

$nombreProcedimiento = trim($input["nombreprocedimiento"] ?? "");

if ($nombreProcedimiento === "") {
    jsonResponse(400, [
        "ok" => false,
        "message" => "El nombre del procedimiento es obligatorio"
    ]);
}

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Verificar duplicado
    $sqlCheck = "SELECT ID
                 FROM TIPOPROCEDIMIENTO
                 WHERE LOWER(NOMBREPROCEDIMIENTO) = LOWER(:nombre)
                 LIMIT 1";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([":nombre" => $nombreProcedimiento]);

    if ($stmtCheck->fetch()) {
        jsonResponse(400, [
            "ok" => false,
            "message" => "Ya existe un tipo de procedimiento con ese nombre"
        ]);
    }

    // Obtener el siguiente ID manualmente (por si la tabla no tiene AUTO_INCREMENT)
    $sqlMaxId = "SELECT COALESCE(MAX(ID), 0) + 1 AS NEXT_ID FROM TIPOPROCEDIMIENTO";
    $stmtMaxId = $conn->prepare($sqlMaxId);
    $stmtMaxId->execute();
    $nextId = (int) $stmtMaxId->fetchColumn();

    // Intentar primero sin ID (para tablas con AUTO_INCREMENT)
    // Si falla, reintentar con ID explícito (para tablas sin AUTO_INCREMENT)
    try {
        $sql = "INSERT INTO TIPOPROCEDIMIENTO (NOMBREPROCEDIMIENTO)
                VALUES (:nombre)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([":nombre" => $nombreProcedimiento]);
    } catch (Throwable $eInner) {
        $sql = "INSERT INTO TIPOPROCEDIMIENTO (ID, NOMBREPROCEDIMIENTO)
                VALUES (:id, :nombre)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ":id"     => $nextId,
            ":nombre" => $nombreProcedimiento
        ]);
    }

    jsonResponse(201, [
        "ok"      => true,
        "message" => "Tipo de procedimiento creado correctamente"
    ]);

} catch (Throwable $e) {
    jsonResponse(500, [
        "ok"      => false,
        "message" => "Error al crear tipo de procedimiento",
        "error"   => $e->getMessage()
    ]);
}
?>