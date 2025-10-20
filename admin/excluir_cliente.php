<?php
session_start();
require_once 'db.php';

// Proteção
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

// Lógica para excluir o cliente. ATENÇÃO: Isso é irreversível.
// Futuramente, seria bom excluir também os veículos e rastreamentos associados a ele.
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Cliente excluído com sucesso!";
} else {
    $_SESSION['error_message'] = "Erro ao excluir cliente.";
}

header("Location: gerenciar_clientes.php");
exit();
?>