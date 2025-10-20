<?php
// Define que este arquivo é um CSS
header("Content-type: text/css; charset: UTF-8");

// Inclui o arquivo de conexão com o banco de dados para podermos buscar as configurações
// O '../' volta uma pasta para encontrar a pasta 'admin'
require_once '../admin/db.php';

// Busca todas as configurações do banco de dados
$resultado = $conexao->query("SELECT nome_config, valor_config FROM configuracoes");
$config = [];
while ($row = $resultado->fetch_assoc()) {
    $config[$row['nome_config']] = $row['valor_config'];
}
?>

/* ============================================== */
/* ESTILOS DINÂMICOS VINDOS DO PAINEL ADMIN       */
/* ============================================== */

/* Cor do Cabeçalho (Header) */
.header {
    background: linear-gradient(to right, <?php echo $config['cor_header_inicio']; ?>, <?php echo $config['cor_header_fim']; ?>);
}

/* Cor do Botão de Destaque "Rastrear" */
.header-nav .btn-rastrear {
    background-color: <?php echo $config['cor_botao_destaque']; ?>;
}

.header-nav .btn-rastrear:hover {
    /* Deixa o hover um pouco mais escuro que a cor principal */
    filter: brightness(0.9);
}

/* Cor de Fundo Geral (se você quiser usar) */
body {
    background-color: <?php echo $config['cor_fundo']; ?>;
}

/* Cores das Fontes */
h1, h2, h3, h4, .site-title, .author-name {
    color: <?php echo $config['cor_fonte_titulos']; ?>;
}

/* Cor da fonte do menu (precisa ser mais específico para sobrescrever) */
.header-nav a {
    color: <?php echo $config['cor_fonte_menu']; ?>;
}

body, p, .site-slogan, .author-role, .testimonial-text {
    color: <?php echo $config['cor_fonte_textos']; ?>;
}