<?php
/**
 * Seeder de prueba
 * 
 * Ejecutar en terminal con: php seeder.php
 * Carga datos de prueba para verificar el módulo de procedimientos quirúrgicos
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';

class Seeder {
    private $pdo;
    private $hospitalId = 'H001';

    public function __construct(PDO $pdo = null) {
        if ($pdo === null) {
            $db = new Database();
            $this->pdo = $db->getConnection();
        } else {
            $this->pdo = $pdo;
        }
        
        if ($this->pdo === null) {
            throw new Exception("No se pudo conectar a la base de datos");
        }
    }

    /**
     * Ejecuta el seeder
     */
    public function run() {
        try {
            echo "\n=== Iniciando Seeder de Hospital HIS ===\n\n";

            // Limpiar datos anteriores
            $this->cleanData();

            // Insertar datos
            $this->seedHospital();
            $this->seedAreas();
            $this->seedEspecialidades();
            $this->seedMedicos();
            $this->seedQuirofanos();
            $this->seedTiposProcedimiento();
            $this->seedProcedimientosQuirurgicos();

            echo "\n✅ ¡Seeder completado exitosamente!\n";
            echo "=== Resumen de datos insertados ===\n";
            $this->showSummary();
            echo "\nAhora puedes iniciar sesión y verificar el módulo de Procedimientos Qx\n\n";

        } catch (Exception $e) {
            echo "\n❌ Error en Seeder: " . $e->getMessage() . "\n\n";
            exit(1);
        }
    }

    /**
     * Limpia datos anteriores de las tablas
     */
    private function cleanData() {
        echo "🧹 Limpiando datos anteriores...\n";

        try {
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

            $tables = [
            'procquirurgicos',
            'tipoprocedimiento',
            'quirofanos',
            'medicos',
            'especialidades',
            'consultorios',
            'departamentos',
            'habitaciones',
            'laboratorios',
            'areas',
            'hospital'
            ];

            foreach ($tables as $table) {
                try {
                    $this->pdo->exec("TRUNCATE TABLE `$table`");
                } catch (Exception $e) {
                    // Tabla no existe o error, continuar
                }
            }

            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            echo "   ✓ Tablas limpias\n\n";
        } catch (Exception $e) {
            throw new Exception("Error limpiando tablas: " . $e->getMessage());
        }
    }

    /**
     * Inserta datos del hospital
     */
    private function seedHospital() {
        echo "📋 Insertando Hospital...\n";

        $stmt = $this->pdo->prepare("
            INSERT INTO hospital (UNI_ORG, NOMUO, DIRECCION, DIRECTOR, TELEFONO) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $hospitals = [
            ['H001', 'Hospital General Central', 'Av. Principal 123, Centro', 'Dr. Roberto García López', 5551234567],
            ['H002', 'Hospital Regional Oriente', 'Calle 5 No. 456, Zona 3', 'Dra. María González Pérez', 5552345678],
        ];

        foreach ($hospitals as $hospital) {
            $stmt->execute($hospital);
        }

        echo "   ✓ 2 hospitales insertados\n\n";
    }

    /**
     * Inserta áreas del hospital
     */
    private function seedAreas() {
        echo "🏢 Insertando Áreas...\n";

        $stmt = $this->pdo->prepare("
            INSERT INTO areas (ID, UBICACION, NOMBREAREA, ID1, HOSPITAL_UNI_ORG) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $areas = [
            [1, 'Planta Baja', 'Quirófano', 1, $this->hospitalId],
            [2, 'Planta Alta', 'Maternidad', 2, $this->hospitalId],
            [3, 'Planta 2', 'Cirugia General', 3, $this->hospitalId],
            [4, 'Sótano', 'Laboratorio', 4, $this->hospitalId],
        ];

        foreach ($areas as $area) {
            $stmt->execute($area);
        }

        echo "   ✓ 4 áreas insertadas\n\n";
    }

    /**
     * Inserta especialidades médicas
     */
    private function seedEspecialidades() {
        echo "🔬 Insertando Especialidades...\n";

        $stmt = $this->pdo->prepare("
            INSERT INTO especialidades (ID, ESPECIALIDAD) 
            VALUES (?, ?)
        ");

        $especialidades = [
            [1, 'Ginecología y Obstetricia'],
            [2, 'Cirugía General'],
            [3, 'Anestesiología'],
            [4, 'Cirugía Pediátrica'],
        ];

        foreach ($especialidades as $esp) {
            $stmt->execute($esp);
        }

        echo "   ✓ 4 especialidades insertadas\n\n";
    }

    /**
     * Inserta médicos
     */
    private function seedMedicos() {
        echo "👨‍⚕️ Insertando Médicos...\n";

        $stmt = $this->pdo->prepare("
            INSERT INTO medicos (EXPEDIENTE, APELLIDOPATERNO, APELLIDOMATERNO, NOMBRE, TELEFONOMOVIL, TELEFONOCASA, ESPECIALIDADES_ID, HOSPITAL_UNI_ORG) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $medicos = [
            [1001, 'González', 'López', 'Carlos', 5551111111, 5559999901, 1, $this->hospitalId],
            [1002, 'Martínez', 'Rodríguez', 'Ana María', 5552222222, 5559999902, 1, $this->hospitalId],
            [1003, 'Hernández', 'García', 'Miguel', 5553333333, 5559999903, 2, $this->hospitalId],
            [1004, 'López', 'Pérez', 'Juan', 5554444444, 5559999904, 2, $this->hospitalId],
            [1005, 'Torres', 'Sánchez', 'Roberto', 5555555555, 5559999905, 3, $this->hospitalId],
            [1006, 'Díaz', 'Cruz', 'Sofia', 5556666666, 5559999906, 4, $this->hospitalId],
        ];

        foreach ($medicos as $medico) {
            $stmt->execute($medico);
        }

        echo "   ✓ 6 médicos insertados\n\n";
    }

    /**
     * Inserta quirófanos
     */
    private function seedQuirofanos() {
        echo "🏥 Insertando Quirófanos...\n";

        $stmt = $this->pdo->prepare("
            INSERT INTO quirofanos (ID, NOMBREQUIROFANO, UBICACION, AREAS_ID) 
            VALUES (?, ?, ?, ?)
        ");

        $quirofanos = [
            [1, 'Quirófano 1', 'Planta Baja - Ala Oeste', 1],
            [2, 'Quirófano 2', 'Planta Baja - Ala Este', 1],
            [3, 'Quirófano Maternidad', 'Planta Alta', 2],
            [4, 'Quirófano Pediátrico', 'Planta 2', 3],
        ];

        foreach ($quirofanos as $qx) {
            $stmt->execute($qx);
        }

        echo "   ✓ 4 quirófanos insertados\n\n";
    }

    /**
     * Inserta tipos de procedimiento
     */
    private function seedTiposProcedimiento() {
        echo "📝 Insertando Tipos de Procedimiento...\n";

        $stmt = $this->pdo->prepare("
            INSERT INTO tipoprocedimiento (ID, NOMBREPROCEDIMIENTO, REQUISITOS, ESTATUS) 
            VALUES (?, ?, ?, ?)
        ");

        $tipos = [
            [1, 'Parto Natural', 'Preparación prenatal, evaluación cardiotocográfica', 1],
            [2, 'Cesárea', 'Evaluación obstétrica, preparación preoperatoria', 1],
            [3, 'Apendicectomía', 'Pruebas de laboratorio, evaluación quirúrgica', 1],
        ];

        foreach ($tipos as $tipo) {
            $stmt->execute($tipo);
        }

        echo "   ✓ 6 tipos de procedimiento insertados\n\n";
    }

    /**
     * Inserta procedimientos quirúrgicos de prueba
     */
    private function seedProcedimientosQuirurgicos() {
        echo "🔪 Insertando Procedimientos Quirúrgicos...\n";

        $stmt = $this->pdo->prepare("
            INSERT INTO procquirurgicos (ID, TIPO, FECHAPROCEDIMIENTO, ESTATUS, QUIROFANOS_ID, MEDICOS_EXPEDIENTE, TIPOPROCEDIMIENTO_ID, ID1) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $procedimientos = [
            // Partos (TIPO = 1)
            [1, 1, '2026-05-01 08:30:00', 1, 3, 1002, 1, 1],
            [2, 1, '2026-05-02 10:15:00', 1, 3, 1001, 2, 1],
            [3, 1, '2026-05-05 14:45:00', 1, 3, 1002, 1, 1],

            // Cirugías (TIPO = 2)
            [4, 2, '2026-05-03 09:00:00', 1, 1, 1003, 3, 1],
            [5, 2, '2026-05-04 11:30:00', 1, 1, 1004, 4, 1],
            [6, 2, '2026-05-06 15:00:00', 1, 2, 1003, 5, 1],
            [7, 2, '2026-05-07 08:00:00', 1, 2, 1004, 3, 1],

        ];

        foreach ($procedimientos as $proc) {
            $stmt->execute($proc);
        }

        echo "   ✓ 7 procedimientos quirúrgicos insertados\n";
        echo "     - 3 Partos\n";
        echo "     - 4 Cirugías\n\n";
    }

    /**
     * Muestra un resumen de los datos insertados
     */
    private function showSummary() {
        $tables = [
            'hospital' => 'Hospitales',
            'areas' => 'Áreas',
            'especialidades' => 'Especialidades',
            'medicos' => 'Médicos',
            'quirofanos' => 'Quirófanos',
            'tipoprocedimiento' => 'Tipos de Procedimiento',
            'procquirurgicos' => 'Procedimientos Quirúrgicos',
        ];

        foreach ($tables as $table => $label) {
            $result = $this->pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $count = $row['count'];
            printf("  • %s: %d\n", $label, $count);
        }
    }
}

// Ejecutar seeder
try {
    $seeder = new Seeder();
    $seeder->run();
} catch (Exception $e) {
    echo "❌ Error al inicializar seeder: " . $e->getMessage() . "\n\n";
    exit(1);
}
