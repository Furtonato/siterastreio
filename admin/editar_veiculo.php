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

if (!isset($_GET['id_veiculo'])) {
    header("Location: gerenciar_clientes.php");
    exit();
}
$id_veiculo = $_GET['id_veiculo'];

// --- LÓGICA DE ATUALIZAÇÃO ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $modelo = $_POST['modelo'];
    $placa = $_POST['placa'];
    $icone_url = $_POST['icone_url'];
    $id_usuario = $_POST['id_usuario']; // Pega o ID do usuário para o redirecionamento

    $sql = "UPDATE veiculos SET modelo = ?, placa = ?, icone_url = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("sssi", $modelo, $placa, $icone_url, $id_veiculo);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Veículo atualizado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar veículo.";
    }

    // Redireciona de volta para a página de detalhes do cliente
    header("Location: gerenciar_rastreamento.php?id=" . $id_usuario);
    exit();
}

// 2. NOW INCLUDE HEADER
include 'admin_header.php';

// --- BUSCA DE DADOS PARA PREENCHER O FORMULÁRIO ---
$stmt_busca = $conexao->prepare("SELECT * FROM veiculos WHERE id = ?");
$stmt_busca->bind_param("i", $id_veiculo);
$stmt_busca->execute();
$veiculo = $stmt_busca->get_result()->fetch_assoc();

// Verifica se o veículo foi encontrado
if (!$veiculo) {
     echo '<div class="feedback-message feedback-error">Veículo não encontrado.</div>';
     include 'admin_footer.php';
     exit();
}
?>

<head>
    <title>Editar Veículo</title>
</head>

<div class="page-header">
    <h1>Editar Veículo: <?php echo htmlspecialchars($veiculo['modelo']); ?></h1>
    <a href="gerenciar_rastreamento.php?id=<?php echo $veiculo['id_usuario']; ?>" class="btn btn-primary">Cancelar</a>
</div>

<?php
// Exibe mensagens de sucesso ou erro (se houver vindo de outra página)
if (isset($_SESSION['success_message'])) {
    echo '<div class="feedback-message feedback-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="feedback-message feedback-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}
?>


<div class="card">
    <form method="POST" action="editar_veiculo.php?id_veiculo=<?php echo $id_veiculo; ?>"> {/* Adicionado action */}
        <input type="hidden" name="id_usuario" value="<?php echo $veiculo['id_usuario']; ?>">
        <div class="form-group">
            <label for="modelo">Modelo do Veículo</label>
            <input type="text" id="modelo" name="modelo" value="<?php echo htmlspecialchars($veiculo['modelo']); ?>" required>
        </div>
        <div class="form-group">
            <label for="placa">Placa</label>
            <input type="text" id="placa" name="placa" value="<?php echo htmlspecialchars($veiculo['placa']); ?>">
        </div>
        <div class="form-group">
            <label for="icone_url">Logo da Marca</label>
            <select id="icone_url" name="icone_url"> {/* REMOVIDO style e class daqui */}
                <?php $logos = [
                    "imagens/logos/chevrolet.png" => "Chevrolet", "imagens/logos/citroen.png" => "Citroën",
                    "imagens/logos/hyundai.png" => "Hyundai", "imagens/logos/mercedes.png" => "Mercedes-Benz",
                    "imagens/logos/mitsubishi.png" => "Mitsubishi", "imagens/logos/renault.png" => "Renault",
                    "imagens/logos/toyota.png" => "Toyota",
                    "imagens/logos/ford.png" => "Ford",
                    "imagens/logos/honda.png" => "Honda",
                    "imagens/logos/vw.png" => "Volkswagen"
                    // Adicione mais marcas se necessário
                ]; ?>
                <option value="">Selecione um logo</option>
                <?php foreach ($logos as $path => $marca): ?>
                    <option value="<?php echo $path; ?>" <?php echo (isset($veiculo['icone_url']) && $veiculo['icone_url'] == $path) ? 'selected' : ''; ?>>
                        <?php echo $marca; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>