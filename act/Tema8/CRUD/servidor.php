<?php
// 1. CONFIGURACIÓN Y ERRORES
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// --- LOG DE DEPURACIÓN (Como en tu código original) ---
$log = "Fecha: " . date("Y-m-d H:i:s") . " | Método: " . $_SERVER['REQUEST_METHOD'] . "\n";
file_put_contents("debug_log.txt", $log, FILE_APPEND);

// 2. CONEXIÓN A LA BASE DE DATOS
$host = 'localhost';
$db   = 'curso_ajax';
$user = 'root';
$pass = 'toor'; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $metodo = $_SERVER['REQUEST_METHOD'];

    // --- OPERACIÓN: LEER (GET) ---
    if ($metodo === 'GET') {
        $stmt = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
        echo json_encode($stmt->fetchAll());
        exit;
    }

    // --- OPERACIÓN: INSERTAR O ACTUALIZAR (POST) --- 
    if ($metodo === 'POST') {
        $id     = $_POST['id'] ?? null;
        $nombre = $_POST['nombre'] ?? '';
        $correo = $_POST['correo'] ?? '';
        $movil  = $_POST['movil'] ?? '';
        $edad   = $_POST['edad'] ?? 0;
        $idioma = $_POST['idioma'] ?? '';

        // Validación Proyecto B: Edad > 18 
        if ($edad < 18) {
            http_response_code(400);
            echo json_encode(["status" => "error", "error" => "Debes ser mayor de 18 años"]);
            exit;
        }

        if (empty($id)) {
            // INSERTAR NUEVO 
            $sql = "INSERT INTO usuarios (nombre, correo, movil, edad, idioma) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $correo, $movil, $edad, $idioma]);
            echo json_encode(["status" => "ok", "mensaje" => "Usuario creado"]);
        } else {
            // ACTUALIZAR EXISTENTE 
            $sql = "UPDATE usuarios SET nombre=?, correo=?, movil=?, edad=?, idioma=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $correo, $movil, $edad, $idioma, $id]);
            echo json_encode(["status" => "ok", "mensaje" => "Usuario actualizado"]);
        }
        exit;
    }

    // --- OPERACIÓN: BORRAR (DELETE) --- 
    if ($metodo === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "ok", "mensaje" => "Registro eliminado"]);
        }
        exit;
    }

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "error" => $e->getMessage()]);
}
?>