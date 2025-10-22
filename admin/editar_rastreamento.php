<?php
// 1. LOGIC MOVED TO TOP
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se está logado (adicionado para segurança)
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
    $status = $_POST['status'];
    $localizacao = $_POST['localizacao_atual'];
    $latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : null;
    $progresso = (int)$_POST['progresso']; // Pega o progresso

    // Garante que o progresso esteja entre 0 e 100
    if ($progresso < 0) {
        $progresso = 0;
    } elseif ($progresso > 100) {
        $progresso = 100;
    }

    // Atualiza incluindo o progresso
    $sql = "UPDATE rastreamentos SET status = ?, localizacao_atual = ?, latitude = ?, longitude = ?, progresso = ? WHERE id_veiculo = ?";
    $stmt = $conexao->prepare($sql);
    // Adiciona "i" para o inteiro do progresso
    $stmt->bind_param("ssddii", $status, $localizacao, $latitude, $longitude, $progresso, $id_veiculo);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Rastreamento atualizado!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar.";
    }
    // Redireciona para a mesma página para ver a atualização
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// 2. NOW INCLUDE HEADER (após toda a lógica de processamento e redirect)
include 'admin_header.php';

// --- BUSCA DE DADOS ---
// Busca incluindo o progresso
$sql_busca = "SELECT v.modelo, v.id_usuario, r.status, r.localizacao_atual, r.progresso
              FROM veiculos v
              JOIN rastreamentos r ON v.id = r.id_veiculo
              WHERE v.id = ?";
$stmt_busca = $conexao->prepare($sql_busca);
$stmt_busca->bind_param("i", $id_veiculo);
$stmt_busca->execute();
$dados = $stmt_busca->get_result()->fetch_assoc();

// Se não encontrar dados, redireciona de volta (melhor checar antes de incluir o header, mas ok aqui)
if (!$dados) {
    $_SESSION['error_message'] = "Dados de rastreamento não encontrados.";
    // Tenta encontrar o id do cliente para redirecionar corretamente
    $stmt_fallback = $conexao->prepare("SELECT id_usuario FROM veiculos WHERE id = ?");
    $stmt_fallback->bind_param("i", $id_veiculo);
    $stmt_fallback->execute();
    $fallback_data = $stmt_fallback->get_result()->fetch_assoc();
    if ($fallback_data) {
        header("Location: gerenciar_rastreamento.php?id=" . $fallback_data['id_usuario']);
    } else {
        header("Location: gerenciar_clientes.php");
    }
    exit();
}
?>

<head>
    <title>Editar Rastreamento</title>
</head>

<div class="page-header">
    <h1>Editar Rastreamento do Veículo: <?php echo htmlspecialchars($dados['modelo']); ?></h1>
    <a href="gerenciar_rastreamento.php?id=<?php echo $dados['id_usuario']; ?>" class="btn btn-primary">Voltar</a>
</div>

<?php
// Exibe mensagens de sucesso ou erro
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
    <form id="form-rastreamento" method="POST">
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">

        <div class="form-group">
            <label for="status">Status da Entrega</label>
            <input type="text" id="status" name="status" value="<?php echo htmlspecialchars($dados['status'] ?? ''); ?>" placeholder="Ex: EM TRÂNSITO">
        </div>

        <div class="form-group">
            <label for="progresso">Progresso Atual (%)</label>
            <input type="number" id="progresso" name="progresso" min="0" max="100" value="<?php echo htmlspecialchars($dados['progresso'] ?? 0); ?>" required>
        </div>

        <div class="form-group">
            <label for="localizacao_atual">Atualizar Posição do Ping no Mapa (Digite um Endereço)</label>
            <input type="text" id="localizacao_atual" name="localizacao_atual" value="<?php echo htmlspecialchars($dados['localizacao_atual'] ?? ''); ?>" placeholder="Dica: Use endereços mais simples. Ex: Rua, Número, Cidade">
            <small>Ao salvar, este endereço moverá o marcador no mapa do cliente para a nova posição (se encontrado).</small>
        </div>
        <button type="submit" class="btn btn-success">Atualizar Rastreamento</button>
    </form>
</div>

<script>
// Mantém o script de geocodificação como estava
document.getElementById('form-rastreamento').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio padrão

    const form = this;
    const address = document.getElementById('localizacao_atual').value;
    const submitButton = form.querySelector('button[type="submit"]');

    // Se não digitou endereço, apenas envia o formulário com status e progresso
    if (!address.trim()) {
        form.submit(); // Envia o formulário normalmente
        return;
    }

    submitButton.textContent = 'Verificando endereço...';
    submitButton.disabled = true;

    // Chama o script PHP local para geocodificação
    fetch(`geocode.php?address=${encodeURIComponent(address)}`)
        .then(res => {
            if (!res.ok) { throw new Error('A resposta da rede não foi OK (' + res.status + ')'); }
            return res.json();
        })
        .then(data => {
            if (data && data.length > 0 && data[0].lat && data[0].lon) {
                // Endereço encontrado, preenche lat/lon e envia
                document.getElementById('latitude').value = data[0].lat;
                document.getElementById('longitude').value = data[0].lon;
                form.submit(); // Envia o formulário
            } else {
                // Endereço não encontrado
                submitButton.textContent = 'Atualizar Rastreamento';
                submitButton.disabled = false;
                if (confirm("Endereço não encontrado para gerar o ping no mapa.\n\nDeseja salvar as outras informações (status e progresso) mesmo assim?")) {
                    // Limpa lat/lon e envia mesmo assim
                    document.getElementById('latitude').value = '';
                    document.getElementById('longitude').value = '';
                    form.submit(); // Envia o formulário
                }
                // Se o usuário clicar em "Cancelar" no confirm(), nada acontece.
            }
        })
        .catch(error => {
            console.error('Erro na chamada de geocodificação:', error);
            submitButton.textContent = 'Atualizar Rastreamento';
            submitButton.disabled = false;
            alert('Ocorreu um erro ao buscar as coordenadas. Verifique o console ou tente novamente.\nVocê pode salvar sem o endereço se desejar.');
        });
});
</script>

<?php include 'admin_footer.php'; ?>