<?php
// Inclui o arquivo de conexão
require_once 'admin/db.php';

// --- Validação e Busca do Veículo ---
if (!isset($_GET['id']) || empty($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: veiculos.php");
    exit();
}
$id_veiculo = (int)$_GET['id'];

// Seleciona todas as colunas necessárias
$stmt = $conexao->prepare("SELECT * FROM vitrine_veiculos WHERE id = ? AND status = 'disponivel'");
if (!$stmt) {
    die("Erro ao preparar a consulta de veículo: " . $conexao->error);
}
$stmt->bind_param("i", $id_veiculo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $stmt->close();
    header("Location: veiculos.php");
    exit();
}
$veiculo = $resultado->fetch_assoc();
$stmt->close();

// --- Busca do Logo Principal ---
$url_logo_principal = 'imagens/logo.png'; // Fallback
$resultado_logo = $conexao->query("SELECT valor FROM configuracoes WHERE chave = 'url_logo_principal'");
if ($resultado_logo) {
    $config_logo = $resultado_logo->fetch_assoc();
    $url_logo_principal = $config_logo['valor'] ?? 'imagens/logo.png';
    $resultado_logo->free();
} else {
    error_log("Erro ao buscar logo principal: " . $conexao->error);
}

// --- Busca do Endereço do Pátio ---
$endereco_patio = 'Localização não configurada'; // Fallback
$resultado_patio = $conexao->query("SELECT valor FROM configuracoes WHERE chave = 'endereco_origem'");
if ($resultado_patio) {
    $config_patio = $resultado_patio->fetch_assoc();
    $endereco_patio = $config_patio['valor'] ?? $endereco_patio;
    $resultado_patio->free();
} else {
    error_log("Erro ao buscar endereço do pátio: " . $conexao->error);
}

// --- Texto Padrão do Laudo ---
$texto_laudo_padrao = "O veículo passou por inspeção técnica completa, contemplando verificação estrutural, identificação veicular e análise documental. Foram avaliados os principais pontos de integridade física e mecânica, incluindo chassi, longarinas, travassas, colunas, painéis, suspensão, freios e sistemas de segurança, não sendo constatados sinais de colisão de grande monta, cortes, enxertos ou adulterações.\nTodos os números identificadores (chassi, motor e etiquetas de fábrica) foram conferidos e estão originais, sem indícios de remarcação ou irregularidades.\n\nO veículo encontra-se livre de restrições administrativas, sinistros de indenização integral ou histórico de leilão por perda total.\nO laudo foi aprovado com 100% de conformidade, garantindo a autenticidade, procedência e segurança para transferência e circulação.";

// --- Lista Fixa de Itens de Vistoria ---
$itens_vistoria_fixos = [
    'Funcionando' => 'Sim', 'Câmbio' => 'Regular', 'Direção' => 'Regular', 'Estepe' => 'Sim',
    'Motor' => 'Regular', 'Embreagem' => 'Regular', 'Freios' => 'Regular', 'Interior' => 'Regular',
    'Bateria' => 'Sim, com carga', 'Lataria/Pintura' => 'Regular',
    'Pneu dianteiro esquerdo' => 'Regular', 'Pneu dianteiro direito' => 'Regular',
    'Pneu traseiro esquerdo' => 'Regular', 'Pneu traseiro direito' => 'Regular'
];

// --- Processamento das Imagens ---
$foto_principal_path = $veiculo['foto_principal'] ?? '';
$fotos_galeria = [];
if (!empty($veiculo['fotos_galeria'])) {
    $fotos_galeria = array_filter(array_map('trim', explode("\n", trim($veiculo['fotos_galeria']))));
}
$galeria_completa = [];
$foto_principal_web = 'imagens/placeholder_large.png';
if (!empty($foto_principal_path) && file_exists($foto_principal_path)) {
   $galeria_completa[] = $foto_principal_path;
   $foto_principal_web = $foto_principal_path;
}
foreach ($fotos_galeria as $foto) {
    if (!empty($foto) && file_exists($foto)) {
        $galeria_completa[] = $foto;
    }
}
$galeria_completa = array_unique($galeria_completa);

$img_mosaic_1 = $galeria_completa[0] ?? 'imagens/placeholder_large.png';
$img_mosaic_2 = $galeria_completa[1] ?? $img_mosaic_1;
$img_mosaic_3 = $galeria_completa[2] ?? $img_mosaic_1;
$img_mosaic_4 = $galeria_completa[3] ?? $img_mosaic_2;
$img_principal_mobile = $img_mosaic_1;

// --- Preparar dados para Meta Tags e WhatsApp ---
$page_title = htmlspecialchars($veiculo['titulo'] . ' - Copart Leilões');
$page_description = htmlspecialchars($veiculo['subtitulo'] ?? ('Confira este veículo: ' . $veiculo['titulo']));
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain_name = $_SERVER['HTTP_HOST'];
$page_url = $protocol . $domain_name . $_SERVER['REQUEST_URI'];
$image_url = $protocol . $domain_name . '/' . str_replace('../', '', $foto_principal_web);

$whatsapp_message = urlencode("tenho interesse nesse " . $page_url);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>

    <meta property="og:title" content="<?php echo htmlspecialchars($veiculo['titulo']); ?>" />
    <meta property="og:description" content="<?php echo $page_description; ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo $page_url; ?>" />
    <meta property="og:image" content="<?php echo $image_url; ?>" />
    <meta property="og:image:alt" content="<?php echo htmlspecialchars($veiculo['titulo']); ?>" />
    <meta property="og:site_name" content="Copart Leilões" />

    <link rel="stylesheet" href="css/dynamic_style.php">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@400;700&display=swap');
        html { scroll-behavior: smooth; overflow-x: hidden; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: #F7F7F8; color: #2E323C; line-height: 1.6; font-size: 14px; overflow-x: hidden;}
        .main-container { max-width: 1280px; margin: 0 auto; padding: 0; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; font-weight: 600; margin: 0; color: #1E2126; }
        a { color: #FF123C; text-decoration: none; }
        a:hover { text-decoration: underline; }

        .header { background: linear-gradient(to right, #004080, #001f3f); padding: 15px 0; color: #ffffff; border-bottom: 3px solid #FFC107; position: relative; }
        .container-header { display: flex; align-items: center; justify-content: space-between; max-width: 1280px; margin: 0 auto; padding: 0 15px; }
        .logo-e-texto { display: flex; align-items: center; gap: 15px; }
        .logo-header-img { max-width: 110px; height: auto; }
        .header-text-content { display: none; }
        .site-title { font-size: 1.8em; font-weight: 700; margin: 0 0 3px 0; letter-spacing: 1px; color: #ffffff; }
        .site-slogan { font-size: 0.9em; margin: 0; opacity: 0.9; color: #ffffff; font-weight: 500; }
        .header-nav { display: none; }
        .header-nav ul { list-style: none; margin: 0; padding: 0; display: flex; align-items: center; gap: 25px; }
        .header-nav a { color: #ffffff; font-family: 'Poppins', sans-serif; font-weight: 500; font-size: 1rem; transition: opacity 0.3s ease; }
        .header-nav a:hover { opacity: 0.8; }
        .header-nav .btn-rastrear { background-color: #f39c12; color: #ffffff; padding: 10px 20px; border-radius: 5px; font-weight: 600; }
        .header-nav .btn-rastrear:hover { background-color: #e0a800; opacity: 1; text-decoration: none;}
        .hamburger-menu { display: flex; flex-direction: column; cursor: pointer; padding: 5px; z-index: 1001; }
        .hamburger-menu span { width: 25px; height: 3px; background-color: #ffffff; margin: 3px 0; transition: 0.3s; border-radius: 2px; }
        .hamburger-menu.active span:nth-child(1) { transform: rotate(-45deg) translate(-5px, 6px); }
        .hamburger-menu.active span:nth-child(2) { opacity: 0; }
        .hamburger-menu.active span:nth-child(3) { transform: rotate(45deg) translate(-5px, -6px); }
        .mobile-nav { position: fixed; top: 0; left: -300px; width: 300px; height: 100vh; background-color: rgba(0, 64, 128, 0.98); z-index: 1000; padding-top: 60px; transition: left 0.3s ease; visibility: hidden; opacity: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.2); }
        .mobile-nav.active { left: 0; visibility: visible; opacity: 1; }
        .mobile-nav ul { list-style: none; padding: 0; margin: 0; text-align: center; }
        .mobile-nav li { margin: 15px 0; }
        .mobile-nav a { color: #ffffff; text-decoration: none; font-size: 1.1em; font-family: 'Poppins', sans-serif; font-weight: 500; display: block; padding: 12px 20px; transition: background-color 0.3s ease; }
        .mobile-nav a:hover { background-color: rgba(255, 255, 255, 0.1); }
        .mobile-nav .btn-rastrear { background-color: #f39c12; margin: 20px auto; display: inline-block; border-radius: 8px; max-width: 200px; padding: 10px 20px; }
        @media (min-width: 960px) {
            .header-nav { display: block; }
            .hamburger-menu { display: none; }
            .mobile-nav { display: none; }
            .header-text-content { display: block; }
             .logo-e-texto { gap: 20px; }
        }
        @media (max-height: 500px) and (orientation: landscape) {
            .mobile-nav { padding-top: 50px; } .mobile-nav li { margin: 8px 0; } .mobile-nav a { padding: 8px 15px; font-size: 1em; }
        }

        .vehicle-gallery-desktop { display: none; }
        .vehicle-gallery-mobile { display: block; background-color: #fff; border-bottom: 1px solid #eaeef5;}
        @media (min-width: 960px) {
            .vehicle-gallery-desktop { display: grid; grid-template-columns: repeat(10, 1fr); grid-template-rows: repeat(2, 190px); gap: 2px; height: 382px; position: relative; background-color: #fff; margin-bottom: 0; }
            .mosaic-item { position: relative; overflow: hidden; background-color: #eee; }
            .mosaic-item img { display: block; width: 100%; height: 100%; object-fit: cover; }
            .mosaic-item-1 { grid-column: 1 / 6; grid-row: 1 / 3; }
            .mosaic-item-2 { grid-column: 6 / 9; grid-row: 1 / 3; }
            .mosaic-item-3 { grid-column: 9 / 11; grid-row: 1 / 2; }
            .mosaic-item-4 { grid-column: 9 / 11; grid-row: 2 / 3; }
            .btn-all-images { position: absolute; bottom: 24px; left: calc(50% - 60px); background-color: rgba(255, 255, 255, 0.9); color: #1E2126; border: none; border-radius: 8px; padding: 8px 16px; font-size: 0.85rem; font-weight: 600; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
            .btn-all-images:hover { background-color: #fff; }
            .vehicle-gallery-mobile { display: none; }
        }
        .gallery-main-image-mobile img { width: 100%; height: auto; aspect-ratio: 4 / 3; object-fit: cover; display: block; }
        .gallery-thumbnails-mobile { display: flex; overflow-x: auto; gap: 8px; padding: 10px; background-color: #fff; scrollbar-width: none; }
        .gallery-thumbnails-mobile::-webkit-scrollbar { display: none; }
        .gallery-thumbnails-mobile img { height: 60px; width: auto; border-radius: 4px; cursor: pointer; border: 2px solid transparent; opacity: 0.7; transition: opacity 0.3s, border-color 0.3s; }
        .gallery-thumbnails-mobile img.active, .gallery-thumbnails-mobile img:hover {
            opacity: 1;
            border-color: #0056b3;
        }

        .inspection-strip { background-color: #1E2126; color: #fff; padding: 10px 0; }
        .inspection-strip-content { max-width: 1280px; margin: 0 auto; padding: 0 15px; display: flex; align-items: center; }
        .inspection-strip-content img { width: 32px; height: auto; margin-right: 15px; }
        .inspection-strip-content h2 { font-size: 1.1rem; font-weight: 600; margin: 0; color: #fff; }
        .inspection-strip-content a { font-size: 0.75rem; font-weight: 300; color: #ccc; margin-left: 5px; }
        .inspection-strip-content a:hover { color: #fff; }

        .vehicle-details-body-grid { display: grid; grid-template-columns: 1fr; gap: 20px; padding: 24px 15px; max-width: 1280px; margin: 0 auto; }
        .vehicle-main-column { order: 0; }
        .vehicle-sidebar-column { order: -1; }

        @media (min-width: 960px) {
            .vehicle-details-body-grid { grid-template-columns: 1fr 384px; gap: 32px; padding: 40px 15px; }
            .vehicle-main-column { order: 0; }
            .vehicle-sidebar-column { order: 0; }
        }

        .vehicle-main-column section { margin-bottom: 32px; }
        .section-title { font-size: 1.5rem; font-weight: 600; color: #1E2126; margin-bottom: 24px; padding-bottom: 8px; border-bottom: 1px solid #eaeef5; }
        .info-card { background-color: #fff; border: 1px solid #eaeef5; border-radius: 8px; padding: 24px; }
        .vehicle-specs-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; }
        .spec-item p { margin: 0; font-size: 0.8rem; color: #79828D; }
        .spec-item span { display: block; font-size: 0.95rem; font-weight: 500; color: #1E2126; margin-top: 4px; }
        .features-grid, .inspection-items-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px 16px; }
        .feature-item, .inspection-item { display: flex; align-items: center; font-size: 0.9rem; }
        .feature-item svg { width: 16px; height: 16px; fill: #4C9317; margin-right: 8px; }
        .feature-item span, .inspection-item span { color: #1E2126; }
        .inspection-item strong { color: #555; margin-right: 5px; }
        .text-content p { font-size: 0.9rem; line-height: 1.7; color: #40474F; margin-bottom: 1em;}
        .text-content strong { color: #1E2126; }
        .approved-text { color: #4C9317; font-weight: 600; }
        .location-info { display: flex; align-items: center; gap: 8px; margin-bottom: 16px;}
        .location-info svg { width: 18px; height: 18px; fill: #FF5876; }
        .location-info p { margin: 0; font-size: 0.85rem; color: #40474F; }
        .location-info strong { font-weight: 600; color: #1E2126; }
        .map-placeholder { width: 100%; height: 250px; background-color: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.9rem; border: 1px dashed #ccc; }
        #laudoCautelar .info-card { border: 1px solid #4C9317; box-shadow: 0 2px 8px rgba(76, 147, 23, 0.1); }

        .vehicle-sidebar-column { position: relative; }
        .floating-card { background-color: #fff; border: 1px solid #eaeef5; border-radius: 8px; padding: 24px; }
        @media (min-width: 960px) { .floating-card { position: sticky; top: 20px; } }
        .floating-card h1 { font-size: 1.75rem; line-height: 1.3; font-weight: 600; color: #1E2126; margin-bottom: 4px; }
        .floating-card .subtitle { font-size: 0.9rem; color: #79828D; margin-bottom: 16px; font-weight: 400; }
        .price-main { font-size: 2rem; font-weight: 700; color: #1E2126; margin-bottom: 24px; }
        .price-comparison { margin-bottom: 24px; }
        .price-comparison h2 { font-size: 1.1rem; font-weight: 500; color: #1E2126; margin-bottom: 16px; padding-left: 8px; }
        .comparison-item { border-radius: 8px; background-color: #fff; padding: 15px; display: grid; grid-template-columns: auto 1fr; align-items: center; gap: 16px; margin-bottom: 12px; border: 1px solid #eaeef5; }
        .comparison-item.highlight { border: 2px solid #FF123C; }
        .comparison-logo img { display: block; max-height: 40px; width: auto; max-width: 80px; }
        .comparison-logo.loop img { max-height: 25px; }
        .comparison-texts p { margin: 0; font-size: 0.75rem; color: #79828D; line-height: 1.4;}
        .comparison-texts span { display: block; font-size: 1.25rem; font-weight: 700; color: #1E2126; margin-top: 4px; }
        .comparison-texts p.source-label { font-weight: 500; font-size: 0.8rem; margin-bottom: 4px; }
        .deadlines { font-size: 0.8rem; color: #40474F; background-color: #f1f5fc; padding: 12px 16px; border-radius: 4px; margin: 24px 0; line-height: 1.5; }
        
        .whatsapp-button {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: auto; /* <<<< MUDANÇA AQUI */
            max-width: 100%;
            padding: 12px 24px; /* <<<< MUDANÇA AQUI */
            margin: 0 auto; /* <<<< ADICIONADO AQUI */
            background-color: #25D366; color: #fff;
            text-align: center; text-decoration: none; font-size: 0.95rem;
            font-weight: 600;
            border-radius: 8px; transition: background-color 0.3s; border: none; cursor: pointer;
            white-space: nowrap;
        }
        .whatsapp-button svg { width: 18px; height: 18px; fill: currentColor; }
        .whatsapp-button:hover { background-color: #1DAE51; text-decoration: none; }

        .footer { background-color: #222222; color: #a9a9a9; padding: 50px 0 20px 0; font-size: 0.9em; margin-top: 40px;}
        .footer-container { max-width: 1140px; margin: 0 auto; padding: 0 15px; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 30px; }
        .footer-column { flex: 1; min-width: 150px; }
        .footer-column h4 { color: #ffffff; font-size: 1.1em; margin-bottom: 15px; font-weight: 500; }
        .footer-column ul { list-style: none; padding: 0; margin: 0; }
        .footer-column ul li { margin-bottom: 10px; }
        .footer-column a { color: #a9a9a9; text-decoration: none; transition: color 0.3s; font-size: 0.9em;}
        .footer-column a:hover { color: #ffffff; text-decoration: none;}
        .footer-logo-column { flex-basis: 100%; max-width: 200px; margin-bottom: 20px;}
        .footer-logo-column img { max-width: 130px; margin-bottom: 15px; }
        .selector-box { background-color: #333; border: 1px solid #444; border-radius: 4px; padding: 8px 12px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; color: #fff; cursor: pointer; max-width: 150px;}
        .selector-box .flag-icon { width: 20px; height: auto; margin-right: 8px; border-radius: 2px; }
        .sub-footer { border-top: 1px solid #444; margin-top: 30px; padding-top: 20px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; font-size: 0.8em; }
        .sub-footer-links { list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: 15px; }
        .sub-footer p { margin: 5px 0; }
        .back-to-top-btn { position: fixed; bottom: 20px; right: 20px; display: flex; align-items: center; gap: 5px; background-color: #f39c12; color: #fff; padding: 8px 12px; border-radius: 5px; text-decoration: none; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.2); opacity: 0; visibility: hidden; transition: opacity 0.4s, visibility 0.4s; z-index: 1000; font-size: 0.85em;}
        .back-to-top-btn.show { opacity: 1; visibility: visible; }
        .back-to-top-btn svg { width: 14px; height: 14px; fill: #fff; }
        .back-to-top-btn:hover { background-color: #e0a800; text-decoration: none; color: #fff; }

        @media (max-width: 959px) { .vehicle-specs-grid { grid-template-columns: repeat(2, 1fr); } .features-grid, .inspection-items-grid { grid-template-columns: 1fr; } .floating-card { margin-top: 0; } }
        @media (max-width: 768px) {
             .container-header { flex-wrap: wrap; justify-content: space-between; gap: 10px;}
             .header-nav { display: none; }
             .footer-container { flex-direction: column; text-align: center; }
             .footer-logo-column { max-width: none; }
             .selector-box { margin: 0 auto 15px auto; }
             .sub-footer { flex-direction: column; gap: 10px; }
             .sub-footer-links { justify-content: center; }
        }
        @media (max-width: 600px) {
            body { font-size: 13px; }
            .section-title { font-size: 1.3rem; margin-bottom: 16px; }
            .info-card { padding: 16px; }
            .floating-card { padding: 16px; }
            .floating-card h1 { font-size: 1.4rem; }
            .subtitle { font-size: 0.85rem; margin-bottom: 12px;}
            .price-main { font-size: 1.75rem; margin-bottom: 16px; }
            .price-comparison h2 { font-size: 1rem; margin-bottom: 12px;}
            .comparison-item { gap: 12px; padding: 12px;}
            .comparison-logo img { max-height: 30px; max-width: 60px;}
            .comparison-texts span { font-size: 1.1rem; }
            .whatsapp-button { font-size: 0.9rem; padding: 10px 16px; } /* <<<< MUDANÇA AQUI */
            .whatsapp-button svg { width: 16px; height: 16px; }
            .gallery-thumbnails-mobile img { height: 50px; }
        }
    </style>
</head>
<body>
     <header class="header">
        <div class="container-header">
             <div class="logo-e-texto">
                <a href="index.php">
                    <img src="<?php echo htmlspecialchars($url_logo_principal); ?>" alt="Copart Logo" class="logo-header-img">
                </a>
                <div class="header-text-content">
                    <h1 class="site-title">COPART LEILÕES</h1>
                    <p class="site-slogan">Veiculos e Motocicletas com as Melhores Condições.</p>
                </div>
            </div>
            <nav class="header-nav">
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="veiculos.php">Veículos</a></li>
                    <li><a href="index.php#depoimentos-video">Depoimentos</a></li>
                    <li><a href="funcionarios.php">Equipe</a></li>
                    <li><a href="rastrear.php" class="btn-rastrear">Rastrear</a></li>
                </ul>
            </nav>
            <div class="hamburger-menu" onclick="toggleMobileMenu()">
                <span></span><span></span><span></span>
            </div>
        </div>
        <nav class="mobile-nav" id="mobileNav">
            <ul>
                <li><a href="index.php" onclick="closeMobileMenu()">Início</a></li>
                <li><a href="veiculos.php" onclick="closeMobileMenu()">Veículos</a></li>
                <li><a href="index.php#depoimentos-video" onclick="closeMobileMenu()">Depoimentos</a></li>
                <li><a href="funcionarios.php" onclick="closeMobileMenu()">Equipe</a></li>
                <li><a href="rastrear.php" class="btn-rastrear" onclick="closeMobileMenu()">Rastrear</a></li>
            </ul>
        </nav>
    </header>

    <div class="main-container">

        <div class="vehicle-gallery-desktop">
            <div class="mosaic-item mosaic-item-1"><img src="<?php echo htmlspecialchars($img_mosaic_1); ?>" alt="<?php echo htmlspecialchars($veiculo['titulo']); ?> - Imagem 1"></div>
            <div class="mosaic-item mosaic-item-2"><img src="<?php echo htmlspecialchars($img_mosaic_2); ?>" alt="<?php echo htmlspecialchars($veiculo['titulo']); ?> - Imagem 2"></div>
            <div class="mosaic-item mosaic-item-3"><img src="<?php echo htmlspecialchars($img_mosaic_3); ?>" alt="<?php echo htmlspecialchars($veiculo['titulo']); ?> - Imagem 3"></div>
            <div class="mosaic-item mosaic-item-4"><img src="<?php echo htmlspecialchars($img_mosaic_4); ?>" alt="<?php echo htmlspecialchars($veiculo['titulo']); ?> - Imagem 4"></div>
            <button class="btn-all-images" onclick="alert('Funcionalidade de galeria completa indisponível no momento.');">+ Imagens</button>
        </div>

        <div class="vehicle-gallery-mobile">
             <div class="gallery-main-image-mobile">
                <img id="mainImageMobile" src="<?php echo htmlspecialchars($img_principal_mobile); ?>" alt="<?php echo htmlspecialchars($veiculo['titulo']); ?>">
             </div>
             <?php if (count($galeria_completa) > 1): ?>
                 <div class="gallery-thumbnails-mobile">
                     <?php foreach ($galeria_completa as $index => $foto_url): ?>
                         <img src="<?php echo htmlspecialchars($foto_url); ?>"
                              onclick="document.getElementById('mainImageMobile').src='<?php echo htmlspecialchars($foto_url); ?>'; setActiveThumb(this);"
                              class="<?php echo ($index === 0) ? 'active' : ''; ?>">
                     <?php endforeach; ?>
                 </div>
             <?php endif; ?>
        </div>

        <div class="inspection-strip">
            <div class="inspection-strip-content">
                <img src="imagens/selo-aprovado.svg" alt="Laudo Cautelar Aprovado">
                <h2>Cautelar Aprovada</h2>
                <a href="#laudoCautelar">(saiba mais)</a>
            </div>
        </div>

        <div class="vehicle-details-body-grid">

             <aside class="vehicle-sidebar-column">
                <div class="floating-card">
                     <h1><?php echo htmlspecialchars($veiculo['titulo']); ?></h1>
                     <p class="subtitle"><?php echo htmlspecialchars($veiculo['subtitulo'] ?? ''); ?></p>
                     <div class="price-main">R$ <?php echo number_format($veiculo['preco'] ?? 0, 0, ',', '.'); ?></div>

                    <div class="price-comparison">
                        <h2>Compare os valores</h2>
                        <div class="comparison-item highlight">
                            <div class="comparison-logo loop">
                                <img src="<?php echo htmlspecialchars($url_logo_principal); ?>" alt="Valor desta oferta">
                            </div>
                            <div class="comparison-texts">
                                <p class="source-label">Valor desta oferta</p>
                                <span>R$ <?php echo number_format($veiculo['preco'] ?? 0, 0, ',', '.'); ?></span>
                            </div>
                        </div>
                        <div class="comparison-item">
                            <div class="comparison-logo">
                                <img src="imagens/webmotors-logo.png" alt="Média Webmotors">
                            </div>
                            <div class="comparison-texts">
                                <p class="source-label">Média Webmotors</p>
                                <span>R$ --.---</span>
                                <p>Valor médio de veículos iguais a este anunciados na Webmotors</p>
                            </div>
                        </div>
                        <div class="comparison-item">
                            <div class="comparison-logo">
                                <img src="imagens/fipe-logo.png" alt="Fipe">
                            </div>
                            <div class="comparison-texts">
                                <p class="source-label">Fipe</p>
                                <span>R$ --.---</span>
                                <p>Valor deste veículo na tabela Fipe</p>
                            </div>
                        </div>
                    </div>

                    <a href="whatsapp://send?text=<?php echo $whatsapp_message; ?>" class="whatsapp-button" data-action="share/whatsapp/share" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24"><path d="M16.75,13.96C17,14.09 17.16,14.17 17.21,14.26C17.27,14.36 17.25,14.45 17.25,14.54C17.25,14.63 17.27,14.72 17.33,14.81C17.39,14.91 17.47,14.97 17.53,15.06C17.6,15.15 17.68,15.26 17.7,15.35C17.72,15.45 17.72,15.54 17.7,15.63C17.68,15.72 17.64,15.84 17.58,15.93C17.52,16.02 17.45,16.1 17.38,16.19C17.13,16.44 16.83,16.63 16.48,16.77C16.12,16.91 15.54,17 15,17C14.66,17 14.33,16.96 14,16.89C13.67,16.81 13.28,16.68 12.87,16.5C12.46,16.32 11.98,16.05 11.45,15.7C10.92,15.36 10.4,14.94 9.93,14.47C9.46,14 9.04,13.48 8.7,12.95C8.35,12.42 8.08,11.94 7.9,11.45C7.72,10.96 7.59,10.57 7.5,10.24C7.43,9.91 7.39,9.58 7.39,9.25C7.39,8.78 7.49,8.34 7.66,7.97C7.83,7.6 8.04,7.31 8.28,7.09C8.38,7 8.48,6.93 8.58,6.87C8.68,6.81 8.78,6.78 8.87,6.78C8.97,6.78 9.06,6.8 9.15,6.83C9.25,6.86 9.35,6.91 9.44,6.99C9.53,7.07 9.61,7.15 9.68,7.25C9.75,7.35 9.8,7.45 9.83,7.55C9.86,7.65 9.88,7.75 9.88,7.84C9.88,7.94 9.86,8.04 9.8,8.13C9.74,8.23 9.68,8.32 9.6,8.41C9.53,8.51 9.44,8.59 9.36,8.68C9.28,8.76 9.21,8.83 9.16,8.89C9.1,8.95 9.07,9 9.07,9.04C9.06,9.07 9.06,9.1 9.07,9.14C9.09,9.18 9.11,9.22 9.15,9.28C9.19,9.34 9.23,9.4 9.28,9.47C9.43,9.69 9.62,9.93 9.84,10.19C10.07,10.45 10.3,10.68 10.54,10.89C10.74,11.08 10.95,11.26 11.19,11.43C11.42,11.6 11.66,11.76 11.89,11.89C11.95,11.93 12.01,11.97 12.06,12C12.11,12.03 12.16,12.05 12.2,12.06C12.24,12.06 12.28,12.06 12.31,12.04C12.35,12.02 12.39,12 12.42,11.96C12.46,11.93 12.5,11.89 12.55,11.85C12.63,11.77 12.71,11.69 12.79,11.61C12.87,11.53 12.95,11.46 13.04,11.41C13.13,11.36 13.22,11.31 13.33,11.27C13.43,11.24 13.54,11.22 13.64,11.22C13.74,11.22 13.84,11.24 13.95,11.3C14.05,11.36 14.15,11.42 14.24,11.5C14.34,11.58 14.43,11.67 14.51,11.78C14.59,11.88 14.65,11.98 14.7,12.09C14.75,12.19 14.79,12.3 14.81,12.4C14.83,12.51 14.83,12.61 14.81,12.71C14.79,12.81 14.75,12.93 14.68,13.04C14.61,13.15 14.53,13.24 14.44,13.33C14.19,13.57 13.91,13.75 13.62,13.87L13.96,16.75C14,16.9 14.11,17 14.25,17H15C15.54,17 16,16.72 16.22,16.27L16.75,15.17C16.83,15 16.89,14.83 16.93,14.67C16.97,14.51 16.97,14.34 16.95,14.18C16.93,14.02 16.86,13.9 16.75,13.96M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22C13.81,22 15.5,21.5 16.97,20.66L19.47,21.96L19.96,19.47C21.34,17.8 22,15.82 22,13.64C22,12.69 21.86,11.78 21.61,10.92L19.41,13.12C19.56,13.28 19.66,13.45 19.7,13.64C19.7,13.78 19.65,13.91 19.56,14C19.47,14.1 19.34,14.15 19.2,14.15C18.83,14.15 18.5,14.04 18.17,13.82C17.85,13.6 17.58,13.3 17.37,12.97C17.16,12.64 17,12.27 17,11.86C17,11.46 17.11,11.09 17.33,10.77C17.55,10.45 17.85,10.19 18.18,10C18.52,9.81 18.89,9.7 19.29,9.7C19.41,9.7 19.53,9.72 19.64,9.77C19.75,9.82 19.85,9.88 19.93,9.97C20,10.05 20.06,10.15 20.09,10.25C20.12,10.35 20.12,10.46 20.1,10.56C19.95,11.13 19.67,11.63 19.28,12.07L16.4,9.18C15.82,8.6 15,8.24 14.14,8.11C14,8.09 13.86,8.08 13.72,8.08C13.16,8.08 12.63,8.25 12.11,8.6L10.36,10.36C10.13,10.61 10.06,10.96 10.16,11.27C10.26,11.58 10.5,11.82 10.82,11.92C11.14,12.03 11.48,11.96 11.73,11.73L12.7,10.76C12.87,10.59 13.13,10.5 13.38,10.5C13.64,10.5 13.9,10.59 14.07,10.76L14.73,11.43C14.9,11.6 15,11.86 15,12.12C15,12.38 14.9,12.64 14.73,12.81L12.8,14.73C12.6,14.93 12.3,15 12,15C11.7,15 11.4,14.93 11.2,14.73C10.76,14.29 10.39,13.78 10.11,13.2C9.84,12.63 9.7,11.97 9.7,11.23C9.7,10.16 10.05,9.21 10.73,8.39L12.61,6.5C13.06,6.05 13.62,5.83 14.28,5.83C14.94,5.83 15.5,6.05 15.95,6.5L18.84,9.39C19.39,9.94 19.67,10.6 19.67,11.36C19.67,12.12 19.39,12.78 18.84,13.33C18.29,13.88 17.63,14.15 16.86,14.15H16.72C16.14,14.15 15.63,14 15.18,13.7C14.73,13.4 14.39,13.03 14.14,12.6L11.89,10.36C11.7,10.17 11.44,10.08 11.18,10.08C10.92,10.08 10.66,10.17 10.47,10.36L8.71,12.11C8.28,12.55 8.06,13.11 8.06,13.77C8.06,14.43 8.28,14.99 8.71,15.43C9.15,15.87 9.71,16.09 10.36,16.09C11.02,16.09 11.58,15.87 12.03,15.43L13.13,14.33C13.2,14.26 13.28,14.21 13.36,14.17C13.44,14.13 13.53,14.11 13.62,14.11H13.77L14.07,17.45C13.4,17.8 12.71,18 12,18H11.97L11.96,18C10.78,18 9.69,17.73 8.71,17.2C7.73,16.67 6.89,15.96 6.19,15.1C5.5,14.23 4.96,13.26 4.58,12.19C4.2,11.12 4,9.97 4,8.79C4,7.61 4.2,6.5 4.58,5.47C4.96,4.44 5.5,3.5 6.19,2.64C6.89,1.78 7.73,1.07 8.71,0.54C9.69,0.01 10.78,0 11.96,0H12C14.19,0 16.17,0.65 17.82,1.94L18.06,2.18L17.8,4.72C17.64,5.18 17.29,5.54 16.84,5.66C15.89,5.91 15.06,6.34 14.34,6.94L12.47,5.06C12.31,4.9 12.06,4.8 11.79,4.8C11.53,4.8 11.27,4.9 11.11,5.06C10.95,5.22 10.86,5.43 10.86,5.69C10.86,5.95 10.95,6.16 11.11,6.31L13.12,8.33C12.87,8.5 12.65,8.64 12.47,8.74C12.28,8.85 12.12,8.91 12,8.91C11.54,8.91 11.13,8.79 10.77,8.54C10.41,8.3 10.11,7.97 9.89,7.56C9.67,7.15 9.56,6.7 9.56,6.19C9.56,5.82 9.61,5.47 9.72,5.15C9.83,4.83 9.99,4.54 10.21,4.28C10.43,4.02 10.68,3.81 10.97,3.65C11.26,3.48 11.58,3.4 11.94,3.4H12C12.28,3.4 12.53,3.44 12.77,3.54C13,3.63 13.22,3.75 13.41,3.91C13.6,4.06 13.77,4.24 13.91,4.44C14.04,4.64 14.14,4.86 14.2,5.1L16.39,2.9C15.19,2.33 13.81,2 12.28,2H12Z"></path></svg>
                        Compartilhar no WhatsApp
                    </a>

                    <div class="deadlines">
                        <strong>Serviço de transferência:</strong> A responsabilidade pela transferência e documentação é do comprador.<br>
                        <strong>Retirada do veículo:</strong> Liberada para agendamento após aprovação do pagamento.<br>
                        <strong>Prazo de retirada:</strong> em 1 dia útil após confirmação do pagamento integral do valor da taxa de transfência e do veiculo.<br>
                        <strong>Prazo de documentação:</strong> até 2 dias úteis.
                    </div>
                </div>
            </aside>

            <div class="vehicle-main-column">

                <section>
                     <h2 class="section-title">Dados do Veículo</h2>
                     <div class="info-card">
                        <div class="vehicle-specs-grid">
                            <div class="spec-item"><p>Ano</p><span><?php echo htmlspecialchars($veiculo['ano_fabricacao'] ?? 'N/I') . '/' . htmlspecialchars($veiculo['ano_modelo'] ?? 'N/I'); ?></span></div>
                            <div class="spec-item"><p>KM</p><span><?php echo number_format($veiculo['quilometragem'] ?? 0, 0, '', '.'); ?></span></div>
                            <div class="spec-item"><p>Câmbio</p><span><?php echo htmlspecialchars($veiculo['cambio'] ?? 'Não informado'); ?></span></div>
                            <div class="spec-item"><p>Combustível</p><span><?php echo htmlspecialchars($veiculo['combustivel'] ?? 'Não informado'); ?></span></div>
                            <div class="spec-item"><p>Cor</p><span><?php echo htmlspecialchars($veiculo['cor'] ?? 'Não informada'); ?></span></div>
                            <div class="spec-item"><p>Placa</p><span>*******</span></div>
                        </div>
                     </div>
                </section>

                 <?php
                    $opcionais = [];
                    if (!empty($veiculo['opcionais'])) {
                        $opcionais = array_filter(array_map('trim', explode(',', trim($veiculo['opcionais']))));
                    }
                ?>
                <?php if (!empty($opcionais)): ?>
                    <section>
                        <h2 class="section-title">Opcionais</h2>
                         <div class="info-card">
                            <div class="features-grid">
                                <?php foreach ($opcionais as $opcional): ?>
                                    <div class="feature-item">
                                        <svg viewBox="0 0 10 10"><path d="M661.246,391.079h0a.536.536,0,0,0-.726,0l-3.492,3.493-.964-.963a.53.53,0,0,0-.371-.144.523.523,0,0,0-.364.89l1.329,1.329a.523.523,0,0,0,.369.151.536.536,0,0,0,.372-.152l3.864-3.866a.506.506,0,0,0,.145-.373A.524.524,0,0,0,661.246,391.079Z" transform="translate(-655.881 -390.923)"></path></svg>
                                        <span><?php echo htmlspecialchars($opcional); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                         </div>
                    </section>
                <?php endif; ?>

                <section>
                    <h2 class="section-title">Itens de Vistoria</h2>
                    <div class="info-card">
                        <div class="inspection-items-grid">
                            <?php foreach ($itens_vistoria_fixos as $item => $status): ?>
                                <div class="inspection-item">
                                    <strong><?php echo htmlspecialchars($item); ?>:</strong>
                                    <span><?php echo htmlspecialchars($status); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>

                 <?php if (!empty($veiculo['descricao'])): ?>
                    <section>
                        <h2 class="section-title">Observações Técnicas</h2>
                        <div class="info-card text-content">
                            <p><?php echo nl2br(htmlspecialchars($veiculo['descricao'])); ?></p>
                        </div>
                    </section>
                <?php endif; ?>

                <section id="laudoCautelar">
                    <h2 class="section-title">Laudo Cautelar</h2>
                    <div class="info-card text-content">
                        <p>Status: <strong class="approved-text">Aprovado</strong></p>
                        <p><strong>Resumo do laudo:</strong></p>
                        <p><?php echo nl2br(htmlspecialchars($texto_laudo_padrao)); ?></p>
                    </div>
                </section>

                <section>
                    <h2 class="section-title">Localização</h2>
                    <div class="info-card">
                         <div class="location-info">
                             <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"></path></svg>
                             <p>Localização do veículo: <strong><?php echo htmlspecialchars($endereco_patio); ?></strong></p>
                         </div>
                         <div class="map-placeholder">
                             <span>Mapa da localização indisponível no momento.</span>
                         </div>
                    </div>
                </section>

            </div>

        </div>
    </div>

     <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo-column">
                <img src="<?php echo htmlspecialchars($url_logo_principal); ?>" alt="Copart Logo">
                <div class="selector-box">
                    <div style="display: flex; align-items: center;">
                        <img src="imagens/bandeira-brasil.png" alt="Brasil" class="flag-icon">
                        <span>Brasil</span>
                    </div>
                    <span>▼</span>
                </div>
            </div>
            <div class="footer-column">
                <h4>Empresa</h4>
                <ul><li><a href="#">Sobre Nós</a></li><li><a href="#">Nossa História</a></li><li><a href="#">Carreiras</a></li><li><a href="#">Imprensa</a></li></ul>
            </div>
            <div class="footer-column">
                <h4>Serviços</h4>
                <ul><li><a href="#">Leilões de Carros</a></li><li><a href="#">Leilões de Motos</a></li><li><a href="rastrear.php">Rastreamento</a></li><li><a href="#">Suporte</a></li></ul>
            </div>
            <div class="footer-column">
                <h4>Suporte</h4>
                <ul><li><a href="#">Central de Ajuda</a></li><li><a href="#">Contato</a></li><li><a href="#">FAQ</a></li><li><a href="#">Termos de Uso</a></li></ul>
            </div>
            <div class="footer-column">
                <h4>Legal</h4>
                <ul><li><a href="#">Política de Privacidade</a></li><li><a href="#">Termos e Condições</a></li><li><a href="#">Cookies</a></li><li><a href="#">Compliance</a></li></ul>
            </div>
        </div>
        <div class="sub-footer">
             <div style="max-width: 1140px; margin: 0 auto; padding: 0 15px; width: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <p>&copy; <span id="currentYearFooter"></span> Copart Leilões. Todos os direitos reservados.</p>
                    <ul class="sub-footer-links">
                        <li><a href="#">Privacidade</a></li><li><a href="#">Termos</a></li><li><a href="#">Cookies</a></li><li><a href="#">Acessibilidade</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <a href="#" class="back-to-top-btn" id="backToTopBtn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
        Topo
    </a>

    <script>
        // --- Funções do Menu Mobile ---
        function toggleMobileMenu() {
            const hamburger = document.querySelector('.hamburger-menu');
            const mobileNav = document.getElementById('mobileNav');
            hamburger.classList.toggle('active');
            mobileNav.classList.toggle('active');
            document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
        }
        function closeMobileMenu() {
            const hamburger = document.querySelector('.hamburger-menu');
            const mobileNav = document.getElementById('mobileNav');
            if (hamburger) hamburger.classList.remove('active');
            if (mobileNav) mobileNav.classList.remove('active');
            document.body.style.overflow = '';
        }

        // --- Funções da Galeria Mobile ---
        function setActiveThumb(thumbElement) {
             let thumbnails = document.querySelectorAll('.gallery-thumbnails-mobile img');
             thumbnails.forEach(thumb => thumb.classList.remove('active'));
             if (thumbElement) {
                 thumbElement.classList.add('active');
             }
        }
        function changeImage(newSrc, thumbElement) {
            document.getElementById('mainImageMobile').src = newSrc;
            setActiveThumb(thumbElement);
        }

         // --- Funções do Botão Voltar ao Topo ---
        const backToTopBtn = document.getElementById('backToTopBtn');
        window.addEventListener('scroll', function() {
            if (backToTopBtn) {
                if (window.pageYOffset > 300) { backToTopBtn.classList.add('show'); }
                else { backToTopBtn.classList.remove('show'); }
            }
        });
         if (backToTopBtn) {
            backToTopBtn.addEventListener('click', function(e) {
                e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' });
            });
         }

        // --- Event Listeners ---
        document.addEventListener('DOMContentLoaded', function() {
            // Ano atual no rodapé
            const currentYearSpan = document.getElementById('currentYearFooter');
            if (currentYearSpan) { currentYearSpan.textContent = new Date().getFullYear(); }
             // Ativa a primeira miniatura mobile
             const firstThumbMobile = document.querySelector('.gallery-thumbnails-mobile img');
             if (firstThumbMobile) { firstThumbMobile.classList.add('active'); }

             // Fecha menu mobile ao clicar fora
            document.addEventListener('click', function(event) {
                const hamburger = document.querySelector('.hamburger-menu');
                const mobileNav = document.getElementById('mobileNav');
                if (mobileNav && mobileNav.classList.contains('active') && hamburger && !hamburger.contains(event.target) && !mobileNav.contains(event.target)) {
                    closeMobileMenu();
                }
            });
             // Fecha menu mobile ao redimensionar para desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 959) { closeMobileMenu(); }
            });
         });
    </script>
</body>
</html>