<?php
// O session_start() precisa vir antes de qualquer lógica de sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// Verifica se está logado
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}

// --- LÓGICA DE ADICIONAR/EXCLUIR MOVIDA PARA O TOPO ---

// SE O FORMULÁRIO DE ADICIONAR FOR ENVIADO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_depoimento'])) {
    $nome_cliente = $_POST['nome_cliente'];
    $texto = $_POST['texto']; // <-- CORRIGIDO AQUI (nome do campo do formulário)
    $foto_url = ''; // Inicia a variável da foto

    // --- LÓGICA DE UPLOAD DE IMAGEM ---
    if (isset($_FILES['foto_arquivo']) && $_FILES['foto_arquivo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_arquivo'];
        // Define o diretório de upload relativo à pasta 'admin'
        $upload_dir = '../uploads/';
        // Cria o diretório se não existir
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = uniqid('depoimento_') . '-' . basename($file['name']); // Nome único
        $target_path = $upload_dir . $file_name;
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($file['type'], $allowed_types)) {
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // Salva o caminho relativo à raiz do site
                $foto_url = 'uploads/' . $file_name;
            } else {
                $_SESSION['error_message'] = "Erro ao mover o arquivo enviado. Verifique permissões.";
                 error_log("Erro ao mover upload: de " . $file['tmp_name'] . " para " . $target_path); // Log de erro
            }
        } else {
            $_SESSION['error_message'] = "Erro: Tipo de arquivo não permitido (" . htmlspecialchars($file['type']) . "). Permitidos: JPEG, PNG, GIF, WebP.";
        }
    }

    if (!isset($_SESSION['error_message'])) {
        // <-- CORRIGIDO AQUI (nome da coluna e variável)
        $sql = "INSERT INTO depoimentos (nome_cliente, texto, foto_url) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        // <-- CORRIGIDO AQUI (variável $texto)
        $stmt->bind_param("sss", $nome_cliente, $texto, $foto_url);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Depoimento adicionado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao adicionar depoimento: " . $stmt->error;
            error_log("Erro SQL ao adicionar depoimento: " . $stmt->error); // Log de erro
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
    // Usa caminho relativo à pasta 'admin' para verificar e excluir
    if ($result && !empty($result['foto_url']) && file_exists('../' . $result['foto_url'])) {
        if (!unlink('../' . $result['foto_url'])) {
             error_log("Erro ao excluir arquivo de imagem: ../" . $result['foto_url']); // Log de erro
             $_SESSION['warning_message'] = "Depoimento excluído do banco, mas houve um erro ao remover o arquivo de imagem."; // Aviso opcional
        }
    }
    $stmt_busca->close();

    $sql = "DELETE FROM depoimentos WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_para_excluir);

    if ($stmt->execute()) {
        // Só define a mensagem de sucesso se não houve aviso sobre arquivo
        if (!isset($_SESSION['warning_message'])) {
            $_SESSION['success_message'] = "Depoimento excluído com sucesso!";
        }
    } else {
        $_SESSION['error_message'] = "Erro ao excluir depoimento: " . $stmt->error;
        error_log("Erro SQL ao excluir depoimento: " . $stmt->error); // Log de erro
    }
    header("Location: gerenciar_depoimentos.php");
    exit();
}

// Inclui o cabeçalho APÓS a lógica de processamento
include 'admin_header.php';

// Busca todos os depoimentos para listar na tabela
// <-- CORRIGIDO AQUI (nome da coluna texto)
$depoimentos_result = $conexao->query("SELECT id, nome_cliente, texto, foto_url FROM depoimentos ORDER BY id DESC");
if (!$depoimentos_result) {
     echo '<div class="feedback-message feedback-error">Erro ao buscar depoimentos: ' . $conexao->error . '</div>';
     error_log("Erro SQL ao buscar depoimentos: " . $conexao->error); // Log de erro
     $depoimentos = []; // Define como array vazio para evitar erros abaixo
} else {
    $depoimentos = $depoimentos_result; // Atribui o resultado se a query funcionou
}

?>

<head>
    <title>Gerenciar Depoimentos</title>
</head>

<div class="page-header">
    <h1>Gerenciar Depoimentos</h1>
</div>

<?php
// Exibe mensagens de sucesso, erro ou aviso
if (isset($_SESSION['success_message'])) {
    echo '<div class="feedback-message feedback-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="feedback-message feedback-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['warning_message'])) {
    echo '<div class="feedback-message feedback-warning">' . htmlspecialchars($_SESSION['warning_message']) . '</div>'; // Adiciona estilo para warning se tiver
    unset($_SESSION['warning_message']);
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
            
            <label for="texto">Texto do Depoimento</label>
            <textarea id="texto" name="texto" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="foto_arquivo">Foto do Cliente (Opcional)</label>
            <input type="file" id="foto_arquivo" name="foto_arquivo" accept="image/jpeg, image/png, image/gif, image/webp"> 
            <small>Formatos permitidos: JPG, PNG, GIF, WebP.</small>
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
                            <?php else: ?>
                                <span style="color: #ccc;">Sem foto</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($dep['nome_cliente']); ?></td>
                        <td>
                            <?php
                                // <-- CORRIGIDO AQUI (nome da coluna texto)
                                $texto_completo = $dep['texto'] ?? ''; // Garante que não é null
                                echo htmlspecialchars(mb_substr($texto_completo, 0, 80)); // Usa mb_substr para UTF-8
                                if (mb_strlen($texto_completo) > 80) {
                                    echo '...';
                                }
                            ?>
                        </td>
                        <td class="actions-cell">
                            <a href="editar_depoimento.php?id=<?php echo $dep['id']; ?>" class="btn btn-warning">Editar</a>
                            <a href="gerenciar_depoimentos.php?action=delete&id=<?php echo $dep['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este depoimento? Arquivos de imagem associados também serão removidos.');">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhum depoimento cadastrado.</td></tr>
            <?php endif; ?>
            <?php if ($depoimentos instanceof mysqli_result) $depoimentos->free(); // Libera memória ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>