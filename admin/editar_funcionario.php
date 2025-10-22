<?php
// 1. LOGIC MOVED TO TOP
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o ID do funcionário foi passado na URL
if (!isset($_GET['id'])) {
    header("Location: gerenciar_funcionarios.php");
    exit();
}
$id_funcionario = $_GET['id'];

// --- LÓGICA PARA ATUALIZAR OS DADOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $foto_url = $_POST['foto_url'];

    // Prepara a query de atualização
    $sql = "UPDATE funcionarios SET nome = ?, telefone = ?, foto_url = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssi", $nome, $telefone, $foto_url, $id_funcionario);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Funcionário atualizado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar funcionário.";
    }
    
    // Redireciona de volta para a lista principal
    header("Location: gerenciar_funcionarios.php");
    exit();
}

// 2. NOW INCLUDE HEADER
include 'admin_header.php';

// --- BUSCA OS DADOS ATUAIS DO FUNCIONÁRIO PARA PREENCHER O FORMULÁRIO ---
$stmt_busca = $conexao->prepare("SELECT * FROM funcionarios WHERE id = ?");
$stmt_busca->bind_param("i", $id_funcionario);
$stmt_busca->execute();
$funcionario = $stmt_busca->get_result()->fetch_assoc();

// Se não encontrar nenhum funcionário com o ID, redireciona
if (!$funcionario) {
    header("Location: gerenciar_funcionarios.php");
    exit();
}
?>

<head>
    <title>Editar Funcionário: <?php echo htmlspecialchars($funcionario['nome']); ?></title>
</head>

<div class="page-header">
    <h1>Editar Funcionário: <?php echo htmlspecialchars($funcionario['nome']); ?></h1>
    <a href="gerenciar_funcionarios.php" class="btn btn-primary">Cancelar</a>
</div>

<div class="card">
    <form action="editar_funcionario.php?id=<?php echo $id_funcionario; ?>" method="POST">
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($funcionario['nome']); ?>" required>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($funcionario['telefone']); ?>" placeholder="(11) 98765-4321">
        </div>
        <div class="form-group">
            <label for="foto_url">URL da Foto</label>
            <input type="text" id="foto_url" name="foto_url" value="<?php echo htmlspecialchars($funcionario['foto_url']); ?>" placeholder="imagens/nome-do-arquivo.jpg">
            <small>Lembre-se de usar o caminho relativo da imagem (ex: imagens/foto.jpg).</small>
        </div>
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>