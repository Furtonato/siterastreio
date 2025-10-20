<?php
require_once 'db.php';
include 'admin_header.php';

// Verifica se o ID do depoimento foi passado na URL
if (!isset($_GET['id'])) {
    header("Location: gerenciar_depoimentos.php");
    exit();
}
$id_depoimento = $_GET['id'];

// --- LÓGICA PARA ATUALIZAR OS DADOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_cliente = $_POST['nome_cliente'];
    $texto_depoimento = $_POST['texto_depoimento'];
    $foto_url = $_POST['foto_url'];

    // Prepara a query de atualização
    $sql = "UPDATE depoimentos SET nome_cliente = ?, texto_depoimento = ?, foto_url = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssi", $nome_cliente, $texto_depoimento, $foto_url, $id_depoimento);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Depoimento atualizado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar depoimento.";
    }
    
    // Redireciona de volta para a lista principal
    header("Location: gerenciar_depoimentos.php");
    exit();
}

// --- BUSCA OS DADOS ATUAIS DO DEPOIMENTO PARA PREENCHER O FORMULÁRIO ---
$stmt_busca = $conexao->prepare("SELECT * FROM depoimentos WHERE id = ?");
$stmt_busca->bind_param("i", $id_depoimento);
$stmt_busca->execute();
$depoimento = $stmt_busca->get_result()->fetch_assoc();

// Se não encontrar nenhum depoimento com o ID, redireciona
if (!$depoimento) {
    header("Location: gerenciar_depoimentos.php");
    exit();
}
?>

<head>
    <title>Editar Depoimento</title>
</head>

<div class="page-header">
    <h1>Editar Depoimento de: <?php echo htmlspecialchars($depoimento['nome_cliente']); ?></h1>
    <a href="gerenciar_depoimentos.php" class="btn btn-primary">Cancelar</a>
</div>

<div class="card">
    <form action="editar_depoimento.php?id=<?php echo $id_depoimento; ?>" method="POST">
        <div class="form-group">
            <label for="nome_cliente">Nome do Cliente</label>
            <input type="text" id="nome_cliente" name="nome_cliente" value="<?php echo htmlspecialchars($depoimento['nome_cliente']); ?>" required>
        </div>
        <div class="form-group">
            <label for="texto_depoimento">Texto do Depoimento</label>
            <textarea id="texto_depoimento" name="texto_depoimento" rows="6" required><?php echo htmlspecialchars($depoimento['texto_depoimento']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="foto_url">URL da Foto (Opcional)</label>
            <input type="text" id="foto_url" name="foto_url" value="<?php echo htmlspecialchars($depoimento['foto_url']); ?>" placeholder="Ex: imagens/cliente1.jpg">
            <small>Lembre-se de usar o caminho relativo da imagem (ex: imagens/foto.jpg).</small>
        </div>
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>