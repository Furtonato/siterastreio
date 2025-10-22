<?php
// 1. LOGIC MOVED TO TOP
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o ID do veículo foi passado na URL
if (!isset($_GET['id'])) {
    header("Location: gerenciar_vitrine.php");
    exit();
}
$id_veiculo = $_GET['id'];

// --- LÓGICA PARA ATUALIZAR OS DADOS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coleta todos os dados do formulário
    $titulo = $_POST['titulo'];
    $subtitulo = $_POST['subtitulo'];
    $preco = $_POST['preco'];
    $ano_fabricacao = $_POST['ano_fabricacao'];
    $ano_modelo = $_POST['ano_modelo'];
    $quilometragem = $_POST['quilometragem'];
    $cambio = $_POST['cambio'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];
    $descricao = $_POST['descricao'];
    $opcionais = $_POST['opcionais'];
    $foto_principal = $_POST['foto_principal'];
    $fotos_galeria = $_POST['fotos_galeria'];
    $status = $_POST['status'];

    $sql = "UPDATE vitrine_veiculos SET 
                titulo = ?, subtitulo = ?, preco = ?, ano_fabricacao = ?, ano_modelo = ?, 
                quilometragem = ?, cambio = ?, cidade = ?, estado = ?, descricao = ?, 
                opcionais = ?, foto_principal = ?, fotos_galeria = ?, status = ? 
            WHERE id = ?";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssdiisssssssssi", 
        $titulo, $subtitulo, $preco, $ano_fabricacao, $ano_modelo, 
        $quilometragem, $cambio, $cidade, $estado, $descricao, 
        $opcionais, $foto_principal, $fotos_galeria, $status, $id_veiculo
    );
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Veículo atualizado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao atualizar veículo: " . $stmt->error;
    }
    header("Location: gerenciar_vitrine.php");
    exit();
}

// 2. NOW INCLUDE HEADER
include 'admin_header.php';

// --- BUSCA OS DADOS ATUAIS DO VEÍCULO PARA PREENCHER O FORMULÁRIO ---
$stmt_busca = $conexao->prepare("SELECT * FROM vitrine_veiculos WHERE id = ?");
$stmt_busca->bind_param("i", $id_veiculo);
$stmt_busca->execute();
$veiculo = $stmt_busca->get_result()->fetch_assoc();

if (!$veiculo) {
    $_SESSION['error_message'] = "Veículo não encontrado.";
    header("Location: gerenciar_vitrine.php");
    exit();
}
?>

<head>
    <title>Editar Veículo da Vitrine</title>
</head>

<div class="page-header">
    <h1>Editar Veículo: <?php echo htmlspecialchars($veiculo['titulo']); ?></h1>
    <a href="gerenciar_vitrine.php" class="btn btn-primary">Cancelar</a>
</div>

<div class="card">
    <form action="editar_vitrine_veiculo.php?id=<?php echo $id_veiculo; ?>" method="POST">
        <div class="form-row">
            <div class="form-group-half">
                <label for="titulo">Título (Marca, Modelo, Versão)</label>
                <input type="text" id="titulo" name="titulo" required value="<?php echo htmlspecialchars($veiculo['titulo']); ?>">
            </div>
            <div class="form-group-half">
                <label for="subtitulo">Subtítulo (Opcional)</label>
                <input type="text" id="subtitulo" name="subtitulo" value="<?php echo htmlspecialchars($veiculo['subtitulo']); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group-third">
                <label for="preco">Preço (somente números)</label>
                <input type="number" step="0.01" id="preco" name="preco" required value="<?php echo htmlspecialchars($veiculo['preco']); ?>">
            </div>
            <div class="form-group-third">
                <label for="ano_fabricacao">Ano Fabricação</label>
                <input type="number" id="ano_fabricacao" name="ano_fabricacao" value="<?php echo htmlspecialchars($veiculo['ano_fabricacao']); ?>">
            </div>
            <div class="form-group-third">
                <label for="ano_modelo">Ano Modelo</label>
                <input type="number" id="ano_modelo" name="ano_modelo" value="<?php echo htmlspecialchars($veiculo['ano_modelo']); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group-third">
                <label for="quilometragem">Quilometragem</label>
                <input type="number" id="quilometragem" name="quilometragem" value="<?php echo htmlspecialchars($veiculo['quilometragem']); ?>">
            </div>
            <div class="form-group-third">
                <label for="cambio">Câmbio</label>
                <input type="text" id="cambio" name="cambio" value="<?php echo htmlspecialchars($veiculo['cambio']); ?>">
            </div>
            <div class="form-group-third">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="disponivel" <?php echo ($veiculo['status'] == 'disponivel') ? 'selected' : ''; ?>>Disponível</option>
                    <option value="vendido" <?php echo ($veiculo['status'] == 'vendido') ? 'selected' : ''; ?>>Vendido</option>
                </select>
            </div>
        </div>
        <div class="form-row">
             <div class="form-group-half">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($veiculo['cidade']); ?>">
            </div>
            <div class="form-group-half">
                <label for="estado">Estado (UF)</label>
                <input type="text" id="estado" name="estado" maxlength="2" value="<?php echo htmlspecialchars($veiculo['estado']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição Completa</label>
            <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($veiculo['descricao']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="opcionais">Opcionais</label>
            <textarea id="opcionais" name="opcionais" rows="3"><?php echo htmlspecialchars($veiculo['opcionais']); ?></textarea>
            <small>Separe os itens por vírgula. Ex: Ar Condicionado, Vidro Elétrico, Trava Elétrica</small>
        </div>
        <div class="form-group">
            <label for="foto_principal">URL da Foto Principal</label>
            <input type="text" id="foto_principal" name="foto_principal" required value="<?php echo htmlspecialchars($veiculo['foto_principal']); ?>">
            <small>Esta é a foto que aparecerá na listagem principal.</small>
        </div>
        <div class="form-group">
            <label for="fotos_galeria">URLs das Fotos da Galeria</label>
            <textarea id="fotos_galeria" name="fotos_galeria" rows="3"><?php echo htmlspecialchars($veiculo['fotos_galeria']); ?></textarea>
            <small>Cole uma URL de imagem por linha. A primeira será a foto principal na página de detalhes.</small>
        </div>
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>