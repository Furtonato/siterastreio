<?php
require_once 'db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: gerenciar_vitrine.php");
    exit();
}
$id_veiculo = (int)$_GET['id'];

$opcionais_disponiveis = [
    'Ar Condicionado', 'Direção Hidráulica', 'Vidro Elétrico', 'Trava Elétrica',
    'Alarme', 'Airbag', 'Freios ABS', 'Rodas de Liga Leve', 'Som / Multimídia',
    'Sensor de Estacionamento', 'Câmera de Ré', 'Teto Solar', 'Bancos de Couro'
];
sort($opcionais_disponiveis);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $upload_dir = '../uploads/vitrine/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $foto_principal_path = $_POST['foto_principal_atual'];
    if (isset($_FILES['foto_principal_nova']) && $_FILES['foto_principal_nova']['error'] === UPLOAD_ERR_OK) {
        $file_principal = $_FILES['foto_principal_nova'];
        $file_name_principal = 'principal_' . uniqid() . '-' . basename($file_principal['name']);
        $target_path_principal = $upload_dir . $file_name_principal;
        if (move_uploaded_file($file_principal['tmp_name'], $target_path_principal)) {
            $foto_principal_path = 'uploads/vitrine/' . $file_name_principal;
            if (!empty($_POST['foto_principal_atual']) && file_exists('../' . $_POST['foto_principal_atual'])) {
                @unlink('../' . $_POST['foto_principal_atual']);
            }
        } else {
            $_SESSION['error_message'] = "Erro ao fazer upload da nova foto principal.";
        }
    }

    $fotos_galeria_atuais = [];
    if (!empty($_POST['fotos_galeria_atuais'])) {
        $fotos_galeria_atuais = explode("\n", trim($_POST['fotos_galeria_atuais']));
    }
    $fotos_para_excluir = $_POST['delete_gallery'] ?? [];
    $fotos_galeria_mantidas = array_diff($fotos_galeria_atuais, $fotos_para_excluir);

    foreach ($fotos_para_excluir as $foto_path) {
        if (!empty($foto_path) && file_exists('../' . $foto_path)) {
            @unlink('../' . $foto_path);
        }
    }

    $fotos_galeria_novas_paths = [];
    if (isset($_FILES['fotos_galeria_novas']) && !empty($_FILES['fotos_galeria_novas']['name'][0])) {
        $files_galeria = $_FILES['fotos_galeria_novas'];
        foreach ($files_galeria['name'] as $key => $name) {
            if ($files_galeria['error'][$key] === UPLOAD_ERR_OK) {
                $file_name_galeria = 'galeria_' . uniqid() . '-' . basename($name);
                $target_path_galeria = $upload_dir . $file_name_galeria;
                if (move_uploaded_file($files_galeria['tmp_name'][$key], $target_path_galeria)) {
                    $fotos_galeria_novas_paths[] = 'uploads/vitrine/' . $file_name_galeria;
                }
            }
        }
    }

    $fotos_galeria_finais = array_merge($fotos_galeria_mantidas, $fotos_galeria_novas_paths);
    $fotos_galeria_finais = array_filter($fotos_galeria_finais);
    $fotos_galeria_string = implode("\n", $fotos_galeria_finais);

    $opcionais_selecionados = isset($_POST['opcionais']) && is_array($_POST['opcionais']) ? $_POST['opcionais'] : [];
    $opcionais_string = implode(',', $opcionais_selecionados);

    if (!isset($_SESSION['error_message'])) {
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

        $sql = "UPDATE vitrine_veiculos SET
                    titulo = ?, subtitulo = ?, preco = ?, ano_fabricacao = ?, ano_modelo = ?,
                    quilometragem = ?, cambio = ?, combustivel = ?, cor = ?, cidade = ?, estado = ?,
                    descricao = ?, opcionais = ?, foto_principal = ?, fotos_galeria = ?, status = ?,
                    placa = ?
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);
        // **** CORREÇÃO NA LINHA ABAIXO ****
        $stmt->bind_param("ssdiiisssssssssssi", // Era ssdiisssssssssssi
            $titulo, $subtitulo, $preco, $ano_fabricacao, $ano_modelo,
            $quilometragem, $cambio, $combustivel, $cor, $cidade, $estado,
            $descricao, $opcionais_string, $foto_principal_path, $fotos_galeria_string, $status,
            $placa, $id_veiculo
        );

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Veículo atualizado com sucesso!";
        } else {
             $_SESSION['error_message'] = "Erro ao atualizar veículo: " . $stmt->error;
        }
         $stmt->close();
    }

    header("Location: gerenciar_vitrine.php");
    exit();
}

include 'admin_header.php';

$stmt_busca = $conexao->prepare("SELECT id, titulo, subtitulo, preco, ano_fabricacao, ano_modelo, quilometragem, cambio, combustivel, cor, cidade, estado, descricao, opcionais, foto_principal, fotos_galeria, status, placa FROM vitrine_veiculos WHERE id = ?");
if(!$stmt_busca){
     $_SESSION['error_message'] = "Erro ao preparar busca: " . $conexao->error;
     header("Location: gerenciar_vitrine.php");
     exit();
}

$stmt_busca->bind_param("i", $id_veiculo);
$stmt_busca->execute();
$veiculo_result = $stmt_busca->get_result();
$veiculo = $veiculo_result ? $veiculo_result->fetch_assoc() : null;
$stmt_busca->close();

if (!$veiculo) {
    $_SESSION['error_message'] = "Veículo não encontrado.";
    header("Location: gerenciar_vitrine.php");
    exit();
}

$opcionais_atuais = [];
if (!empty($veiculo['opcionais'])) {
    $opcionais_atuais = array_map('trim', explode(',', $veiculo['opcionais']));
}

?>

<head>
    <title>Editar Veículo da Vitrine</title>
    <style>
        .current-image-preview { max-width: 200px; height: auto; border-radius: 5px; border: 1px solid #ddd; margin-bottom: 10px; display: block; }
        .gallery-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px; margin-top: 10px; }
        .gallery-item { position: relative; border: 1px solid #ddd; border-radius: 5px; padding: 5px; }
        .gallery-item img { width: 100%; height: 80px; object-fit: cover; border-radius: 3px; }
        .gallery-item label { font-size: 0.8em; display: block; margin-top: 5px; text-align: center; color: #dc3545; cursor: pointer; }
        .gallery-item input[type="checkbox"] { margin-right: 5px; cursor: pointer;}
        .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; background-color: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; margin-top: 5px; }
        .checkbox-grid label { display: flex; align-items: center; gap: 8px; font-weight: normal; font-size: 0.9em; cursor: pointer; }
        .checkbox-grid input[type="checkbox"] { cursor: pointer; }
    </style>
</head>

<div class="page-header">
    <h1>Editar Veículo: <?php echo htmlspecialchars($veiculo['titulo']); ?></h1>
    <a href="gerenciar_vitrine.php" class="btn btn-primary">Cancelar</a>
</div>
<?php
if (isset($_SESSION['error_message'])) {
    echo '<div class="feedback-message feedback-error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}
?>

<div class="card">
    <form action="editar_vitrine_veiculo.php?id=<?php echo $id_veiculo; ?>" method="POST" enctype="multipart/form-data">

        <input type="hidden" name="foto_principal_atual" value="<?php echo htmlspecialchars($veiculo['foto_principal'] ?? ''); ?>">
        <input type="hidden" name="fotos_galeria_atuais" value="<?php echo htmlspecialchars($veiculo['fotos_galeria'] ?? ''); ?>">

        <div class="form-row">
            <div class="form-group-half">
                <label for="titulo">Título (Marca, Modelo, Versão)</label>
                <input type="text" id="titulo" name="titulo" required value="<?php echo htmlspecialchars($veiculo['titulo'] ?? ''); ?>">
            </div>
            <div class="form-group-half">
                <label for="subtitulo">Subtítulo (Opcional)</label>
                <input type="text" id="subtitulo" name="subtitulo" value="<?php echo htmlspecialchars($veiculo['subtitulo'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group-third">
                <label for="preco">Preço (Ex: 89990.00)</label>
                <input type="number" step="0.01" id="preco" name="preco" required value="<?php echo htmlspecialchars($veiculo['preco'] ?? ''); ?>">
            </div>
            <div class="form-group-third">
                <label for="ano_fabricacao">Ano Fabricação</label>
                <input type="number" id="ano_fabricacao" name="ano_fabricacao" value="<?php echo htmlspecialchars($veiculo['ano_fabricacao'] ?? ''); ?>" min="1900" max="<?php echo date('Y') + 1; ?>">
            </div>
            <div class="form-group-third">
                <label for="ano_modelo">Ano Modelo</label>
                <input type="number" id="ano_modelo" name="ano_modelo" value="<?php echo htmlspecialchars($veiculo['ano_modelo'] ?? ''); ?>" min="1900" max="<?php echo date('Y') + 1; ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group-third">
                <label for="quilometragem">Quilometragem</label>
                <input type="number" id="quilometragem" name="quilometragem" value="<?php echo htmlspecialchars($veiculo['quilometragem'] ?? ''); ?>" min="0">
            </div>
             <div class="form-group-third">
                <label for="placa">Placa</label>
                <input type="text" id="placa" name="placa" value="<?php echo htmlspecialchars($veiculo['placa'] ?? ''); ?>">
            </div>
            <div class="form-group-third">
                <label for="cambio">Câmbio</label>
                <input type="text" id="cambio" name="cambio" value="<?php echo htmlspecialchars($veiculo['cambio'] ?? ''); ?>">
            </div>
        </div>
         <div class="form-row">
            <div class="form-group-third">
                <label for="combustivel">Combustível</label>
                <input type="text" id="combustivel" name="combustivel" value="<?php echo htmlspecialchars($veiculo['combustivel'] ?? ''); ?>">
            </div>
            <div class="form-group-third">
                <label for="cor">Cor</label>
                <input type="text" id="cor" name="cor" value="<?php echo htmlspecialchars($veiculo['cor'] ?? ''); ?>">
            </div>
             <div class="form-group-third">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="disponivel" <?php echo (($veiculo['status'] ?? '') == 'disponivel') ? 'selected' : ''; ?>>Disponível</option>
                    <option value="vendido" <?php echo (($veiculo['status'] ?? '') == 'vendido') ? 'selected' : ''; ?>>Vendido</option>
                </select>
            </div>
        </div>
        <div class="form-row">
             <div class="form-group-half">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($veiculo['cidade'] ?? ''); ?>">
            </div>
            <div class="form-group-half">
                <label for="estado">Estado (UF)</label>
                <input type="text" id="estado" name="estado" maxlength="2" value="<?php echo htmlspecialchars($veiculo['estado'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Opcionais</label>
            <div class="checkbox-grid">
                <?php foreach ($opcionais_disponiveis as $opcional): ?>
                    <?php $isChecked = in_array($opcional, $opcionais_atuais); ?>
                    <label>
                        <input type="checkbox" name="opcionais[]" value="<?php echo htmlspecialchars($opcional); ?>" <?php echo $isChecked ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($opcional); ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição Completa</label>
            <textarea id="descricao" name="descricao" rows="4"><?php echo htmlspecialchars($veiculo['descricao'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Foto Principal Atual</label>
            <?php if (!empty($veiculo['foto_principal'])): ?>
                <img src="../<?php echo htmlspecialchars($veiculo['foto_principal']); ?>" alt="Foto Principal" class="current-image-preview">
            <?php else: ?>
                <p><small>Nenhuma foto principal cadastrada.</small></p>
            <?php endif; ?>
            <label for="foto_principal_nova" style="margin-top:10px;">Substituir Foto Principal (Opcional)</label>
            <input type="file" id="foto_principal_nova" name="foto_principal_nova" accept="image/jpeg, image/png, image/webp">
            <small>Envie um novo arquivo apenas se desejar substituir a foto principal atual.</small>
        </div>

        <div class="form-group">
            <label>Gerenciar Fotos da Galeria</label>
            <?php
                $fotos_galeria_atuais_array = [];
                if (!empty($veiculo['fotos_galeria'])) {
                    $fotos_galeria_atuais_array = array_filter(explode("\n", trim($veiculo['fotos_galeria'])));
                }
            ?>
            <?php if (!empty($fotos_galeria_atuais_array)): ?>
                <small>Marque as fotos que deseja excluir:</small>
                <div class="gallery-preview-grid">
                    <?php foreach ($fotos_galeria_atuais_array as $foto_path): ?>
                         <?php if (!empty($foto_path)): ?>
                            <div class="gallery-item">
                                <img src="../<?php echo htmlspecialchars($foto_path); ?>" alt="Foto da Galeria">
                                <label>
                                    <input type="checkbox" name="delete_gallery[]" value="<?php echo htmlspecialchars($foto_path); ?>"> Excluir
                                </label>
                            </div>
                         <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p><small>Nenhuma foto na galeria.</small></p>
            <?php endif; ?>

            <label for="fotos_galeria_novas" style="margin-top:15px;">Adicionar Novas Fotos à Galeria (Opcional)</label>
            <input type="file" id="fotos_galeria_novas" name="fotos_galeria_novas[]" multiple accept="image/jpeg, image/png, image/webp">
            <small>Selecione uma ou mais imagens para adicionar à galeria.</small>
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>