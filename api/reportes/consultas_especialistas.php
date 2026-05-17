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
    $especialidad_id = $_GET['especialidad_id'] ?? null;
    $consultorio_id = $_GET['consultorio_id'] ?? null;
    $atencion = $_GET['atencion'] ?? null;
    $fecha_desde = $_GET['fecha_desde'] ?? null;
    $fecha_hasta = $_GET['fecha_hasta'] ?? null;

    $sql = "SELECT 
                c.ID,
                c.FECHACONSULTA,
                c.TIPOCONSULTA,
                con.CONSULTORIO as NOMBRECONSULTORIO,
                e.ESPECIALIDAD,
                m.NOMBRE as MEDICO_NOMBRE,
                m.APELLIDOPATERNO as MEDICO_PATERNO
            FROM consultas c
            INNER JOIN medicos m ON m.EXPEDIENTE = c.MEDICOS_EXPEDIENTE
            INNER JOIN especialidades e ON e.ID = m.ESPECIALIDADES_ID
            INNER JOIN consultorios con ON con.ID = c.CONSULTORIOS_ID
            INNER JOIN areas a ON a.ID = con.AREAS_ID
            WHERE 1=1";

    $params = [];

    if (!empty($hospital_id)) {
        $sql .= " AND a.HOSPITAL_UNI_ORG = :hospital_id";
        $params[':hospital_id'] = $hospital_id;
    }

    if (!empty($especialidad_id)) {
        $sql .= " AND m.ESPECIALIDADES_ID = :especialidad_id";
        $params[':especialidad_id'] = $especialidad_id;
    }

    if (!empty($consultorio_id)) {
        $sql .= " AND c.CONSULTORIOS_ID = :consultorio_id";
        $params[':consultorio_id'] = $consultorio_id;
    }

    if (!empty($atencion)) {
        $sql .= " AND c.TIPOCONSULTA = :atencion";
        $params[':atencion'] = $atencion;
    }

    if (!empty($fecha_desde)) {
        $sql .= " AND DATE(c.FECHACONSULTA) >= :fecha_desde";
        $params[':fecha_desde'] = $fecha_desde;
    }

    if (!empty($fecha_hasta)) {
        $sql .= " AND DATE(c.FECHACONSULTA) <= :fecha_hasta";
        $params[':fecha_hasta'] = $fecha_hasta;
    }

    $sql .= " ORDER BY c.FECHACONSULTA DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtenemos el resumen agrupado por Especialidad, Consultorio y Tipo de Atención
    $sqlResumen = "SELECT 
                    e.ESPECIALIDAD,
                    con.CONSULTORIO as NOMBRECONSULTORIO,
                    c.TIPOCONSULTA,
                    COUNT(c.ID) as total_consultas
                FROM consultas c
                INNER JOIN medicos m ON m.EXPEDIENTE = c.MEDICOS_EXPEDIENTE
                INNER JOIN especialidades e ON e.ID = m.ESPECIALIDADES_ID
                INNER JOIN consultorios con ON con.ID = c.CONSULTORIOS_ID
                INNER JOIN areas a ON a.ID = con.AREAS_ID
                WHERE 1=1";

    if (!empty($hospital_id)) {
        $sqlResumen .= " AND a.HOSPITAL_UNI_ORG = :hospital_id";
    }

    if (!empty($especialidad_id)) {
        $sqlResumen .= " AND m.ESPECIALIDADES_ID = :especialidad_id";
    }

    if (!empty($consultorio_id)) {
        $sqlResumen .= " AND c.CONSULTORIOS_ID = :consultorio_id";
    }

    if (!empty($atencion)) {
        $sqlResumen .= " AND c.TIPOCONSULTA = :atencion";
    }

    if (!empty($fecha_desde)) {
        $sqlResumen .= " AND DATE(c.FECHACONSULTA) >= :fecha_desde";
    }

    if (!empty($fecha_hasta)) {
        $sqlResumen .= " AND DATE(c.FECHACONSULTA) <= :fecha_hasta";
    }

    $sqlResumen .= " GROUP BY e.ID, con.ID, c.TIPOCONSULTA ORDER BY total_consultas DESC";

    $stmtResumen = $conn->prepare($sqlResumen);
    $stmtResumen->execute($params);
    $resumenData = $stmtResumen->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(200, [
        "ok" => true,
        "data" => [
            "total" => count($data),
            "registros" => $data,
            "resumen" => $resumenData
        ]
    ]);

} catch (Throwable $e) {
    jsonResponse(500, [
        "ok" => false,
        "message" => "Error al cargar reporte de consultas de especialistas",
        "error" => $e->getMessage()
    ]);
}
?>
