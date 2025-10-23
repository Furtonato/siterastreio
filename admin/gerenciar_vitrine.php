<?php
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}

$opcionais_disponiveis = [
    'Ar Condicionado', 'Direção Hidráulica', 'Vidro Elétrico', 'Trava Elétrica',
    'Alarme', 'Airbag', 'Freios ABS', 'Rodas de Liga Leve', 'Som / Multimídia',
    'Sensor de Estacionamento', 'Câmera de Ré', 'Teto Solar', 'Bancos de Couro'
];
sort($opcionais_disponiveis);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_veiculo'])) {

    $upload_dir = '../uploads/vitrine/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $foto_principal_path = '';
    $fotos_galeria_paths = [];

    if (isset($_FILES['foto_principal']) && $_FILES['foto_principal']['error'] === UPLOAD_ERR_OK) {
        $file_principal = $_FILES['foto_principal'];
        $file_name_principal = 'principal_' . uniqid() . '-' . basename($file_principal['name']);
        $target_path_principal = $upload_dir . $file_name_principal;
        if (move_uploaded_file($file_principal['tmp_name'], $target_path_principal)) {
            $foto_principal_path = 'uploads/vitrine/' . $file_name_principal;
        } else {
            $_SESSION['error_message'] = "Erro ao mover a foto principal.";
        }
    } else {
        $_SESSION['error_message'] = "A foto principal é obrigatória.";
    }

    if (isset($_FILES['fotos_galeria']) && !empty($_FILES['fotos_galeria']['name'][0])) {
        $files_galeria = $_FILES['fotos_galeria'];
        foreach ($files_galeria['name'] as $key => $name) {
            if ($files_galeria['error'][$key] === UPLOAD_ERR_OK) {
                $file_name_galeria = 'galeria_' . uniqid() . '-' . basename($name);
                $target_path_galeria = $upload_dir . $file_name_galeria;
                if (move_uploaded_file($files_galeria['tmp_name'][$key], $target_path_galeria)) {
                    $fotos_galeria_paths[] = 'uploads/vitrine/' . $file_name_galeria;
                }
            }
        }
    }
    $fotos_galeria_string = implode("\n", $fotos_galeria_paths);

    $opcionais_selecionados = isset($_POST['opcionais']) && is_array($_POST['opcionais']) ? $_POST['opcionais'] : [];
    $opcionais_string = implode(',', $opcionais_selecionados);

    if (empty($_SESSION['error_message']) && !empty($foto_principal_path)) {
        $titulo = $_POST['titulo'];
        $subtitulo = $_POST['subtitulo'];
        $preco = $_POST['preco'];
        $ano_fabricacao = $_POST['ano_fabricacao'];
        $ano_modelo = $_POST['ano_modelo'];
        $quilometragem = $_POST['quilometragem'];
        $cambio = $_POST['cambio'];
        $combustivel = $_POST['combustivel'];
        $cor = $_POST['cor'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];
        $descricao = $_POST['descricao'];
        $status = $_POST['status'];
        $placa = $_POST['placa'];

        $sql = "INSERT INTO vitrine_veiculos (
                    titulo, subtitulo, preco, ano_fabricacao, ano_modelo, quilometragem,
                    cambio, combustivel, cor, cidade, estado, descricao, opcionais,
                    foto_principal, fotos_galeria, status, placa
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssdiissssssssssss",
            $titulo, $subtitulo, $preco, $ano_fabricacao, $ano_modelo, $quilometragem,
            $cambio, $combustivel, $cor, $cidade, $estado, $descricao, $opcionais_string,
            $foto_principal_path, $fotos_galeria_string, $status, $placa
        );

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Veículo da vitrine adicionado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao adicionar veículo: " . $stmt->error;
        }
         $stmt->close();
    }

    header("Location: gerenciar_vitrine.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_para_excluir = (int)$_GET['id'];

    $stmt_busca_fotos = $conexao->prepare("SELECT foto_principal, fotos_galeria FROM vitrine_veiculos WHERE id = ?");
    if($stmt_busca_fotos){
        $stmt_busca_fotos->bind_param("i", $id_para_excluir);
        $stmt_busca_fotos->execute();
        $fotos = $stmt_busca_fotos->get_result()->fetch_assoc();
        $stmt_busca_fotos->close();

        if ($fotos) {
            if (!empty($fotos['foto_principal']) && file_exists('../' . $fotos['foto_principal'])) {
                @unlink('../' . $fotos['foto_principal']);
            }
            if (!empty($fotos['fotos_galeria'])) {
                $galeria_array = explode("\n", $fotos['fotos_galeria']);
                foreach ($galeria_array as $foto_path) {
                    if (!empty($foto_path) && file_exists('../' . $foto_path)) {
                        @unlink('../' . $foto_path);
                    }
                }
            }
        }
    }

    $sql = "DELETE FROM vitrine_veiculos WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    if($stmt){
        $stmt->bind_param("i", $id_para_excluir);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Veículo excluído com sucesso (e imagens associadas removidas).";
        } else {
            $_SESSION['error_message'] = "Erro ao excluir veículo.";
        }
        $stmt->close();
    } else {
         $_SESSION['error_message'] = "Erro ao preparar exclusão.";
    }
    header("Location: gerenciar_vitrine.php");
    exit();
}

include 'admin_header.php';

$veiculos_result = $conexao->query("SELECT id, foto_principal, titulo, preco, ano_fabricacao, ano_modelo, status FROM vitrine_veiculos ORDER BY id DESC");
$veiculos = $veiculos_result ? $veiculos_result : null;

?>

<head>
    <title>Gerenciar Vitrine de Veículos</title>
    <style>
        .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; background-color: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; margin-top: 5px; }
        .checkbox-grid label { display: flex; align-items: center; gap: 8px; font-weight: normal; font-size: 0.9em; cursor: pointer; }
        .checkbox-grid input[type="checkbox"] { cursor: pointer; }
    </style>
</head>

<div class="page-header">
    <h1>Gerenciar Vitrine de Veículos</h1>
</div>

<?php
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
    <h2>Adicionar Novo Veículo à Vitrine</h2>
    <form action="gerenciar_vitrine.php" method="POST" enctype="multipart/form-data">
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
                <label for="preco">Preço (Ex: 89990.00)</label>
                <input type="number" step="0.01" id="preco" name="preco" required placeholder="89990.00">
            </div>
            <div class="form-group-third">
                <label for="ano_fabricacao">Ano Fabricação</label>
                <input type="number" id="ano_fabricacao" name="ano_fabricacao" placeholder="Ex: 2021" min="1900" max="<?php echo date('Y') + 1; ?>">
            </div>
            <div class="form-group-third">
                <label for="ano_modelo">Ano Modelo</label>
                <input type="number" id="ano_modelo" name="ano_modelo" placeholder="Ex: 2021" min="1900" max="<?php echo date('Y') + 1; ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group-third">
                <label for="quilometragem">Quilometragem</label>
                <input type="number" id="quilometragem" name="quilometragem" placeholder="Ex: 34000" min="0">
            </div>
             <div class="form-group-third">
                <label for="placa">Placa</label>
                <input type="text" id="placa" name="placa" placeholder="Ex: ABC1D23">
            </div>
            <div class="form-group-third">
                <label for="cambio">Câmbio</label>
                <input type="text" id="cambio" name="cambio" placeholder="Ex: MANUAL">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group-third">
                <label for="combustivel">Combustível</label>
                <input type="text" id="combustivel" name="combustivel" placeholder="Ex: Flex">
            </div>
            <div class="form-group-third">
                <label for="cor">Cor</label>
                <input type="text" id="cor" name="cor" placeholder="Ex: Prata">
            </div>
             <div class="form-group-third">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="disponivel" selected>Disponível</option>
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
            <label>Opcionais</label>
            <div class="checkbox-grid">
                <?php foreach ($opcionais_disponiveis as $opcional): ?>
                    <label>
                        <input type="checkbox" name="opcionais[]" value="<?php echo htmlspecialchars($opcional); ?>">
                        <?php echo htmlspecialchars($opcional); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição Completa</label>
            <textarea id="descricao" name="descricao" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="foto_principal">Foto Principal (Obrigatória)</label>
            <input type="file" id="foto_principal" name="foto_principal" accept="image/jpeg, image/png, image/webp" required>
            <small>Esta é a foto que aparecerá na listagem principal.</small>
        </div>

        <div class="form-group">
            <label for="fotos_galeria">Fotos da Galeria (Opcional)</label>
            <input type="file" id="fotos_galeria" name="fotos_galeria[]" multiple accept="image/jpeg, image/png, image/webp">
            <small>Selecione uma ou mais imagens para a galeria de detalhes.</small>
        </div>

        <button type="submit" name="add_veiculo" class="btn btn-success">Adicionar Veículo</button>
    </form>
</div>

<div class="card">
    <h2>Veículos na Vitrine</h2>
    <table class="content-table">
        <thead>
            <tr> <th>Foto</th> <th>Título</th> <th>Preço</th> <th>Ano</th> <th>Status</th> <th width="180px">Ações</th> </tr>
        </thead>
        <tbody>
            <?php if ($veiculos && $veiculos->num_rows > 0): ?>
                <?php while ($vec = $veiculos->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (!empty($vec['foto_principal'])): ?>
                                <img src="../<?php echo htmlspecialchars($vec['foto_principal']); ?>" alt="Foto" style="width: 100px; height: auto; border-radius: 4px;">
                            <?php else: ?> <span>Sem Foto</span> <?php endif; ?>
                        </td>
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
                 <?php $veiculos->free(); ?>
            <?php else: ?>
                <tr><td colspan="6">Nenhum veículo cadastrado na vitrine.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'admin_footer.php'; ?>