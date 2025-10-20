<?php
// O session_start() precisa vir antes de qualquer lógica de sessão

require_once 'db.php';

// --- LÓGICA PARA ADICIONAR OU EXCLUIR FOI MOVIDA PARA O TOPO ---

// SE O FORMULÁRIO DE ADICIONAR FOR ENVIADO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_funcionario'])) {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $foto_url = ''; // Inicia a variável da foto como vazia

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
        $sql = "INSERT INTO funcionarios (nome, telefone, foto_url) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sss", $nome, $telefone, $foto_url);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Funcionário adicionado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao adicionar funcionário.";
        }
    }
    
    // Este header() agora será executado ANTES de qualquer HTML
    header("Location: gerenciar_funcionarios.php");
    exit();
}

// SE UMA AÇÃO DE EXCLUSÃO FOR SOLICITADA
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_para_excluir = $_GET['id'];

    $stmt_busca = $conexao->prepare("SELECT foto_url FROM funcionarios WHERE id = ?");
    $stmt_busca->bind_param("i", $id_para_excluir);
    $stmt_busca->execute();
    $result = $stmt_busca->get_result()->fetch_assoc();
    if ($result && !empty($result['foto_url']) && file_exists('../' . $result['foto_url'])) {
        unlink('../' . $result['foto_url']);
    }

    $sql = "DELETE FROM funcionarios WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_para_excluir);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Funcionário excluído com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao excluir funcionário.";
    }
    
    // Este header() agora também será executado ANTES de qualquer HTML
    header("Location: gerenciar_funcionarios.php");
    exit();
}

// --- FIM DA LÓGICA MOVIDA ---


// AGORA QUE TODA A LÓGICA DE REDIRECIONAMENTO JÁ PASSOU, PODEMOS INCLUIR O HTML
include 'admin_header.php';

// Busca todos os funcionários para listar na tabela
$funcionarios = $conexao->query("SELECT id, nome, telefone, foto_url FROM funcionarios ORDER BY nome ASC");
?>

<head>
    <title>Gerenciar Funcionários</title>
</head>

<div class="page-header">
    <h1>Gerenciar Funcionários</h1>
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
    <h2>Adicionar Novo Funcionário</h2>
    <form action="gerenciar_funcionarios.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" placeholder="(11) 98765-4321">
        </div>
        <div class="form-group">
            <label for="foto_arquivo">Foto do Funcionário</label>
            <input type="file" id="foto_arquivo" name="foto_arquivo">
        </div>
        <button type="submit" name="add_funcionario" class="btn btn-success">Cadastrar Funcionário</button>
    </form>
</div>

<div class="card">
    <h2>Funcionários Cadastrados</h2>
    <table class="content-table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nome</th>
                <th>Telefone</th>
                <th width="180px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($funcionarios->num_rows > 0): ?>
                <?php while ($func = $funcionarios->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($func['foto_url'])): ?>
                                <img src="../<?php echo htmlspecialchars($func['foto_url']); ?>" alt="Foto" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($func['nome']); ?></td>
                        <td><?php echo htmlspecialchars($func['telefone']); ?></td>
                        <td class="actions-cell">
                            <a href="editar_funcionario.php?id=<?php echo $func['id']; ?>" class="btn btn-warning">Editar</a>
                            <a href="gerenciar_funcionarios.php?action=delete&id=<?php echo $func['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este funcionário?');">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhum funcionário cadastrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>