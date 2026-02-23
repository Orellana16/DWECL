<?php
// --- MODO DEBUG: ACTIVAR PARA VER ERRORES ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Habilitar CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// --- CHIVATO: Guardar lo que llega en un archivo de texto ---
$log = "Fecha: " . date("Y-m-d H:i:s") . "\n";
$log .= "Método: " . $_SERVER['REQUEST_METHOD'] . "\n";
$log .= "Datos recibidos: " . print_r($_POST, true) . "\n";
$log .= "-----------------------------------\n";
file_put_contents("debug_log.txt", $log, FILE_APPEND);
// -----------------------------------------------------------
// 2. VALIDAR MÉTODO HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir datos (PHP los saca del FormData automáticamente)
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    // Validación básica
    if (empty($nombre) || empty($correo)) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "status" => "error",
            "error" => "Faltan
campos obligatorios"
        ]);
        exit;
    }
    // --- AQUÍ EMPIEZA LA CONEXIÓN A BASE DE DATOS ---

    // Configuración de la BDD (¡Cambia esto por tus credenciales de XAMPP/MAMP!)
    $host = 'localhost';
    $db = 'curso_ajax'; // El nombre que pusimos en el SQL
    $user = 'root'; // Usuario por defecto en XAMPP
    $pass = 'toor'; // Contraseña (suele ser vacía o 'root' en MAMP)
    $charset = 'utf8mb4';
    // Data Source Name (Cadena de conexión)
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar errores como excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devolver arrays asociativos
        PDO::ATTR_EMULATE_PREPARES => false, // Usar sentencias preparadas reales
    ];
    try {
        // 1. Conectar
        $pdo = new PDO($dsn, $user, $pass, $options);
        // 2. Preparar la sentencia (El '?' o ':nombre' son marcadores de seguridad)
        // Usamos IGNORE o comprobación previa para evitar error fatal si el correo ya existe
        $sql = "INSERT INTO usuarios (nombre, correo) VALUES (:nombre,
:correo)";
        $stmt = $pdo->prepare($sql);
        // 3. Ejecutar con los datos reales
        $stmt->execute(['nombre' => $nombre, 'correo' => $correo]);
        // 4. Responder Éxito
        echo json_encode([
            "status" => "ok",
            "mensaje" => "Usuario $nombre registrado correctamente con
ID: " . $pdo->lastInsertId()
        ]);
    } catch (\PDOException $e) {
        // Manejo de errores de BDD (Ej: Correo duplicado código 23000)
        if ($e->getCode() == 23000) {
            http_response_code(409); // Conflict
            echo json_encode([
                "status" => "error",
                "error" => "El
correo ya está registrado"
            ]);
        } else {
            http_response_code(500); // Error interno
            echo json_encode([
                "status" => "error",
                "error" => "Error en
BDD: " . $e->getMessage()
            ]);
        }
    }
} else {
    // Método no permitido
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "error" => "Método no
permitido"
    ]);
}
?>