<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helpers/auth.php";

requireLogin();

$type = $_GET["type"] ?? '';
$hospital_id = $_GET["hospital_id"] ?? '';
$fecha_desde = $_GET["fecha_desde"] ?? '';
$fecha_hasta = $_GET["fecha_hasta"] ?? '';

// Nombre del archivo basado en el tipo y fecha
$filename = "reporte_" . $type . "_" . date('Ymd_His') . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Para que Excel reconozca caracteres especiales (UTF-8 with BOM)
echo "\xEF\xBB\xBF";

try {
    $database = new Database();
    $conn = $database->getConnection();

    if ($type === 'ingresos_egresos_hosp') {
        exportIngresosEgresosHosp($conn, $hospital_id, $fecha_desde, $fecha_hasta);
    } elseif ($type === 'urgencias') {
        exportUrgencias($conn, $hospital_id);
    } elseif ($type === 'estudios') {
        exportEstudios($conn, $hospital_id);
    } elseif ($type === 'medicina_general') {
        exportMedicinaGeneral($conn, $hospital_id, $fecha_desde, $fecha_hasta);
    } elseif ($type === 'consultas_especialistas') {
        exportConsultasEspecialistas($conn, $hospital_id, $_GET['especialidad_id'] ?? '', $_GET['consultorio_id'] ?? '', $_GET['atencion'] ?? '', $fecha_desde, $fecha_hasta);
    } elseif ($type === 'otros_consultorios') {
        exportOtrosConsultorios($conn, $hospital_id, $_GET['servicio_id'] ?? '', $_GET['consultorio_id'] ?? '', $_GET['atencion'] ?? '');
    } elseif ($type === 'procedimientos') {
        exportProcedimientos($conn, $hospital_id, $_GET['quirofano_id'] ?? '', $_GET['tipo_atencion'] ?? '', $_GET['tipo_procedimiento_id'] ?? '', $fecha_desde, $fecha_hasta);
    } else {
        echo "Tipo de reporte no soportado para exportación.";
    }

} catch (Throwable $e) {
    echo "Error al generar el reporte: " . $e->getMessage();
}

function exportOtrosConsultorios($conn, $hospital_id, $servicio_id, $consultorio_id, $atencion) {
    $where = " WHERE ts.ID != 1 ";
    $params = [];

    if ($hospital_id !== '') { $where .= " AND a.HOSPITAL_UNI_ORG = :hospital_id "; $params[':hospital_id'] = $hospital_id; }
    if ($servicio_id !== '') { $where .= " AND c.TIPOSERVICIO_ID = :servicio_id "; $params[':servicio_id'] = $servicio_id; }
    if ($consultorio_id !== '') { $where .= " AND c.CONSULTORIOS_ID = :consultorio_id "; $params[':consultorio_id'] = $consultorio_id; }
    if ($atencion !== '') { $where .= " AND c.TIPOCONSULTA = :atencion "; $params[':atencion'] = $atencion; }

    $sql = "SELECT c.FECHACONSULTA, c.TIPOCONSULTA, con.CONSULTORIO as NOMBRECONSULTORIO, ts.NOMBRESERVICIO, m.NOMBRE as MEDICO_NOMBRE, m.APELLIDOPATERNO as MEDICO_PATERNO
            FROM consultas c
            INNER JOIN tipos_servicios ts ON ts.ID = c.TIPOSERVICIO_ID
            INNER JOIN consultorios con ON con.ID = c.CONSULTORIOS_ID
            INNER JOIN medicos m ON m.EXPEDIENTE = c.MEDICOS_EXPEDIENTE
            INNER JOIN areas a ON a.ID = con.AREAS_ID
            $where ORDER BY c.FECHACONSULTA DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>";
    echo "<thead><tr style='background-color: #4b5563; color: white;'><th colspan='5'>REPORTE DE OTROS CONSULTORIOS</th></tr>";
    echo "<tr style='background-color: #6b7280; color: white;'><th>Fecha</th><th>Tipo Servicio</th><th>Consultorio</th><th>Médico</th><th>Tipo Atención</th></tr></thead><tbody>";

    foreach ($registros as $r) {
        echo "<tr><td>{$r['FECHACONSULTA']}</td><td>{$r['NOMBRESERVICIO']}</td><td>{$r['NOMBRECONSULTORIO']}</td><td>Dr. {$r['MEDICO_NOMBRE']} {$r['MEDICO_PATERNO']}</td><td>{$r['TIPOCONSULTA']}</td></tr>";
    }
    if (empty($registros)) echo "<tr><td colspan='5'>No hay registros.</td></tr>";
    echo "</tbody></table>";
}

function exportProcedimientos($conn, $hospital_id, $quirofano_id, $tipo_atencion, $tipo_procedimiento_id, $fecha_desde, $fecha_hasta) {
    $where = " WHERE p.TIPO IN (1, 2) ";
    $params = [];

    if ($hospital_id !== '') { $where .= " AND a.HOSPITAL_UNI_ORG = :hospital_id "; $params[':hospital_id'] = $hospital_id; }
    if ($quirofano_id !== '') { $where .= " AND p.QUIROFANOS_ID = :quirofano_id "; $params[':quirofano_id'] = $quirofano_id; }
    if ($tipo_atencion !== '') { $where .= " AND p.TIPO = :tipo_atencion "; $params[':tipo_atencion'] = $tipo_atencion; }
    if ($tipo_procedimiento_id !== '') { $where .= " AND p.TIPOPROCEDIMIENTO_ID = :tipo_procedimiento_id "; $params[':tipo_procedimiento_id'] = $tipo_procedimiento_id; }
    if ($fecha_desde !== '') { $where .= " AND DATE(p.FECHAPROCEDIMIENTO) >= :fecha_desde "; $params[':fecha_desde'] = $fecha_desde; }
    if ($fecha_hasta !== '') { $where .= " AND DATE(p.FECHAPROCEDIMIENTO) <= :fecha_hasta "; $params[':fecha_hasta'] = $fecha_hasta; }

    $sql = "SELECT DATE(p.FECHAPROCEDIMIENTO) AS FECHA, CASE WHEN p.TIPO = 1 THEN 'Partos' ELSE 'Cirugías' END AS TIPO_ATENCION, q.NOMBREQUIROFANO, tp.NOMBREPROCEDIMIENTO, COUNT(*) AS total
            FROM PROCQUIRURGICOS p
            INNER JOIN QUIROFANOS q ON q.ID = p.QUIROFANOS_ID
            INNER JOIN AREAS a ON a.ID = q.AREAS_ID
            INNER JOIN TIPOPROCEDIMIENTO tp ON tp.ID = p.TIPOPROCEDIMIENTO_ID
            $where 
            GROUP BY DATE(p.FECHAPROCEDIMIENTO), p.TIPO, q.ID, tp.ID
            ORDER BY FECHA DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>";
    echo "<thead><tr style='background-color: #7c3aed; color: white;'><th colspan='5'>REPORTE DE CIRUGÍAS Y PARTOS (QUIRÓFANOS)</th></tr>";
    echo "<tr style='background-color: #8b5cf6; color: white;'><th>Fecha</th><th>Quirófano</th><th>Tipo</th><th>Procedimiento</th><th>Cantidad</th></tr></thead><tbody>";

    foreach ($registros as $r) {
        echo "<tr><td>{$r['FECHA']}</td><td>{$r['NOMBREQUIROFANO']}</td><td>{$r['TIPO_ATENCION']}</td><td>{$r['NOMBREPROCEDIMIENTO']}</td><td style='text-align: center;'>{$r['total']}</td></tr>";
    }
    if (empty($registros)) echo "<tr><td colspan='5'>No hay registros.</td></tr>";
    echo "</tbody></table>";
}

function exportMedicinaGeneral($conn, $hospital_id, $fecha_desde, $fecha_hasta) {
    $where = " WHERE (UPPER(TRIM(ts.NOMBRESERVICIO)) LIKE '%MEDICINA GENERAL%' OR UPPER(TRIM(ts.NOMBRESERVICIO)) LIKE '%MEDICINA FAMILIAR%') ";
    $params = [];

    if ($hospital_id !== '') {
        $where .= " AND a.HOSPITAL_UNI_ORG = :hospital_id ";
        $params[":hospital_id"] = $hospital_id;
    }
    if ($fecha_desde !== '') {
        $where .= " AND c.FECHACONSULTA >= :fecha_desde ";
        $params[":fecha_desde"] = $fecha_desde . " 00:00:00";
    }
    if ($fecha_hasta !== '') {
        $where .= " AND c.FECHACONSULTA <= :fecha_hasta ";
        $params[":fecha_hasta"] = $fecha_hasta . " 23:59:59";
    }

    $sqlDetalle = "SELECT c.FECHACONSULTA, c.TIPOCONSULTA, m.NOMBRE AS MEDICO_NOMBRE, m.APELLIDOPATERNO AS MEDICO_PATERNO, co.CONSULTORIO AS NOMBRECONSULTORIO
                   FROM CONSULTAS c
                   INNER JOIN MEDICOS m ON m.EXPEDIENTE = c.MEDICOS_EXPEDIENTE
                   INNER JOIN CONSULTORIOS co ON co.ID = c.CONSULTORIOS_ID
                   INNER JOIN AREAS a ON a.ID = co.AREAS_ID
                   INNER JOIN TIPOS_SERVICIOS ts ON ts.ID = c.TIPOSERVICIO_ID
                   $where ORDER BY c.FECHACONSULTA DESC";

    $stmt = $conn->prepare($sqlDetalle);
    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>";
    echo "<thead><tr style='background-color: #1e3a8a; color: white;'><th colspan='4'>REPORTE DE MEDICINA GENERAL / FAMILIAR</th></tr>";
    echo "<tr style='background-color: #3b82f6; color: white;'><th>Fecha</th><th>Médico</th><th>Consultorio</th><th>Tipo Atención</th></tr></thead><tbody>";

    foreach ($registros as $r) {
        echo "<tr><td>{$r['FECHACONSULTA']}</td><td>Dr. {$r['MEDICO_NOMBRE']} {$r['MEDICO_PATERNO']}</td><td>{$r['NOMBRECONSULTORIO']}</td><td>{$r['TIPOCONSULTA']}</td></tr>";
    }
    if (empty($registros)) echo "<tr><td colspan='4'>No hay registros.</td></tr>";
    echo "</tbody></table>";
}

function exportConsultasEspecialistas($conn, $hospital_id, $especialidad_id, $consultorio_id, $atencion, $fecha_desde, $fecha_hasta) {
    $where = " WHERE 1=1 ";
    $params = [];

    if ($hospital_id !== '') { $where .= " AND a.HOSPITAL_UNI_ORG = :hospital_id "; $params[':hospital_id'] = $hospital_id; }
    if ($especialidad_id !== '') { $where .= " AND m.ESPECIALIDADES_ID = :especialidad_id "; $params[':especialidad_id'] = $especialidad_id; }
    if ($consultorio_id !== '') { $where .= " AND c.CONSULTORIOS_ID = :consultorio_id "; $params[':consultorio_id'] = $consultorio_id; }
    if ($atencion !== '') { $where .= " AND c.TIPOCONSULTA = :atencion "; $params[':atencion'] = $atencion; }
    if ($fecha_desde !== '') { $where .= " AND DATE(c.FECHACONSULTA) >= :fecha_desde "; $params[':fecha_desde'] = $fecha_desde; }
    if ($fecha_hasta !== '') { $where .= " AND DATE(c.FECHACONSULTA) <= :fecha_hasta "; $params[':fecha_hasta'] = $fecha_hasta; }

    $sql = "SELECT c.FECHACONSULTA, c.TIPOCONSULTA, con.CONSULTORIO as NOMBRECONSULTORIO, e.ESPECIALIDAD, m.NOMBRE as MEDICO_NOMBRE, m.APELLIDOPATERNO as MEDICO_PATERNO
            FROM consultas c
            INNER JOIN medicos m ON m.EXPEDIENTE = c.MEDICOS_EXPEDIENTE
            INNER JOIN especialidades e ON e.ID = m.ESPECIALIDADES_ID
            INNER JOIN consultorios con ON con.ID = c.CONSULTORIOS_ID
            INNER JOIN areas a ON a.ID = con.AREAS_ID
            $where ORDER BY c.FECHACONSULTA DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>";
    echo "<thead><tr style='background-color: #064e3b; color: white;'><th colspan='5'>REPORTE DE CONSULTAS DE ESPECIALISTAS</th></tr>";
    echo "<tr style='background-color: #10b981; color: white;'><th>Fecha</th><th>Especialidad</th><th>Consultorio</th><th>Médico</th><th>Tipo Atención</th></tr></thead><tbody>";

    foreach ($registros as $r) {
        echo "<tr><td>{$r['FECHACONSULTA']}</td><td>{$r['ESPECIALIDAD']}</td><td>{$r['NOMBRECONSULTORIO']}</td><td>Dr. {$r['MEDICO_NOMBRE']} {$r['MEDICO_PATERNO']}</td><td>{$r['TIPOCONSULTA']}</td></tr>";
    }
    if (empty($registros)) echo "<tr><td colspan='5'>No hay registros.</td></tr>";
    echo "</tbody></table>";
}

function exportEstudios($conn, $hospital_id) {
    $sql = "SELECT 
                l.NOMBRELABORATORIO,
                te.NOMBREESTUDIO,
                COUNT(e.ID) AS total,
                h.NOMUO AS HOSPITAL,
                a.NOMBREAREA
            FROM ESTUDIOS e
            INNER JOIN TIPOESTUDIOS te ON te.ID = e.TIPOESTUDIOS_ID
            INNER JOIN LABORATORIOS l ON l.ID = te.LABORATORIOS_ID
            INNER JOIN AREAS a ON a.ID = l.AREAS_ID
            INNER JOIN HOSPITAL h ON h.UNI_ORG = a.HOSPITAL_UNI_ORG
            WHERE 1=1";

    $params = [];
    if ($hospital_id !== '') {
        $sql .= " AND h.UNI_ORG = :hospital_id";
        $params[':hospital_id'] = $hospital_id;
    }

    $sql .= " 
        GROUP BY l.ID, te.ID
        ORDER BY l.NOMBRELABORATORIO ASC, total DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>";
    echo "<thead>";
    echo "<tr style='background-color: #5b21b6; color: white;'>";
    echo "<th colspan='4'>REPORTE DE ESTUDIOS PARACLÍNICOS</th>";
    echo "</tr>";
    echo "<tr style='background-color: #8b5cf6; color: white;'>";
    echo "<th>Laboratorio / Departamento</th>";
    echo "<th>Área Relacionada</th>";
    echo "<th>Tipo de Estudio</th>";
    echo "<th>Total Realizados</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($registros as $r) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($r['NOMBRELABORATORIO']) . "</td>";
        echo "<td>" . htmlspecialchars($r['NOMBREAREA']) . "</td>";
        echo "<td>" . htmlspecialchars($r['NOMBREESTUDIO']) . "</td>";
        echo "<td style='text-align: center;'>" . htmlspecialchars($r['total']) . "</td>";
        echo "</tr>";
    }

    if (empty($registros)) {
        echo "<tr><td colspan='4'>No hay registros para los filtros seleccionados.</td></tr>";
    }

    echo "</tbody>";
    echo "</table>";
}

function exportUrgencias($conn, $hospital_id) {
    $where = " WHERE a.NOMBREAREA LIKE '%Urgencias%' ";
    $params = [];
    if ($hospital_id !== '') {
        $where .= " AND a.HOSPITAL_UNI_ORG = :hospital_id ";
        $params[":hospital_id"] = $hospital_id;
    }

    $sqlRecientes = "SELECT 
                        'Ingreso' as tipo_mov, 
                        i.FECHAINGRESO as fecha, 
                        h.NOMBREHABITACION 
                    FROM INGRESOS i
                    JOIN HABITACIONES h ON i.HABITACIONES_ID = h.ID
                    JOIN AREAS a ON h.AREAS_ID = a.ID
                    $where
                    
                    UNION ALL
                    
                    SELECT 
                        'Egreso' as tipo_mov, 
                        e.FECHAEGRESO as fecha, 
                        h.NOMBREHABITACION 
                    FROM EGRESOS e
                    JOIN HABITACIONES h ON e.HABITACIONES_ID = h.ID
                    JOIN AREAS a ON h.AREAS_ID = a.ID
                    $where
                    
                    ORDER BY fecha DESC";

    $stmt = $conn->prepare($sqlRecientes);
    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>";
    echo "<thead>";
    echo "<tr style='background-color: #991b1b; color: white;'>";
    echo "<th colspan='3'>REPORTE DETALLADO DE URGENCIAS</th>";
    echo "</tr>";
    echo "<tr style='background-color: #ef4444; color: white;'>";
    echo "<th>Tipo</th>";
    echo "<th>Fecha y Hora</th>";
    echo "<th>Habitación</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($registros as $r) {
        $color = ($r['tipo_mov'] === 'Ingreso') ? '#eff6ff' : '#fef2f2';
        echo "<tr style='background-color: $color;'>";
        echo "<td>" . htmlspecialchars($r['tipo_mov']) . "</td>";
        echo "<td>" . htmlspecialchars($r['fecha']) . "</td>";
        echo "<td>" . htmlspecialchars($r['NOMBREHABITACION']) . "</td>";
        echo "</tr>";
    }

    if (empty($registros)) {
        echo "<tr><td colspan='3'>No hay registros para los filtros seleccionados.</td></tr>";
    }

    echo "</tbody>";
    echo "</table>";
}

function exportIngresosEgresosHosp($conn, $hospital_id, $fecha_desde, $fecha_hasta) {
    // Reutilizamos la lógica de filtros
    $whereDI = " WHERE 1=1 ";
    $whereDE = " WHERE 1=1 ";
    $paramsD = [];

    if ($hospital_id !== '') {
        $whereDI .= " AND a.HOSPITAL_UNI_ORG = :hospital_id_i ";
        $whereDE .= " AND a.HOSPITAL_UNI_ORG = :hospital_id_e ";
        $paramsD[":hospital_id_i"] = $hospital_id;
        $paramsD[":hospital_id_e"] = $hospital_id;
    }

    if ($fecha_desde !== '') {
        $whereDI .= " AND i.FECHAINGRESO >= :fecha_desde_i ";
        $whereDE .= " AND e.FECHAEGRESO >= :fecha_desde_e ";
        $paramsD[":fecha_desde_i"] = $fecha_desde . " 00:00:00";
        $paramsD[":fecha_desde_e"] = $fecha_desde . " 00:00:00";
    }

    if ($fecha_hasta !== '') {
        $whereDI .= " AND i.FECHAINGRESO <= :fecha_hasta_i ";
        $whereDE .= " AND e.FECHAEGRESO <= :fecha_hasta_e ";
        $paramsD[":fecha_hasta_i"] = $fecha_hasta . " 23:59:59";
        $paramsD[":fecha_hasta_e"] = $fecha_hasta . " 23:59:59";
    }

    $sqlDetalle = "SELECT 
                       'Ingreso' AS TIPO_MOV, 
                       i.FECHAINGRESO AS FECHA, 
                       a.NOMBREAREA, 
                       h.NOMBREHABITACION
                   FROM INGRESOS i
                   INNER JOIN HABITACIONES h 
                       ON h.ID = i.HABITACIONES_ID
                   INNER JOIN AREAS a 
                       ON a.ID = h.AREAS_ID
                   $whereDI

                   UNION ALL

                   SELECT 
                       'Egreso' AS TIPO_MOV, 
                       e.FECHAEGRESO AS FECHA, 
                       a.NOMBREAREA, 
                       h.NOMBREHABITACION
                   FROM EGRESOS e
                   INNER JOIN HABITACIONES h 
                       ON h.ID = e.HABITACIONES_ID
                   INNER JOIN AREAS a 
                       ON a.ID = h.AREAS_ID
                   $whereDE

                   ORDER BY FECHA DESC";

    $stmtD = $conn->prepare($sqlDetalle);
    $stmtD->execute($paramsD);
    $registros = $stmtD->fetchAll(PDO::FETCH_ASSOC);

    // Generar HTML para Excel
    echo "<table border='1'>";
    echo "<thead>";
    echo "<tr style='background-color: #1e40af; color: white;'>";
    echo "<th colspan='4'>REPORTE DE INGRESOS Y EGRESOS HOSPITALARIOS</th>";
    echo "</tr>";
    echo "<tr style='background-color: #3b82f6; color: white;'>";
    echo "<th>Tipo de Movimiento</th>";
    echo "<th>Fecha y Hora</th>";
    echo "<th>Área / Servicio</th>";
    echo "<th>Habitación</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($registros as $r) {
        $color = ($r['TIPO_MOV'] === 'Ingreso') ? '#eff6ff' : '#fef2f2';
        echo "<tr style='background-color: $color;'>";
        echo "<td>" . htmlspecialchars($r['TIPO_MOV']) . "</td>";
        echo "<td>" . htmlspecialchars($r['FECHA']) . "</td>";
        echo "<td>" . htmlspecialchars($r['NOMBREAREA']) . "</td>";
        echo "<td>" . htmlspecialchars($r['NOMBREHABITACION']) . "</td>";
        echo "</tr>";
    }

    if (empty($registros)) {
        echo "<tr><td colspan='4'>No hay registros para los filtros seleccionados.</td></tr>";
    }

    echo "</tbody>";
    echo "</table>";
}
?>