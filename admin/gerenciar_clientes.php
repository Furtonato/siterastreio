<?php
require_once 'db.php';
include 'admin_header.php';

$id_admin_logado = $_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_cliente'])) {
    $nome = $_POST['nome'];
    $cpf_input = $_POST['cpf'];
    $cpf = preg_replace('/[^0-9]/', '', $cpf_input);
    $endereco = $_POST['endereco'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $telefone = $_POST['telefone'];

    $sql = "INSERT INTO usuarios (nome, cpf, endereco, senha, id_admin, telefone) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssssis", $nome, $cpf, $endereco, $senha, $id_admin_logado, $telefone);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cliente adicionado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao adicionar cliente.";
    }
    header("Location: gerenciar_clientes.php");
    exit();
}

$sql_busca = "SELECT id, nome, cpf, endereco FROM usuarios WHERE id_admin = ?";
$stmt_busca = $conexao->prepare($sql_busca);
$stmt_busca->bind_param("i", $id_admin_logado);
$stmt_busca->execute();
$clientes = $stmt_busca->get_result();
?>

<head>
    <title>Gerenciar Clientes</title>
</head>

<div class="page-header">
    <h1>Gerenciar Clientes</h1>
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
    <h2>Adicionar Novo Cliente</h2>
    <form action="gerenciar_clientes.php" method="POST">
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        <div class="form-group">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" required>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone (Opcional)</label>
            <input type="text" id="telefone" name="telefone" placeholder="(11) 98765-4321">
        </div>
        <div class="form-group">
            <label for="endereco">Endereço Completo (para Rota)</label>
            <input type="text" id="endereco" name="endereco" placeholder="Ex: Rua, Número, Bairro, Cidade - Estado">
        </div>
        <div class="form-group">
            <label for="senha">Senha Provisória</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <button type="submit" name="add_cliente" class="btn btn-success">Cadastrar Cliente</button>
    </form>
</div>

<div class="card">
    <h2>Clientes Cadastrados</h2>
    <table class="content-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Endereço</th>
                <th width="220px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($cliente = $clientes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $cliente['id']; ?></td>
                    <td><?php echo htmlspecialchars($cliente['nome']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['cpf']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['endereco']); ?></td>
                    <td class="actions-cell">
                        <a href="gerenciar_rastreamento.php?id=<?php echo $cliente['id']; ?>" class="btn btn-primary">Rastreamento</a>
                        <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-warning">Editar</a>
                        <a href="excluir_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este cliente?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>