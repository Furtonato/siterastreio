<?php
// O session_start() precisa vir antes de qualquer lógica de sessão
session_start();
require_once 'db.php';

// --- LÓGICA DE ADICIONAR/EXCLUIR MOVIDA PARA O TOPO ---

// SE O FORMULÁRIO DE ADICIONAR FOR ENVIADO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_depoimento'])) {
    $nome_cliente = $_POST['nome_cliente'];
    $texto_depoimento = $_POST['texto_depoimento'];
    $foto_url = ''; // Inicia a variável da foto

    // --- LÓGICA DE UPLOAD DE IMAGEM ---
    if (isset($_FILES['foto_arquivo']) && $_FILES['foto_arquivo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_arquivo'];
        $upload_dir = '../uploads/';
        $file_name = uniqid() . '-' . basename($file['name']);
        $target_path = $upload_dir . $file_name;
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($file['type'], $allowed_types)) {
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $foto_url = 'uploads/' . $file_name;
            } else {
                $_SESSION['error_message'] = "Erro ao mover o arquivo enviado.";
            }
        } else {
            $_SESSION['error_message'] = "Erro: Tipo de arquivo não permitido.";
        }
    }

    if (!isset($_SESSION['error_message'])) {
        $sql = "INSERT INTO depoimentos (nome_cliente, texto_depoimento, foto_url) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sss", $nome_cliente, $texto_depoimento, $foto_url);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Depoimento adicionado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao adicionar depoimento.";
        }
    }
    header("Location: gerenciar_depoimentos.php");
    exit();
}

// SE UMA AÇÃO DE EXCLUSÃO FOR SOLICITADA
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_para_excluir = $_GET['id'];
    
    // Antes de excluir do banco, apaga o arquivo de imagem do servidor
    $stmt_busca = $conexao->prepare("SELECT foto_url FROM depoimentos WHERE id = ?");
    $stmt_busca->bind_param("i", $id_para_excluir);
    $stmt_busca->execute();
    $result = $stmt_busca->get_result()->fetch_assoc();
    if ($result && !empty($result['foto_url']) && file_exists('../' . $result['foto_url'])) {
        unlink('../' . $result['foto_url']);
    }

    $sql = "DELETE FROM depoimentos WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_para_excluir);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Depoimento excluído com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao excluir depoimento.";
    }
    header("Location: gerenciar_depoimentos.php");
    exit();
}

// Inclui o cabeçalho APÓS a lógica de processamento
include 'admin_header.php';

// Busca todos os depoimentos para listar na tabela
$depoimentos = $conexao->query("SELECT id, nome_cliente, texto_depoimento, foto_url FROM depoimentos ORDER BY id DESC");
?>

<head>
    <title>Gerenciar Depoimentos</title>
</head>

<div class="page-header">
    <h1>Gerenciar Depoimentos</h1>
</div>

<?php
// Exibe mensagens de sucesso ou erro
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
    <h2>Adicionar Novo Depoimento</h2>
    <form action="gerenciar_depoimentos.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome_cliente">Nome do Cliente</label>
            <input type="text" id="nome_cliente" name="nome_cliente" required>
        </div>
        <div class="form-group">
            <label for="texto_depoimento">Texto do Depoimento</label>
            <textarea id="texto_depoimento" name="texto_depoimento" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="foto_arquivo">Foto do Cliente (Opcional)</label>
            <input type="file" id="foto_arquivo" name="foto_arquivo">
        </div>
        <button type="submit" name="add_depoimento" class="btn btn-success">Cadastrar Depoimento</button>
    </form>
</div>

<div class="card">
    <h2>Depoimentos Cadastrados</h2>
    <table class="content-table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nome do Cliente</th>
                <th>Início do Depoimento</th>
                <th width="180px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($depoimentos && $depoimentos->num_rows > 0): ?>
                <?php while ($dep = $depoimentos->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($dep['foto_url'])): ?>
                                <img src="../<?php echo htmlspecialchars($dep['foto_url']); ?>" alt="Foto" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($dep['nome_cliente']); ?></td>
                        <td><?php echo htmlspecialchars(substr($dep['texto_depoimento'], 0, 80)); ?>...</td>
                        <td class="actions-cell">
                            <a href="editar_depoimento.php?id=<?php echo $dep['id']; ?>" class="btn btn-warning">Editar</a>
                            <a href="gerenciar_depoimentos.php?action=delete&id=<?php echo $dep['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este depoimento?');">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhum depoimento cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>