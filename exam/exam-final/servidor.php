<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$host = 'localhost';
$db = 'tienda_ropa'; // Base de datos del examen
$user = 'root';
$pass = 'toorF'; // Cambia según tu configuración
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $metodo = $_SERVER['REQUEST_METHOD'];

    // LEER UN PRODUCTO ESPECÍFICO (O TODOS)
    if ($metodo === "GET") {
        $id = $_GET['id'] ?? null; // Recogemos de la URL

        if ($id) {
            // Si hay ID, filtramos ese producto
            $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch());
        } else {
            // Si no hay ID, devolvemos la lista completa
            $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
            echo json_encode($stmt->fetchAll());
        }
        exit;
    }

    // INSERTAR O ACTUALIZAR
    if ($metodo === 'POST') {
        $id = $_POST['id'] ?? null;
        $codigo = $_POST['codigo'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $talla = $_POST['talla'] ?? '';
        $precio = $_POST['precio'] ?? 0;
        $email = $_POST['email_creador'] ?? '';

        if (empty($id)) {
            // INSERTAR
            $sql = "INSERT INTO productos (codigo, nombre, talla, precio, email_creador) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$codigo, $nombre, $talla, $precio, $email]);
            echo json_encode(["status" => "ok"]);
        } else {
            // ACTUALIZAR
            $sql = "UPDATE productos SET codigo=?, nombre=?, talla=?, precio=?, email_creador=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$codigo, $nombre, $talla, $precio, $email, $id]);
            echo json_encode(["status" => "ok"]);
        }
        exit;
    }

    // BORRAR
    if ($metodo === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["status" => "ok"]);
        }
        exit;
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "error" => $e->getMessage()]);
}
?>