<?php
// 1. LOGIC MOVED TO TOP
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- LÓGICA PARA ATUALIZAR OS DADOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $endereco = $_POST['endereco_comercial'];
    $habilitacao = $_POST['habilitacao'];
    $admissao = $_POST['data_admissao'];
    $status = $_POST['status'];
    $foto_path = $_POST['foto_atual']; // Pega o caminho da foto atual

    // Lógica para Upload de nova foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_dir = '../imagens/'; // Salva na pasta de imagens principal
        $file_name = uniqid() . '-' . basename($_FILES['foto']['name']);
        $destination = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
            $foto_path = 'imagens/' . $file_name; // Atualiza o caminho da foto para o novo
        } else {
            $_SESSION['error_message'] = "Erro ao fazer upload da nova foto.";
        }
    }

    // Como só tem um motorista, atualizamos o registro com ID = 1
    $sql = "UPDATE motoristas SET nome=?, endereco_comercial=?, habilitacao=?, data_admissao=?, status=?, foto_url=? WHERE id = 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssssss", $nome, $endereco, $habilitacao, $admissao, $status, $foto_path);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Dados do motorista atualizados com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar os dados.";
    }
    
    header("Location: gerenciar_motorista.php");
    exit();
}

// 2. NOW INCLUDE HEADER
include 'admin_header.php';

// --- LÓGICA PARA BUSCAR DADOS E EXIBIR NO FORMULÁRIO ---
$resultado = $conexao->query("SELECT * FROM motoristas WHERE id = 1 LIMIT 1");
$motorista = $resultado->fetch_assoc();
?>

<head>
    <title>Gerenciar Motorista</title>
</head>

<div class="page-header">
    <h1>Gerenciar Dados do Motorista</h1>
</div>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="feedback-message feedback-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="feedback-message feedback-error">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}
?>

<div class="card">
    <form action="gerenciar_motorista.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="foto_atual" value="<?php echo htmlspecialchars($motorista['foto_url']); ?>">
        
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($motorista['nome']); ?>" required>
        </div>
        <div class="form-group">
            <label for="endereco_comercial">Endereço Comercial</label>
            <input type="text" id="endereco_comercial" name="endereco_comercial" value="<?php echo htmlspecialchars($motorista['endereco_comercial']); ?>">
        </div>
        <div class="form-group">
            <label for="habilitacao">Habilitação</label>
            <input type="text" id="habilitacao" name="habilitacao" value="<?php echo htmlspecialchars($motorista['habilitacao']); ?>">
        </div>
        <div class="form-group">
            <label for="data_admissao">Data de Admissão</label>
            <input type="date" id="data_admissao" name="data_admissao" value="<?php echo htmlspecialchars($motorista['data_admissao']); ?>" style="padding: 11px;">
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <input type="text" id="status" name="status" value="<?php echo htmlspecialchars($motorista['status']); ?>">
        </div>
        <div class="form-group">
            <label>Foto Atual</label>
            <div>
                <img src="../<?php echo htmlspecialchars($motorista['foto_url']); ?>" alt="Foto do Motorista" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ccc;">
            </div>
        </div>
        <div class="form-group">
            <label for="foto">Alterar Foto (opcional)</label>
            <input type="file" id="foto" name="foto">
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>