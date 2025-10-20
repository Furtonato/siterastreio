<?php
require_once 'db.php';
include 'admin_header.php';

if (!isset($_GET['id'])) {
    header("Location: gerenciar_clientes.php");
    exit();
}
$id_cliente = $_GET['id'];

// --- LÓGICA PARA ADICIONAR NOVO VEÍCULO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_veiculo'])) {
    $modelo = $_POST['modelo'];
    $placa = $_POST['placa'];
    $icone_url = $_POST['icone_url'];

    $sql_veiculo = "INSERT INTO veiculos (id_usuario, modelo, placa, icone_url) VALUES (?, ?, ?, ?)";
    $stmt_veiculo = $conexao->prepare($sql_veiculo);
    $stmt_veiculo->bind_param("isss", $id_cliente, $modelo, $placa, $icone_url);
    
    if ($stmt_veiculo->execute()) {
        $id_novo_veiculo = $stmt_veiculo->insert_id;
        $conexao->query("INSERT INTO rastreamentos (id_veiculo, status) VALUES ($id_novo_veiculo, 'AGUARDANDO LIBERAÇÃO')");
        $_SESSION['success_message'] = "Veículo adicionado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao adicionar veículo.";
    }
    header("Location: gerenciar_rastreamento.php?id=" . $id_cliente);
    exit();
}

// --- BUSCA DADOS PARA EXIBIÇÃO ---
$stmt_cliente = $conexao->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmt_cliente->bind_param("i", $id_cliente);
$stmt_cliente->execute();
$cliente = $stmt_cliente->get_result()->fetch_assoc();

$sql_veiculos = "SELECT v.id, v.modelo, v.placa, r.status FROM veiculos v LEFT JOIN rastreamentos r ON v.id = r.id_veiculo WHERE v.id_usuario = ?";
$stmt_veiculos = $conexao->prepare($sql_veiculos);
$stmt_veiculos->bind_param("i", $id_cliente);
$stmt_veiculos->execute();
$veiculos = $stmt_veiculos->get_result();
?>

<head>
    <title>Detalhes do Cliente: <?php echo htmlspecialchars($cliente['nome']); ?></title>
</head>

<div class="page-header">
    <h1>Cliente: <?php echo htmlspecialchars($cliente['nome']); ?></h1>
    <a href="gerenciar_clientes.php" class="btn btn-primary">Voltar para a Lista</a>
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
    <h2>Veículos Cadastrados</h2>
    <table class="content-table">
        <thead>
            <tr>
                <th>Modelo</th>
                <th>Placa</th>
                <th>Status Atual</th>
                <th width="320px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($veiculos->num_rows > 0): ?>
                <?php while ($veiculo = $veiculos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($veiculo['modelo']); ?></td>
                        <td><?php echo htmlspecialchars($veiculo['placa']); ?></td>
                        <td><?php echo htmlspecialchars($veiculo['status']); ?></td>
                        <td class="actions-cell">
                            <a href="editar_rastreamento.php?id_veiculo=<?php echo $veiculo['id']; ?>" class="btn btn-primary">Editar Rastreio</a>
                            <a href="editar_veiculo.php?id_veiculo=<?php echo $veiculo['id']; ?>" class="btn btn-warning">Editar Veículo</a>
                            <a href="excluir_veiculo.php?id_veiculo=<?php echo $veiculo['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este veículo? Esta ação é irreversível.');">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Nenhum veículo cadastrado para este cliente.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>Adicionar Novo Veículo</h2>
    <form action="gerenciar_rastreamento.php?id=<?php echo $id_cliente; ?>" method="POST">
        <div class="form-group">
            <label for="modelo">Modelo do Veículo</label>
            <input type="text" id="modelo" name="modelo" required>
        </div>
        <div class="form-group">
            <label for="placa">Placa</label>
            <input type="text" id="placa" name="placa">
        </div>
        <div class="form-group">
            <label for="icone_url">Logo da Marca</label>
            <select id="icone_url" name="icone_url" class="form-group" style="width:100%; padding:12px; border: 1px solid #ccc; border-radius: 5px; font-size: 1em;">
                <option value="">Selecione um logo</option>
                <option value="imagens/logos/chevrolet.png">Chevrolet</option>
                <option value="imagens/logos/citroen.png">Citroën</option>
                <option value="imagens/logos/hyundai.png">Hyundai</option>
                <option value="imagens/logos/mercedes.png">Mercedes-Benz</option>
                <option value="imagens/logos/mitsubishi.png">Mitsubishi</option>
                <option value="imagens/logos/renault.png">Renault</option>
                <option value="imagens/logos/toyota.png">Toyota</option>
                <option value="imagens/logos/vw.png">Volkswagen</option>
                <option value="imagens/logos/ford.png">Ford</option>
                <option value="imagens/logos/honda.png">Honda</option>
            </select>
        </div>
        <button type="submit" name="add_veiculo" class="btn btn-success">Adicionar Veículo</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>