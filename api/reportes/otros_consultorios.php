<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helpers/auth.php";
require_once __DIR__ . "/../../helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    jsonResponse(405, [
        "ok" => false,
        "message" => "Método no permitido"
    ]);
}

requireLogin();

try {
    $database = new Database();
    $conn = $database->getConnection();

    $hospital_id = $_GET['hospital_id'] ?? null;
    $servicio_id = $_GET['servicio_id'] ?? null;
    $consultorio_id = $_GET['consultorio_id'] ?? null;
    $atencion = $_GET['atencion'] ?? null;

    $sql = "SELECT 
                c.ID,
                c.FECHACONSULTA,
                c.TIPOCONSULTA,
                con.CONSULTORIO as NOMBRECONSULTORIO,
                ts.NOMBRESERVICIO,
                m.NOMBRE as MEDICO_NOMBRE,
                m.APELLIDOPATERNO as MEDICO_PATERNO
            FROM consultas c
            INNER JOIN tipos_servicios ts ON ts.ID = c.TIPOSERVICIO_ID
            INNER JOIN consultorios con ON con.ID = c.CONSULTORIOS_ID
            INNER JOIN medicos m ON m.EXPEDIENTE = c.MEDICOS_EXPEDIENTE
            INNER JOIN areas a ON a.ID = con.AREAS_ID
            WHERE ts.ID != 1"; // Excluir medicina general (ID=1)

    $params = [];

    if (!empty($hospital_id)) {
        $sql .= " AND a.HOSPITAL_UNI_ORG = :hospital_id";
        $params[':hospital_id'] = $hospital_id;
    }

    if (!empty($servicio_id)) {
        $sql .= " AND c.TIPOSERVICIO_ID = :servicio_id";
        $params[':servicio_id'] = $servicio_id;
    }

    if (!empty($consultorio_id)) {
        $sql .= " AND c.CONSULTORIOS_ID = :consultorio_id";
        $params[':consultorio_id'] = $consultorio_id;
    }

    if (!empty($atencion)) {
        $sql .= " AND c.TIPOCONSULTA = :atencion";
        $params[':atencion'] = $atencion;
    }

    $sql .= " ORDER BY c.FECHACONSULTA DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total = count($data);

    jsonResponse(200, [
        "ok" => true,
        "data" => [
            "total" => $total,
            "registros" => $data
        ]
    ]);

} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al cargar reporte de otros consultorios",
        "error" => $e->getMessage()
    ]);
}
?>
