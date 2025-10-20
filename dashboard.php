<?php
session_start();
require_once 'admin/db.php';

// Proteção da página
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header('Location: rastrear.php');
    exit();
}

$id_usuario_logado = $_SESSION['user_id'];
$nome_usuario = $_SESSION['user_name']; // Nome do login (sessão)

// --- BUSCA DADOS DO CLIENTE "AO VIVO" DO BANCO ---
$stmt_cliente = $conexao->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt_cliente->bind_param("i", $id_usuario_logado);
$stmt_cliente->execute();
$cliente_data = $stmt_cliente->get_result()->fetch_assoc();
$stmt_cliente->close();

// --- BUSCA DADOS DO MOTORISTA ---
$motorista_data = $conexao->query("SELECT * FROM motoristas LIMIT 1")->fetch_assoc();

// --- BUSCA TODOS OS VEÍCULOS E SEUS RASTREAMENTOS ---
$sql_veiculos = "
    SELECT 
        v.id, v.modelo, v.placa, v.icone_url,
        r.status, r.latitude, r.longitude
    FROM veiculos v
    LEFT JOIN rastreamentos r ON v.id = r.id_veiculo
    WHERE v.id_usuario = ?";
$stmt_veiculos = $conexao->prepare($sql_veiculos);
$stmt_veiculos->bind_param("i", $id_usuario_logado);
$stmt_veiculos->execute();
$veiculos_result = $stmt_veiculos->get_result();
$veiculos_cliente = [];
while($row = $veiculos_result->fetch_assoc()) {
    $veiculos_cliente[] = $row;
}
$stmt_veiculos->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Copart</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="css/dashboard_style.css">
    <style>
        /* Ajuste de alinhamento na página "Entregador" para Desktop */
        @media screen and (min-width: 769px) {
            .info-photo {
                display: flex;
                align-items: center;
                justify-content: flex-start;
                gap: 25px;
                text-align: left;
            }
            .info-photo .status-badge {
                margin-top: 0;
                transform: translateY(-15px);
            }
        }

        /* Animação de rotação para o loading */
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        .loading-spinner {
            animation: spin 1.5s linear infinite;
        }

        /* ===== ESTILOS MOBILE RESPONSIVOS INTEGRADOS ===== */
        html, body { overflow-x: hidden; }
        .live-indicator {
            width: 12px; height: 12px; background-color: #ff0000; border-radius: 50%;
            margin-right: 8px; animation: pulse 1.5s infinite ease-in-out;
            box-shadow: 0 0 5px rgba(255, 0, 0, 0.7);
        }
        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; box-shadow: 0 0 15px rgba(255, 0, 0, 1); }
            100% { transform: scale(0.8); opacity: 0.7; }
        }
        .mobile-header {
            display: none; position: fixed; top: 0; left: 0; right: 0; height: 60px;
            background: linear-gradient(to right, #004080, #001f3f);
            color: white; align-items: center; justify-content: center; padding: 0 20px;
            z-index: 1001; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: relative;
        }
        .mobile-menu-toggle {
            background: none; border: none; color: white; font-size: 24px; cursor: pointer;
            padding: 8px; border-radius: 4px; transition: background-color 0.3s ease;
            position: absolute; left: 20px;
        }
        .mobile-menu-toggle:hover { background-color: rgba(255,255,255,0.1); }
        .mobile-header-center { display: flex; align-items: center; }
        .mobile-header-title { font-size: 18px; font-weight: 600; text-align: center; }
        .mobile-rastreio-layout {
            display: none; flex-direction: column;
            height: calc(100vh - 130px); margin-top: 60px;
        }
        .mobile-map-container { height: 45%; width: 100%; position: relative; flex-shrink: 0; }
        #mobile-map { height: 100%; width: 100%; }
        .mobile-info-container {
            flex: 1; padding: 15px; background-color: var(--content-bg);
            display: flex; flex-direction: column; gap: 12px; overflow-y: auto;
        }
        .mobile-vehicle-card {
            background: white; border-radius: 12px; padding: 16px; display: flex;
            align-items: center; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 2px solid transparent; transition: all 0.3s ease;
        }
        .mobile-vehicle-card.active { border-color: var(--active-link-bg); box-shadow: 0 4px 12px rgba(243, 156, 18, 0.2); }
        .mobile-vehicle-icon {
            width: 50px; height: 50px; border-radius: 8px; overflow: hidden;
            background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;
        }
        .mobile-vehicle-icon img { width: 100%; height: 100%; object-fit: cover; }
        .mobile-vehicle-info { flex: 1; }
        .mobile-vehicle-info h4 { margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: var(--text-primary); }
        .mobile-vehicle-info .status-text { margin: 0; font-size: 12px; color: var(--text-secondary); font-weight: 500; }
        .mobile-progress-widget { background: white; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .mobile-progress-widget p { margin: 0 0 12px 0; font-size: 14px; font-weight: 600; color: var(--text-primary); text-align: center; }
        .mobile-progress-bar-container { position: relative; background-color: #e9ecef; border-radius: 20px; height: 24px; overflow: hidden; }
        .mobile-progress-bar {
            height: 100%; background-color: var(--progress-color);
            background-image: repeating-linear-gradient(-45deg, rgba(255,255,255,0.15), rgba(255,255,255,0.15) 25px, transparent 25px, transparent 50px);
            background-size: 50px 50px; border-radius: 20px; transition: width 0.8s ease; animation: animate-stripes 1.5s linear infinite;
        }
        .mobile-progress-bar-container span {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            font-size: 12px; font-weight: 600; color: white; text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .mobile-alert-widget {
            background: white; border-radius: 12px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #ffc107;
        }
        .mobile-alert-widget.flashing { animation: flash 1.5s infinite; border-left-color: var(--alert-color); }
        .mobile-alert-widget p { margin: 0 0 8px 0; font-size: 14px; font-weight: 600; color: var(--text-primary); text-align: center; }
        .mobile-alert-content { display: flex; align-items: center; gap: 8px; justify-content: center; }
        .mobile-alert-icon {
            font-size: 18px; color: var(--alert-color); background-color: #ff3d7120;
            border-radius: 50%; padding: 8px; display: flex; align-items: center; justify-content: center;
        }
        .mobile-alert-content p { margin: 0; font-size: 13px; color: var(--text-secondary); }
        .mobile-bottom-nav {
            display: none; position: fixed; bottom: 0; left: 0; right: 0; height: 70px;
            background: white; justify-content: space-around; align-items: center;
            z-index: 1000; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); border-top: 1px solid var(--border-color);
        }
        .mobile-nav-item {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-decoration: none; color: var(--text-secondary); font-size: 12px; font-weight: 500;
            padding: 8px 12px; border-radius: 8px; transition: all 0.3s ease; min-width: 60px;
        }
        .mobile-nav-item:hover { color: var(--active-link-bg); background-color: #f8f9fa; }
        .mobile-nav-item.active { color: var(--active-link-bg); background-color: rgba(243, 156, 18, 0.1); }
        .mobile-nav-item svg { width: 24px; height: 24px; margin-bottom: 4px; fill: currentColor; }
        .mobile-nav-item span { font-size: 11px; text-align: center; }
        .mobile-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5); z-index: 999; opacity: 0; transition: opacity 0.3s ease;
        }
        .mobile-overlay.active { display: block; opacity: 1; }
        @media screen and (max-width: 768px) {
            .content-header, .rastreio-layout { display: none; }
            .mobile-header, .mobile-rastreio-layout, .mobile-bottom-nav { display: flex; }
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; z-index: 1000; }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 0; }
            .page { padding: 0; margin-top: 60px; margin-bottom: 70px; }
            .info-card { margin: 15px; }
            .info-grid { flex-direction: column; gap: 20px; }
            .info-photo { order: -1; text-align: center; }
            .info-photo img { width: 100px; height: 100px; }
        }
        @media screen and (max-width: 480px) {
            .mobile-header { padding: 0 15px; }
            .mobile-header-title { font-size: 16px; }
            .mobile-info-container { padding: 12px; gap: 10px; }
            .mobile-vehicle-card, .mobile-progress-widget, .mobile-alert-widget { padding: 12px; }
            .mobile-bottom-nav { height: 65px; }
            .mobile-nav-item { padding: 6px 8px; min-width: 50px; }
            .mobile-nav-item svg { width: 20px; height: 20px; }
            .mobile-nav-item span { font-size: 10px; }
        }
        .mobile-header, .main-content, .mobile-bottom-nav {
            transition: transform 0.3s ease-in-out;
        }
        body.sidebar-is-open .mobile-header,
        body.sidebar-is-open .main-content,
        body.sidebar-is-open .mobile-bottom-nav {
            transform: translateX(250px);
        }
    </style>
</head>
<body>
    <header class="mobile-header">
        <button class="mobile-menu-toggle" onclick="toggleMobileSidebar()">☰</button>
        <div class="mobile-header-center">
            <?php if (!empty($veiculos_cliente)): ?>
                <div class="live-indicator"></div>
            <?php endif; ?>
            <span class="mobile-header-title">RASTREAMENTO AO VIVO</span>
        </div>
    </header>

    <div class="mobile-overlay" id="mobile-overlay" onclick="toggleMobileSidebar()"></div>

    <div class="dashboard-container">
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header"><img src="imagens/logo.png" alt="Copart Logo"></div>
            <ul class="sidebar-nav">
                <li class="nav-item" id="nav-dashboard"><a href="#" onclick="alert('Página em construção')"><svg viewBox="0 0 24 24"><path d="M13,3V9H21V3M13,21H21V11H13M3,21H11V15H3M3,13H11V3H3V13Z"></path></svg><span>Dashboard</span></a></li>
                <li class="nav-item" id="nav-entregador"><a href="#" onclick="showPage('entregador')"><svg viewBox="0 0 24 24"><path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,6A3,3 0 0,1 15,9A3,3 0 0,1 12,12A3,3 0 0,1 9,9A3,3 0 0,1 12,6M12,14C16,14 18.2,15.74 18.5,17.29L16.5,17.93C16.27,16.94 14.5,16 12,16C9.5,16 7.73,16.94 7.5,17.93L5.5,17.29C5.8,15.74 8,14 12,14Z"></path></svg><span>Entregador</span></a></li>
                <li class="nav-item active" id="nav-rastreio"><a href="#" onclick="showPage('rastreio')"><svg viewBox="0 0 24 24"><path d="M12,2C15.86,2 19,5.13 19,9C19,14.25 12,22 12,22C12,22 5,14.25 5,9C5,5.13 8.13,2 12,2M12,11.5A2.5,2.5 0 0,0 14.5,9A2.5,2.5 0 0,0 12,6.5A2.5,2.5 0 0,0 9.5,9A2.5,2.5 0 0,0 12,11.5Z"></path></svg><span>Rastreio</span></a></li>
                <li class="nav-item" id="nav-perfil"><a href="#" onclick="showPage('perfil')"><svg viewBox="0 0 24 24"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"></path></svg><span>Meu Perfil</span></a></li>
                <li class="nav-item" id="nav-ajuda"><a href="#" onclick="alert('Página em construção')"><svg viewBox="0 0 24 24"><path d="M12,2C6.47,2 2,6.47 2,12C2,17.53 6.47,22 12,22A10,10 0 0,0 22,12C22,6.47 17.53,2 12,2M13,19H11V17H13M13,15H11V7H13V15Z"></path></svg><span>Ajuda</span></a></li>
            </ul>
            <ul class="sidebar-nav nav-footer"><li class="nav-item"><a href="logout_cliente.php"><svg viewBox="0 0 24 24"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"></path></svg><span>Sair</span></a></li></ul>
        </nav>

        <main class="main-content">
            <header class="content-header">
                <div class="breadcrumb">DASHBOARD / <span id="breadcrumb-page">RASTREIO</span></div>
                <div class="user-info"><?php echo htmlspecialchars($cliente_data['nome'] ?? $nome_usuario); ?></div>
            </header>

            <div id="page-rastreio" class="page active">
                <div class="rastreio-layout">
                    <aside class="rastreio-sidebar">
                        <div class="rastreio-header"><h3>RASTREAMENTO</h3><p>Controle da sua entrega</p></div>
                        <div class="tabs"><div class="tab active">VEÍCULOS</div></div>

                        <?php if (empty($veiculos_cliente)): ?>
                            <p style="padding: 20px 0;">Nenhum veículo encontrado.</p>
                        <?php else: ?>
                            <?php foreach ($veiculos_cliente as $index => $veiculo): ?>
                                <div class="vehicle-card <?php echo $index === 0 ? 'active' : ''; ?>" 
                                     onclick="updateMapInfo(this)"
                                     data-lat="<?php echo $veiculo['latitude']; ?>"
                                     data-lng="<?php echo $veiculo['longitude']; ?>">
                                    <div class="vehicle-icon">
                                        <img src="<?php echo htmlspecialchars($veiculo['icone_url'] ?? 'imagens/logos/default.png'); ?>" alt="Logo">
                                    </div>
                                    <div class="vehicle-info">
                                        <h4><?php echo htmlspecialchars($veiculo['modelo']); ?></h4>
                                        <p class="status-text">STATUS: <?php echo htmlspecialchars($veiculo['status']); ?></p>
                                    </div>
                                    <div class="arrow-icon">&gt;</div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </aside>
                    <section class="rastreio-main">
                        <div class="top-widgets">
                            <?php if (empty($veiculos_cliente)): ?>
                                <div class="widget progress-widget">
                                    <p>LIBERAÇÃO</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" style="width: 0%; animation: none;"></div>
                                        <span>0%</span>
                                    </div>
                                </div>
                                <div class="widget alert-widget">
                                    <p>ALERTA</p>
                                    <div class="alert-content">
                                        <div class="alert-icon">🔔</div>
                                        <p>Nenhum veículo cadastrado.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="widget progress-widget">
                                    <p>LIBERAÇÃO</p>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar"></div>
                                        <span>90%</span>
                                    </div>
                                </div>
                                <div class="widget alert-widget flashing">
                                    <p>ALERTA</p>
                                    <div class="alert-content">
                                        <div class="alert-icon">🔔</div>
                                        <p>AGUARDANDO LIBERAÇÃO!</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div id="map"></div>
                    </section>
                </div>

                <div class="mobile-rastreio-layout">
                    <div class="mobile-map-container">
                        <div id="mobile-map"></div>
                    </div>
                    <div class="mobile-info-container">
                        
                        <?php if (!empty($veiculos_cliente)): ?>
                            <div class="mobile-alert-widget flashing">
                                <p>ALERTA</p>
                                <div class="mobile-alert-content">
                                    <div class="mobile-alert-icon">🔔</div>
                                    <p>AGUARDANDO LIBERAÇÃO!</p>
                                </div>
                            </div>
                        
                            <div class="mobile-progress-widget">
                                <p>LIBERAÇÃO</p>
                                <div class="mobile-progress-bar-container">
                                    <div class="mobile-progress-bar" style="width: 90%;"></div>
                                    <span>90%</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($veiculos_cliente)): ?>
                            <div class="mobile-vehicle-card">
                                <div class="mobile-vehicle-icon">
                                    <img src="imagens/Loading.png" alt="Carregando" class="loading-spinner">
                                </div>
                                <div class="mobile-vehicle-info">
                                    <h4>Procurando veículos...</h4>
                                    <p class="status-text">STATUS: AGUARDE</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($veiculos_cliente as $index => $veiculo): ?>
                                <div class="mobile-vehicle-card <?php echo $index === 0 ? 'active' : ''; ?>" 
                                     data-lat="<?php echo $veiculo['latitude']; ?>"
                                     data-lng="<?php echo $veiculo['longitude']; ?>">
                                    <div class="mobile-vehicle-icon">
                                        <img src="<?php echo htmlspecialchars($veiculo['icone_url'] ?? 'imagens/logos/default.png'); ?>" alt="Logo">
                                    </div>
                                    <div class="mobile-vehicle-info">
                                        <h4><?php echo htmlspecialchars($veiculo['modelo']); ?></h4>
                                        <p class="status-text">STATUS: <?php echo htmlspecialchars($veiculo['status']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <div id="page-entregador" class="page">
                <div class="info-card">
                    <div class="card-header"><h2>SEGURANÇA NA SUA ENTREGA</h2></div>
                    <div class="info-grid">
                        <div class="info-details">
                            <div class="info-item"><label>NOME</label><span><?php echo htmlspecialchars($motorista_data['nome'] ?? 'N/D'); ?></span></div>
                            <div class="info-item"><label>ENDEREÇO COMERCIAL</label><span><?php echo htmlspecialchars($motorista_data['endereco_comercial'] ?? 'N/D'); ?></span></div>
                            <div class="info-item"><label>HABILITAÇÃO</label><span><?php echo htmlspecialchars($motorista_data['habilitacao'] ?? 'N/D'); ?></span></div>
                            <div class="info-item"><label>ADMISSÃO</label><span><?php echo $motorista_data['data_admissao'] ? date('d/m/Y', strtotime($motorista_data['data_admissao'])) : 'N/D'; ?></span></div>
                        </div>
                        <div class="info-photo">
                            <img src="<?php echo htmlspecialchars($motorista_data['foto_url'] ?? 'imagens/motorista_padrao.png'); ?>" alt="Foto do Entregador">
                            <div class="status-badge"><?php echo htmlspecialchars($motorista_data['status'] ?? 'N/D'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="page-perfil" class="page">
                <div class="info-card">
                    <div class="card-header"><h2>CADASTRO</h2></div>
                    <div class="info-grid">
                        <div class="info-details">
                            <div class="info-item"><label>NOME</label><span><?php echo htmlspecialchars($cliente_data['nome'] ?? 'N/D'); ?></span></div>
                            <?php if (!empty($cliente_data['telefone'])): ?>
                                <div class="info-item">
                                    <label>TELEFONE</label>
                                    <span><?php echo htmlspecialchars($cliente_data['telefone']); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="info-item"><label>ENDEREÇO</label><span><?php echo htmlspecialchars($cliente_data['endereco'] ?? 'N/D'); ?></span></div>
                            <div class="info-item"><label>CPF</label><span><?php echo htmlspecialchars($cliente_data['cpf'] ?? 'N/D'); ?></span></div>
                            <div class="info-item"><label>VEÍCULOS</label>
                                <span>
                                    <?php 
                                    if (!empty($veiculos_cliente)) {
                                        $modelos = array_map(function($v) { return $v['modelo']; }, $veiculos_cliente);
                                        echo htmlspecialchars(implode(', ', $modelos));
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-photo"><div class="status-badge">CADASTRADO</div></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer class="mobile-bottom-nav">
        <a href="#" class="mobile-nav-item" onclick="showPage('perfil')"><svg viewBox="0 0 24 24"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"></path></svg><span>Meu Perfil</span></a>
        <a href="#" class="mobile-nav-item active" onclick="showPage('rastreio')"><svg viewBox="0 0 24 24"><path d="M12,2C15.86,2 19,5.13 19,9C19,14.25 12,22 12,22C12,22 5,14.25 5,9C5,5.13 8.13,2 12,2M12,11.5A2.5,2.5 0 0,0 14.5,9A2.5,2.5 0 0,0 12,6.5A2.5,2.5 0 0,0 9.5,9A2.5,2.5 0 0,0 12,11.5Z"></path></svg><span>Rastreio</span></a>
        <a href="#" class="mobile-nav-item" onclick="showPage('entregador')"><svg viewBox="0 0 24 24"><path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,6A3,3 0 0,1 15,9A3,3 0 0,1 12,12A3,3 0 0,1 9,9A3,3 0 0,1 12,6M12,14C16,14 18.2,15.74 18.5,17.29L16.5,17.93C16.27,16.94 14.5,16 12,16C9.5,16 7.73,16.94 7.5,17.93L5.5,17.29C5.8,15.74 8,14 12,14Z"></path></svg><span>Entregador</span></a>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Variáveis globais
        const navItems = document.querySelectorAll('.nav-item, .mobile-nav-item');
        const pages = document.querySelectorAll('.page');
        const breadcrumbPage = document.getElementById('breadcrumb-page');
        let map, mobileMap;
        let vehicleMarker = null;
        let mobileVehicleMarker = null;

        function showPage(pageId) {
            pages.forEach(page => page.classList.remove('active'));
            navItems.forEach(item => item.classList.remove('active'));
            const pageToShow = document.getElementById(`page-${pageId}`);
            if(pageToShow) pageToShow.classList.add('active');
            const navItemToActivate = document.getElementById(`nav-${pageId}`);
            if (navItemToActivate) navItemToActivate.classList.add('active');
            document.querySelectorAll('.mobile-nav-item').forEach(item => {
                if (item.getAttribute('onclick').includes(`'${pageId}'`)) {
                    item.classList.add('active');
                }
            });
            if (breadcrumbPage && navItemToActivate) {
                breadcrumbPage.textContent = navItemToActivate.querySelector('span').textContent.toUpperCase();
            }
            closeMobileSidebar();
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
            document.body.classList.toggle('sidebar-is-open');
            if (sidebar.classList.contains('mobile-open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.classList.remove('sidebar-is-open');
            document.body.style.overflow = '';
        }

        function initMaps() {
            if (document.getElementById('map')) {
                map = L.map('map').setView([-14.235, -51.925], 4);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
            }
            if (document.getElementById('mobile-map')) {
                mobileMap = L.map('mobile-map').setView([-14.235, -51.925], 4);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(mobileMap);
            }
        }

        function updateMapInfo(cardElement) {
            document.querySelectorAll('.vehicle-card').forEach(card => card.classList.remove('active'));
            cardElement.classList.add('active');
            const lat = cardElement.dataset.lat;
            const lng = cardElement.dataset.lng;
            if (lat && lng && lat !== '' && lng !== '') {
                const latLng = [parseFloat(lat), parseFloat(lng)];
                if (!vehicleMarker) {
                    vehicleMarker = L.marker(latLng).addTo(map);
                } else {
                    vehicleMarker.setLatLng(latLng);
                }
                map.flyTo(latLng, 15);
            }
        }

        function updateMobileMapInfo() {
            const mobileVehicleCard = document.querySelector('.mobile-vehicle-card.active'); // Select only the active one
            if (mobileVehicleCard && mobileMap) {
                const lat = mobileVehicleCard.dataset.lat;
                const lng = mobileVehicleCard.dataset.lng;
                if (lat && lng && lat !== '' && lng !== '') {
                    const latLng = [parseFloat(lat), parseFloat(lng)];
                    if (!mobileVehicleMarker) {
                        mobileVehicleMarker = L.marker(latLng).addTo(mobileMap);
                    } else {
                        mobileVehicleMarker.setLatLng(latLng);
                    }
                    mobileMap.flyTo(latLng, 15);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initMaps();
            const firstVehicleCard = document.querySelector('.vehicle-card');
            if (firstVehicleCard) {
                firstVehicleCard.click();
            }
            setTimeout(() => {
                if (map) map.invalidateSize();
                if (mobileMap) mobileMap.invalidateSize();
                updateMobileMapInfo();
            }, 500);
        });

        window.addEventListener('resize', function() {
            setTimeout(() => {
                if (map) map.invalidateSize();
                if (mobileMap) mobileMap.invalidateSize();
            }, 300);
        });
    </script>
</body>
</html>