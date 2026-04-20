<?php
header("Content-Type: application/json");

$host = 'mysql';
$user = 'meu_usuario';
$pass = 'minha_senha';
$db   = 'meu_banco';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["error" => "Falha na conexão"]));
}

$conn->query("CREATE TABLE IF NOT EXISTS usuarios (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(255) NOT NULL)");

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$scriptName = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$apiPath = array_slice($requestUri, count($scriptName));

// --- ROTAS ---

// LISTAR
if ($method == 'GET' && count($apiPath) == 1 && $apiPath[0] == 'users') {
    $res = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));

// CRIAR
} elseif ($method == 'POST' && count($apiPath) == 1 && $apiPath[0] == 'users') {
    $input = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("INSERT INTO usuarios (nome) VALUES (?)");
    $stmt->bind_param("s", $input['nome']);
    $stmt->execute();
    echo json_encode(["id" => $conn->insert_id]);

// EDITAR (ATUALIZAR)
} elseif ($method == 'PUT' && count($apiPath) == 2 && $apiPath[0] == 'users') {
    $id = intval($apiPath[1]);
    $input = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("UPDATE usuarios SET nome = ? WHERE id = ?");
    $stmt->bind_param("si", $input['nome'], $id);
    $stmt->execute();
    echo json_encode(["success" => true]);

// EXCLUIR
} elseif ($method == 'DELETE' && count($apiPath) == 2 && $apiPath[0] == 'users') {
    $id = intval($apiPath[1]);
    $conn->query("DELETE FROM usuarios WHERE id = $id");
    echo json_encode(["success" => true]);
}

$conn->close();