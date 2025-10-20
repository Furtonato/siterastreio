<?php
session_start();
require_once 'admin/db.php';

// Proteção
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Não autorizado']);
    exit();
}

$id_usuario_logado = $_SESSION['user_id'];

// Busca as últimas coordenadas salvas para o veículo do usuário logado
$sql = "
    SELECT r.latitude, r.longitude
    FROM rastreamentos r
    JOIN veiculos v ON r.id_veiculo = v.id
    WHERE v.id_usuario = ?
    ORDER BY r.ultima_atualizacao DESC
    LIMIT 1";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario_logado);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Retorna os dados em formato JSON
header('Content-Type: application/json');

if ($result && !empty($result['latitude'])) {
    echo json_encode($result);
} else {
    echo json_encode(['latitude' => null, 'longitude' => null]);
}
?>