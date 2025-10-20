<?php
session_start();
require_once 'db.php';

// Proteção da página
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in'] || !isset($_GET['id_veiculo'])) {
    header('Location: index.php');
    exit();
}

$id_veiculo = (int)$_GET['id_veiculo'];
$id_cliente = null;

// Antes de excluir, precisamos saber para qual cliente voltar
$stmt_user = $conexao->prepare("SELECT id_usuario FROM veiculos WHERE id = ?");
$stmt_user->bind_param("i", $id_veiculo);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows > 0) {
    $id_cliente = $result_user->fetch_assoc()['id_usuario'];
}
$stmt_user->close();

if (!$id_cliente) {
    $_SESSION['error_message'] = "Veículo não encontrado para exclusão.";
    header("Location: gerenciar_clientes.php");
    exit();
}

// O banco de dados está configurado com ON DELETE CASCADE,
// então só precisamos excluir o veículo. O rastreamento associado será apagado automaticamente.
$stmt_veiculo = $conexao->prepare("DELETE FROM veiculos WHERE id = ?");
$stmt_veiculo->bind_param("i", $id_veiculo);

if ($stmt_veiculo->execute()) {
    $_SESSION['success_message'] = "Veículo excluído com sucesso!";
} else {
    $_SESSION['error_message'] = "Erro ao excluir o veículo.";
}
$stmt_veiculo->close();

// Redireciona de volta para a página de detalhes do cliente
header("Location: gerenciar_rastreamento.php?id=" . $id_cliente);
exit();
?>