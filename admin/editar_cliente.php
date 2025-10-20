<?php
require_once 'db.php';
include 'admin_header.php';

if (!isset($_GET['id'])) {
    header("Location: gerenciar_clientes.php");
    exit();
}
$id_cliente = $_GET['id'];
$id_admin_logado = $_SESSION['admin_id'];

$sql_check = "SELECT id FROM usuarios WHERE id = ? AND id_admin = ?";
$stmt_check = $conexao->prepare($sql_check);
$stmt_check->bind_param("ii", $id_cliente, $id_admin_logado);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows === 0) {
    $_SESSION['error_message'] = "Acesso negado.";
    header("Location: gerenciar_clientes.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cpf_input = $_POST['cpf'];
    $cpf = preg_replace('/[^0-9]/', '', $cpf_input);
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if (!empty($nova_senha)) {
        if ($nova_senha !== $confirmar_senha) {
            $_SESSION['error_message'] = "Erro: As senhas não coincidem.";
            header("Location: editar_cliente.php?id=" . $id_cliente);
            exit();
        }
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        $sql = "UPDATE usuarios SET nome = ?, cpf = ?, endereco = ?, telefone = ?, senha = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $cpf, $endereco, $telefone, $senha_hash, $id_cliente);
    } else {
        $sql = "UPDATE usuarios SET nome = ?, cpf = ?, endereco = ?, telefone = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $cpf, $endereco, $telefone, $id_cliente);
    }
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cliente atualizado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar cliente.";
    }
    header("Location: gerenciar_clientes.php");
    exit();
}

$sql_busca = "SELECT nome, cpf, endereco, telefone FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql_busca);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
?>

<head>
    <title>Editar Cliente: <?php echo htmlspecialchars($cliente['nome']); ?></title>
</head>

<div class="page-header">
    <h1>Editar Cliente: <?php echo htmlspecialchars($cliente['nome']); ?></h1>
    <a href="gerenciar_clientes.php" class="btn btn-primary">Cancelar</a>
</div>

<div class="card">
    <form action="editar_cliente.php?id=<?php echo $id_cliente; ?>" method="POST">
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
        </div>
        <div class="form-group">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($cliente['cpf']); ?>" required>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone (Opcional)</label>
            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="endereco">Endereço Completo</label>
            <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($cliente['endereco'] ?? ''); ?>">
        </div>

        <hr>
        <p style="margin: 15px 0 10px 0;">
            <strong>Redefinir Senha (opcional)</strong><br>
            <small>Preencha os campos abaixo apenas se desejar alterar a senha do cliente.</small>
        </p>
        <div class="form-group">
            <label for="nova_senha">Nova Senha</label>
            <input type="password" id="nova_senha" name="nova_senha" placeholder="Deixe em branco para não alterar">
        </div>
        <div class="form-group">
            <label for="confirmar_senha">Confirmar Nova Senha</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Deixe em branco para não alterar">
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>