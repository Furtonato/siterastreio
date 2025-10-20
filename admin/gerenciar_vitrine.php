<?php
require_once 'db.php';
include 'admin_header.php';

// --- LÓGICA PARA ADICIONAR OU EXCLUIR VEÍCULOS ---

// SE O FORMULÁRIO DE ADICIONAR FOR ENVIADO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_veiculo'])) {
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

    $sql = "INSERT INTO vitrine_veiculos (titulo, subtitulo, preco, ano_fabricacao, ano_modelo, quilometragem, cambio, cidade, estado, descricao, opcionais, foto_principal, fotos_galeria, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexao->prepare($sql);
    // 's' para string, 'd' para decimal/double, 'i' para integer
    $stmt->bind_param("ssdiisssssssss", $titulo, $subtitulo, $preco, $ano_fabricacao, $ano_modelo, $quilometragem, $cambio, $cidade, $estado, $descricao, $opcionais, $foto_principal, $fotos_galeria, $status);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Veículo da vitrine adicionado com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao adicionar veículo: " . $stmt->error;
    }
    header("Location: gerenciar_vitrine.php");
    exit();
}

// SE UMA AÇÃO DE EXCLUSÃO FOR SOLICITADA
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_para_excluir = $_GET['id'];
    $sql = "DELETE FROM vitrine_veiculos WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_para_excluir);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Veículo excluído com sucesso!";
    } else {
        $_SESSION['error_message'] = "Erro ao excluir veículo.";
    }
    header("Location: gerenciar_vitrine.php");
    exit();
}

// Busca todos os veículos para listar na tabela
$veiculos = $conexao->query("SELECT id, foto_principal, titulo, preco, ano_fabricacao, ano_modelo, status FROM vitrine_veiculos ORDER BY id DESC");
?>

<head>
    <title>Gerenciar Vitrine de Veículos</title>
</head>

<div class="page-header">
    <h1>Gerenciar Vitrine de Veículos</h1>
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
    <h2>Adicionar Novo Veículo à Vitrine</h2>
    <form action="gerenciar_vitrine.php" method="POST">
        <div class="form-row">
            <div class="form-group-half">
                <label for="titulo">Título (Marca, Modelo, Versão)</label>
                <input type="text" id="titulo" name="titulo" required placeholder="Ex: FIAT STRADA FREEDOM 1.3">
            </div>
            <div class="form-group-half">
                <label for="subtitulo">Subtítulo (Opcional)</label>
                <input type="text" id="subtitulo" name="subtitulo" placeholder="Ex: SUPER CONSERVADA">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group-third">
                <label for="preco">Preço (somente números)</label>
                <input type="number" step="0.01" id="preco" name="preco" required placeholder="Ex: 89990.00">
            </div>
            <div class="form-group-third">
                <label for="ano_fabricacao">Ano Fabricação</label>
                <input type="number" id="ano_fabricacao" name="ano_fabricacao" placeholder="Ex: 2021">
            </div>
            <div class="form-group-third">
                <label for="ano_modelo">Ano Modelo</label>
                <input type="number" id="ano_modelo" name="ano_modelo" placeholder="Ex: 2021">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group-third">
                <label for="quilometragem">Quilometragem</label>
                <input type="number" id="quilometragem" name="quilometragem" placeholder="Ex: 34000">
            </div>
            <div class="form-group-third">
                <label for="cambio">Câmbio</label>
                <input type="text" id="cambio" name="cambio" placeholder="Ex: MANUAL">
            </div>
            <div class="form-group-third">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="disponivel">Disponível</option>
                    <option value="vendido">Vendido</option>
                </select>
            </div>
        </div>
        <div class="form-row">
             <div class="form-group-half">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" placeholder="Ex: São Paulo">
            </div>
            <div class="form-group-half">
                <label for="estado">Estado (UF)</label>
                <input type="text" id="estado" name="estado" maxlength="2" placeholder="Ex: SP">
            </div>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição Completa</label>
            <textarea id="descricao" name="descricao" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label for="opcionais">Opcionais</label>
            <textarea id="opcionais" name="opcionais" rows="3"></textarea>
            <small>Separe os itens por vírgula. Ex: Ar Condicionado, Vidro Elétrico, Trava Elétrica</small>
        </div>
        <div class="form-group">
            <label for="foto_principal">URL da Foto Principal</label>
            <input type="text" id="foto_principal" name="foto_principal" required placeholder="imagens/veiculos/nome-do-arquivo.jpg">
            <small>Esta é a foto que aparecerá na listagem principal.</small>
        </div>
        <div class="form-group">
            <label for="fotos_galeria">URLs das Fotos da Galeria</label>
            <textarea id="fotos_galeria" name="fotos_galeria" rows="3"></textarea>
            <small>Cole uma URL de imagem por linha. A primeira será a foto principal na página de detalhes.</small>
        </div>
        <button type="submit" name="add_veiculo" class="btn btn-success">Adicionar Veículo</button>
    </form>
</div>

<div class="card">
    <h2>Veículos na Vitrine</h2>
    <table class="content-table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Título</th>
                <th>Preço</th>
                <th>Ano</th>
                <th>Status</th>
                <th width="180px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($veiculos && $veiculos->num_rows > 0): ?>
                <?php while ($vec = $veiculos->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../<?php echo htmlspecialchars($vec['foto_principal']); ?>" alt="Foto" style="width: 100px; height: auto; border-radius: 4px;"></td>
                        <td><?php echo htmlspecialchars($vec['titulo']); ?></td>
                        <td>R$ <?php echo number_format($vec['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($vec['ano_fabricacao']) . '/' . htmlspecialchars($vec['ano_modelo']); ?></td>
                        <td><?php echo htmlspecialchars($vec['status']); ?></td>
                        <td class="actions-cell">
                            <a href="editar_vitrine_veiculo.php?id=<?php echo $vec['id']; ?>" class="btn btn-warning">Editar</a>
                            <a href="gerenciar_vitrine.php?action=delete&id=<?php echo $vec['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este veículo da vitrine?');">Excluir</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Nenhum veículo cadastrado na vitrine.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>