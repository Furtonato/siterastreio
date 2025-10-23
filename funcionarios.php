<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'admin/db.php';

// Busca todos os funcionários cadastrados no banco, ordenando pelo nome
$funcionarios = $conexao->query("SELECT * FROM funcionarios ORDER BY nome ASC");

// Busca o logo principal nas configurações
$resultado_logo = $conexao->query("SELECT valor FROM configuracoes WHERE chave = 'url_logo_principal'");
$url_logo_principal = $resultado_logo->fetch_assoc()['valor'] ?? 'imagens/logo.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossa Equipe - Copart Leilões</title>
    <link rel="stylesheet" href="css/dynamic_style.php">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@400;700&display=swap');
        html { scroll-behavior: smooth; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: #F7F7F8; color: #2E323C; line-height: 1.6; font-size: 14px; overflow-x: hidden; }
        .main-container { max-width: 1140px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; font-weight: 600; color: #1E2126; margin: 0; }
        a { color: #0056b3; text-decoration: none; }
        a:hover { text-decoration: underline; }

         /* --- CABEÇALHO RESPONSIVO (Estilo Index) --- */
        .header { background: linear-gradient(to right, #004080, #001f3f); padding: 15px 0; color: #ffffff; border-bottom: 3px solid #FFC107; position: relative; }
        .container-header { display: flex; align-items: center; justify-content: space-between; max-width: 1140px; margin: 0 auto; padding: 0 15px; }
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
             .container-header { max-width: 1140px; }
        }
        @media (max-height: 500px) and (orientation: landscape) {
            .mobile-nav { padding-top: 50px; } .mobile-nav li { margin: 8px 0; } .mobile-nav a { padding: 8px 15px; font-size: 1em; }
        }

        /* --- ESTILOS PÁGINA EQUIPE --- */
        .page-header-internal {
            text-align: center; padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0; margin-bottom: 30px;
        }
        .page-header-internal h1 { color: #003D7A; font-size: 2em; margin-bottom: 10px; }
        .page-header-internal p { font-size: 1em; color: #555; max-width: 700px; margin: 0 auto; }

        .employee-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px; }
        .employee-card { background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 25px 20px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.05); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .employee-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .employee-photo img { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid #0056b3; margin-bottom: 15px; }
        .employee-name { font-size: 1.3em; font-weight: 700; color: #003D7A; margin-bottom: 8px; }
        .verified-badge { display: inline-flex; align-items: center; background-color: #E6FBF5; color: #0A6648; padding: 4px 10px; border-radius: 16px; font-size: 0.8em; font-weight: bold; margin-bottom: 12px; }
        .verified-badge svg { width: 14px; height: 14px; margin-right: 6px; fill: currentColor; }
        .employee-phone { font-size: 0.95em; color: #444; margin-bottom: 5px; }
        .employee-phone strong { color: #333; }
        .employee-phone a { color: #0056b3; font-weight: 500; }
        .employee-phone a:hover { text-decoration: underline; }
        .no-employees { text-align: center; padding: 40px 20px; font-size: 1.1em; color: #555; grid-column: 1 / -1;}
        @media (max-width: 768px) {
            .page-header-internal h1 { font-size: 1.8em; }
            .page-header-internal p { font-size: 0.9em; }
            .employee-grid { grid-template-columns: 1fr; }
            .employee-name { font-size: 1.2em; }
        }

        /* --- RODAPÉ (Estilo Index) --- */
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
        @media (max-width: 768px) {
            .footer-container { flex-direction: column; text-align: center; }
            .footer-logo-column { max-width: none; }
            .selector-box { margin: 0 auto 15px auto; }
            .sub-footer { flex-direction: column; gap: 10px; }
            .sub-footer-links { justify-content: center; }
        }

        /* --- BOTÃO VOLTAR AO TOPO --- */
        .back-to-top-btn { position: fixed; bottom: 20px; right: 20px; display: flex; align-items: center; gap: 5px; background-color: #f39c12; color: #fff; padding: 8px 12px; border-radius: 5px; text-decoration: none; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.2); opacity: 0; visibility: hidden; transition: opacity 0.4s, visibility 0.4s; z-index: 1000; font-size: 0.85em;}
        .back-to-top-btn.show { opacity: 1; visibility: visible; }
        .back-to-top-btn svg { width: 14px; height: 14px; fill: #fff; }
        .back-to-top-btn:hover { background-color: #e0a800; text-decoration: none; color: #fff; }

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
        <header class="page-header-internal">
            <h1>Nossa Equipe</h1>
            <p>Para sua segurança e transparência, conheça os consultores autorizados. Em caso de dúvida, confirme as informações diretamente conosco.</p>
        </header>

        <main>
            <div class="employee-grid">

                <?php if ($funcionarios && $funcionarios->num_rows > 0): ?>
                    <?php while($func = $funcionarios->fetch_assoc()): ?>
                        <div class="employee-card">
                            <div class="employee-photo">
                                <?php
                                    $foto_func_path = $func['foto_url'] ?? 'imagens/funcionario-padrao.png';
                                    if (empty($foto_func_path) || !file_exists($foto_func_path)) {
                                        $foto_func_path = 'imagens/funcionario-padrao.png'; // Placeholder
                                    }
                                ?>
                                <img src="<?php echo htmlspecialchars($foto_func_path); ?>" alt="Foto de <?php echo htmlspecialchars($func['nome']); ?>">
                            </div>
                            <div class="employee-name"><?php echo htmlspecialchars($func['nome']); ?></div>
                            <div class="verified-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2L4.5 5V11C4.5 16.55 7.71 21.69 12 23C16.29 21.69 19.5 16.55 19.5 11V5L12 2M10 17L6 13L7.41 11.59L10 14.17L16.59 7.58L18 9L10 17Z"></path></svg>
                                Verificado
                            </div>
                            <div class="employee-phone">
                                <strong>Telefone:</strong> <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $func['telefone']); ?>"><?php echo htmlspecialchars($func['telefone']); ?></a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php $funcionarios->free(); ?>
                <?php else: ?>
                    <div class="no-employees">
                        <p>Nenhum consultor autorizado encontrado no momento. Por favor, entre em contato através dos nossos canais oficiais.</p>
                    </div>
                <?php endif; ?>

            </div>
        </main>

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

        document.addEventListener('DOMContentLoaded', function() {
            const currentYearSpan = document.getElementById('currentYearFooter');
            if (currentYearSpan) { currentYearSpan.textContent = new Date().getFullYear(); }

            document.addEventListener('click', function(event) {
                const hamburger = document.querySelector('.hamburger-menu');
                const mobileNav = document.getElementById('mobileNav');
                if (mobileNav && mobileNav.classList.contains('active') && hamburger && !hamburger.contains(event.target) && !mobileNav.contains(event.target)) {
                    closeMobileMenu();
                }
            });
            window.addEventListener('resize', function() {
                if (window.innerWidth > 959) { closeMobileMenu(); }
            });
        });
    </script>
</body>
</html>