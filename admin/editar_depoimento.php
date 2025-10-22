<?php
// 1. LOGIC MOVED TO TOP
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se está logado
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}

// Verifica se o ID do depoimento foi passado na URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) { // Valida se é um inteiro
    header("Location: gerenciar_depoimentos.php");
    exit();
}
$id_depoimento = (int)$_GET['id'];

// --- LÓGICA PARA ATUALIZAR OS DADOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_cliente = $_POST['nome_cliente'];
    $texto = $_POST['texto']; // <-- CORRIGIDO AQUI (nome do campo do formulário)
    $foto_url = $_POST['foto_url']; // Assume que estamos editando apenas a URL por enquanto

    // Prepara a query de atualização
    // <-- CORRIGIDO AQUI (nome da coluna texto)
    $sql = "UPDATE depoimentos SET nome_cliente = ?, texto = ?, foto_url = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    // <-- CORRIGIDO AQUI (variável $texto)
    $stmt->bind_param("sssi", $nome_cliente, $texto, $foto_url, $id_depoimento);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Depoimento atualizado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar depoimento: " . $stmt->error;
        error_log("Erro SQL ao atualizar depoimento ID {$id_depoimento}: " . $stmt->error); // Log de erro
    }
    $stmt->close(); // Fecha o statement

    // Redireciona de volta para a lista principal
    header("Location: gerenciar_depoimentos.php");
    exit();
}

// 2. NOW INCLUDE HEADER
include 'admin_header.php';

// --- BUSCA OS DADOS ATUAIS DO DEPOIMENTO PARA PREENCHER O FORMULÁRIO ---
// <-- CORRIGIDO AQUI (seleciona a coluna 'texto')
$stmt_busca = $conexao->prepare("SELECT id, nome_cliente, texto, foto_url FROM depoimentos WHERE id = ?");
$stmt_busca->bind_param("i", $id_depoimento);
$stmt_busca->execute();
$result_busca = $stmt_busca->get_result();
$depoimento = $result_busca->fetch_assoc();
$stmt_busca->close(); // Fecha o statement

// Se não encontrar nenhum depoimento com o ID, exibe mensagem e sai (mas já dentro do HTML)
if (!$depoimento) {
    echo '<div class="feedback-message feedback-error">Depoimento não encontrado.</div>';
    include 'admin_footer.php';
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
             
            <label for="texto">Texto do Depoimento</label>
             
            <textarea id="texto" name="texto" rows="6" required><?php echo htmlspecialchars($depoimento['texto']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="foto_url">URL da Foto (Opcional)</label>
            <input type="text" id="foto_url" name="foto_url" value="<?php echo htmlspecialchars($depoimento['foto_url']); ?>" placeholder="Ex: uploads/nome_arquivo.jpg">
            <small>Lembre-se de usar o caminho relativo à raiz do site (ex: uploads/foto.jpg).</small>
             
        </div>
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>