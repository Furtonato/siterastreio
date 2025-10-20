<?php
require_once 'db.php';
include 'admin_header.php';

// --- LÓGICA PARA SALVAR AS ALTERAÇÕES ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salvar_tudo'])) {
    // Loop através de cada configuração enviada pelo formulário
    foreach ($_POST as $nome_config => $valor_config) {
        // Ignora o botão 'salvar_tudo' para não tentar salvar no banco
        if ($nome_config === 'salvar_tudo') continue;

        // Prepara a query para atualizar cada configuração no banco de dados
        $sql = "UPDATE configuracoes SET valor_config = ? WHERE nome_config = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $valor_config, $nome_config);
        $stmt->execute();
    }
    $_SESSION['success_message'] = "Configurações salvas com sucesso!";
    // Redireciona para a mesma página para evitar reenvio do formulário
    header("Location: personalizacao.php");
    exit();
}

// --- BUSCA AS CONFIGURAÇÕES ATUAIS PARA PREENCHER O FORMULÁRIO ---
$resultado = $conexao->query("SELECT nome_config, valor_config FROM configuracoes");
$config = [];
while ($row = $resultado->fetch_assoc()) {
    $config[$row['nome_config']] = $row['valor_config'];
}
?>

<head>
    <title>Área Designer - Personalização</title>
    <style>
        /* Estilo para o novo layout de 3 colunas */
        .designer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .designer-column fieldset {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            height: 100%;
        }
        .designer-column legend {
            font-weight: bold;
            color: #333;
            padding: 0 10px;
            font-size: 1.1em;
        }
        /* Ajuste para o input de cor */
        .form-group input[type="color"] {
            padding: 0;
            height: 40px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group small {
            margin-top: 5px;
            display: block;
            color: #777;
        }
    </style>
</head>

<div class="page-header">
    <h1><i class="fas fa-paint-brush"></i> Área Designer</h1>
</div>

<?php
// Exibe mensagens de sucesso
if (isset($_SESSION['success_message'])) {
    echo '<div class="feedback-message feedback-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
?>

<div class="card">
    <form action="personalizacao.php" method="POST">
        <div class="designer-grid">

            <div class="designer-column">
                <fieldset>
                    <legend>Escrita do Menu</legend>
                    <div class="form-group">
                        <label for="menu_home">Menu Home:</label>
                        <input type="text" id="menu_home" name="menu_home_text" value="Home" disabled>
                    </div>
                    <div class="form-group">
                        <label for="menu_empresa">Menu Empresa:</label>
                        <input type="text" id="menu_empresa" name="menu_empresa_text" value="Quem Somos" disabled>
                    </div>
                     <small style="color: red; margin-top: 20px; display: block;">*A edição dos textos do menu será implementada na próxima etapa.</small>
                </fieldset>
            </div>

            <div class="designer-column">
                <fieldset>
                    <legend>Elementos e Cores</legend>
                    <div class="form-group">
                        <label for="url_logo_principal">Logo (URL):</label>
                        <input type="text" id="url_logo_principal" name="url_logo_principal" value="<?php echo htmlspecialchars($config['url_logo_principal'] ?? ''); ?>">
                        <small>Por enquanto, cole a URL da imagem aqui. A função de upload será adicionada depois.</small>
                    </div>
                    <div class="form-group">
                        <label for="cor_fundo">Cor do fundo:</label>
                        <input type="color" id="cor_fundo" name="cor_fundo" value="<?php echo htmlspecialchars($config['cor_fundo'] ?? '#FFFFFF'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cor_menu">Cor do menu:</label>
                        <input type="color" id="cor_menu" name="cor_menu" value="<?php echo htmlspecialchars($config['cor_menu'] ?? '#FFFFFF'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cor_formulario">Cor do formulário:</label>
                        <input type="color" id="cor_formulario" name="cor_formulario" value="<?php echo htmlspecialchars($config['cor_formulario'] ?? '#E2E2E2'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cor_botao_destaque">Cor do botão "Enviar":</label>
                        <input type="color" id="cor_botao_destaque" name="cor_botao_destaque" value="<?php echo htmlspecialchars($config['cor_botao_destaque'] ?? '#FF2424'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cor_rodape">Cor do rodapé:</label>
                        <input type="color" id="cor_rodape" name="cor_rodape" value="<?php echo htmlspecialchars($config['cor_rodape'] ?? '#9F9F9F'); ?>">
                    </div>
                </fieldset>
            </div>

            <div class="designer-column">
                <fieldset>
                    <legend>Cores das Fontes</legend>
                    <div class="form-group">
                        <label for="cor_fonte_menu">Cor da fonte do menu:</label>
                        <input type="color" id="cor_fonte_menu" name="cor_fonte_menu" value="<?php echo htmlspecialchars($config['cor_fonte_menu'] ?? '#838383'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cor_fonte_titulos">Cor da fonte dos títulos:</label>
                        <input type="color" id="cor_fonte_titulos" name="cor_fonte_titulos" value="<?php echo htmlspecialchars($config['cor_fonte_titulos'] ?? '#547590'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="cor_fonte_textos">Cor da fonte dos textos:</label>
                        <input type="color" id="cor_fonte_textos" name="cor_fonte_textos" value="<?php echo htmlspecialchars($config['cor_fonte_textos'] ?? '#838383'); ?>">
                    </div>
                </fieldset>
            </div>

        </div>

        <button type="submit" name="salvar_tudo" class="btn btn-success" style="margin-top: 20px;">Salvar Tudo</button>
    </form>
</div>

<?php include 'admin_footer.php'; ?>