<?php
require_once 'db.php';
include 'admin_header.php';

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

    $sql = "UPDATE rastreamentos SET status = ?, localizacao_atual = ?, latitude = ?, longitude = ? WHERE id_veiculo = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssddi", $status, $localizacao, $latitude, $longitude, $id_veiculo);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Rastreamento atualizado!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar.";
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// --- BUSCA DE DADOS ---
$sql_busca = "SELECT v.modelo, v.id_usuario, r.status, r.localizacao_atual FROM veiculos v JOIN rastreamentos r ON v.id = r.id_veiculo WHERE v.id = ?";
$stmt_busca = $conexao->prepare($sql_busca);
$stmt_busca->bind_param("i", $id_veiculo);
$stmt_busca->execute();
$dados = $stmt_busca->get_result()->fetch_assoc();
?>

<head>
    <title>Editar Rastreamento</title>
</head>

<div class="page-header">
    <h1>Editar Rastreamento do Veículo: <?php echo htmlspecialchars($dados['modelo']); ?></h1>
    <a href="gerenciar_rastreamento.php?id=<?php echo $dados['id_usuario']; ?>" class="btn btn-primary">Voltar</a>
</div>

<?php if (isset($_SESSION['success_message'])) { echo '<div class="feedback-message feedback-success">' . $_SESSION['success_message'] . '</div>'; unset($_SESSION['success_message']); } ?>

<div class="card">
    <form id="form-rastreamento" method="POST">
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        
        <div class="form-group">
            <label for="status">Status da Entrega</label>
            <input type="text" id="status" name="status" value="<?php echo htmlspecialchars($dados['status'] ?? ''); ?>" placeholder="Ex: EM TRÂNSITO">
        </div>
        <div class="form-group">
            <label for="localizacao_atual">Atualizar Posição do Ping no Mapa (Digite um Endereço)</label>
            <input type="text" id="localizacao_atual" name="localizacao_atual" value="<?php echo htmlspecialchars($dados['localizacao_atual'] ?? ''); ?>" placeholder="Dica: Use endereços mais simples. Ex: Rua, Número, Cidade">
            <small>Ao salvar, este endereço moverá o marcador no mapa do cliente para a nova posição.</small>
        </div>
        <button type="submit" class="btn btn-success">Atualizar Rastreamento</button>
    </form>
</div>

<script>
document.getElementById('form-rastreamento').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const form = this;
    const address = document.getElementById('localizacao_atual').value;
    const submitButton = form.querySelector('button[type="submit"]');

    if (!address.trim()) { 
        form.submit(); 
        return; 
    }
    
    submitButton.textContent = 'Verificando endereço...';
    submitButton.disabled = true;

    // A MUDANÇA ESTÁ AQUI: AGORA CHAMAMOS O NOSSO "ASSISTENTE" LOCAL
    fetch(`geocode.php?address=${encodeURIComponent(address)}`)
        .then(res => {
            if (!res.ok) { throw new Error('A resposta da rede não foi OK'); }
            return res.json();
        })
        .then(data => {
            if (data && data.length > 0) {
                document.getElementById('latitude').value = data[0].lat;
                document.getElementById('longitude').value = data[0].lon;
                form.submit();
            } else {
                submitButton.textContent = 'Atualizar Rastreamento';
                submitButton.disabled = false;
                if (confirm("Endereço não encontrado para gerar o ping.\n\nDeseja salvar as outras informações (como o status) mesmo assim?")) {
                    document.getElementById('latitude').value = '';
                    document.getElementById('longitude').value = '';
                    form.submit();
                }
            }
        })
        .catch(error => {
            console.error('Erro na chamada de geocodificação:', error);
            submitButton.textContent = 'Atualizar Rastreamento';
            submitButton.disabled = false;
            alert('Ocorreu um erro ao buscar as coordenadas. Verifique o console para mais detalhes.');
        });
});
</script>

<?php include 'admin_footer.php'; ?>