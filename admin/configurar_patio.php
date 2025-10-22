<?php
// 1. START SESSION (if not already) AND REQUIRE DB
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// 2. MOVED ALL FORM LOGIC TO THE TOP
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $endereco_origem = $_POST['endereco_origem'];
    $sql = "INSERT INTO configuracoes (chave, valor) VALUES ('endereco_origem', ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $endereco_origem);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Endereço de origem salvo com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao salvar o endereço.";
    }
    // This redirect will now work
    header("Location: configurar_patio.php");
    exit();
}

// 3. NOW INCLUDE THE HTML HEADER
include 'admin_header.php';

// 4. Logic for fetching data (can stay here)
$sql_busca = "SELECT valor FROM configuracoes WHERE chave = 'endereco_origem' LIMIT 1";
$resultado = $conexao->query($sql_busca);
$config = $resultado->fetch_assoc();
$endereco_atual = $config['valor'] ?? '';
?>

<head>
    <title>Configurar Endereço de Origem</title>
</head>

<div class="page-header">
    <h1>Configurar Endereço de Origem (Pátio)</h1>
</div>

<?php
if(isset($_SESSION['success_message'])) {
    echo '<div class="feedback-message feedback-success">'.$_SESSION['success_message'].'</div>';
    unset($_SESSION['success_message']);
}
if(isset($_SESSION['error_message'])) {
    echo '<div class="feedback-message feedback-error">'.$_SESSION['error_message'].'</div>';
    unset($_SESSION['error_message']);
}
?>

<div class="card">
    <h2>Endereço Fixo de Partida</h2>
    <p style="margin-bottom: 20px; color: var(--text-secondary);">Este é o endereço do seu pátio/garagem. Ele será usado como ponto de partida inicial para os veículos. Você só precisa configurar isso uma vez.</p>
    <form action="configurar_patio.php" method="POST">
        <div class="form-group">
            <label for="endereco_origem">Endereço Completo do Pátio</label>
            <input type="text" id="endereco_origem" name="endereco_origem" value="<?php echo htmlspecialchars($endereco_atual); ?>" required placeholder="Ex: Rua, Número, Bairro, Cidade - Estado, CEP">
        </div>
        <button type="submit" class="btn btn-success">Salvar Endereço</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>