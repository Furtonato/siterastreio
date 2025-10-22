<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'admin/db.php';

// Busca todos os depoimentos cadastrados no banco
$depoimentos = $conexao->query("SELECT * FROM depoimentos ORDER BY id DESC");

$resultado_logo = $conexao->query("SELECT valor FROM configuracoes WHERE chave = 'url_logo_principal'");
$url_logo_principal = $resultado_logo->fetch_assoc()['valor'] ?? 'imagens/logo.png';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leilões de Veículos Copart</title>
    <link rel="stylesheet" href="css/dynamic_style.php">
    <style>
        /* --- GERAL --- */
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&family=Roboto:wght@400;700&display=swap');

        * {
            box-sizing: border-box;
        }

        html { 
            scroll-behavior: smooth; 
        }
        
        body { 
            font-family: 'Roboto', sans-serif; 
            margin: 0; 
            background-color: #f8f9fa; 
            color: #343a40; 
            line-height: 1.6; 
            font-size: 16px; 
            overflow-x: hidden;
        }
        
        .container { 
            max-width: 1140px; 
            margin: 0 auto; 
            padding: 15px; 
        }
        
        h1, h2, h3, h4 { 
            font-family: 'Montserrat', sans-serif; 
            font-weight: 700; 
        }

        /* --- CABEÇALHO RESPONSIVO --- */
        .header { 
            background: linear-gradient(to right, #004080, #001f3f); 
            padding: 20px 0; 
            color: #ffffff; 
            border-bottom: 5px solid #FFC107; 
            position: relative;
        }
        
        .container-header { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            gap: 20px; 
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .logo-e-texto { 
            display: flex; 
            align-items: center; 
            gap: 20px; 
        }
        
        .logo-header-img { 
            max-width: 130px; 
            height: auto; 
        }
        
        .header-text-content { 
            text-align: left; 
        }
        
        .site-title { 
            font-size: 2em; 
            font-weight: 700; 
            margin: 0 0 5px 0; 
            letter-spacing: 1px; 
            color: #ffffff; 
            font-family: 'Montserrat', sans-serif; 
        }
        
        .site-slogan { 
            font-size: 1em; 
            margin: 0; 
            opacity: 0.9; 
            color: #ffffff; 
            font-weight: 700; 
            font-family: 'Montserrat', sans-serif; 
        }

        /* --- MENU HAMBÚRGUER --- */
        .hamburger-menu {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
            z-index: 1001;
        }

        .hamburger-menu span {
            width: 25px;
            height: 3px;
            background-color: #ffffff;
            margin: 3px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .hamburger-menu.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .hamburger-menu.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger-menu.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        /* --- NAVEGAÇÃO --- */
        .header-nav ul { 
            list-style: none; 
            margin: 0; 
            padding: 0; 
            display: flex; 
            align-items: center; 
            gap: 25px; 
        }
        
        .header-nav a { 
            text-decoration: none; 
            color: #ffffff; 
            font-family: 'Montserrat', sans-serif; 
            font-weight: 500; 
            font-size: 1rem; 
            transition: opacity 0.3s ease; 
        }
        
        .header-nav a:hover { 
            opacity: 0.8; 
        }
        
        .header-nav .btn-rastrear { 
            background-color: #f39c12; 
            color: #ffffff; 
            padding: 12px 24px; 
            border-radius: 5px; 
            font-weight: bold; 
            transition: background-color 0.3s ease; 
        }
        
        .header-nav .btn-rastrear:hover { 
            background-color: #e0a800; 
            opacity: 1; 
        }

        /* --- MENU MOBILE --- */
        .mobile-nav {
            position: fixed;
            top: 0;
            left: -300px;
            width: 300px;
            height: 100vh;
            background-color: rgba(0, 64, 128, 0.98);
            z-index: 1000;
            padding-top: 80px;
            transition: left 0.3s ease;
            visibility: hidden;
            opacity: 0;
        }

        .mobile-nav.active {
            left: 0;
            visibility: visible;
            opacity: 1;
        }

        .mobile-nav.active {
            display: block;
            left: 0;
        }


        .mobile-nav.active {
            display: block;
        }

        .mobile-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: center;
        }

        .mobile-nav li {
            margin: 20px 0;
        }

        .mobile-nav a {
            color: #ffffff;
            text-decoration: none;
            font-size: 1.2em;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            display: block;
            padding: 15px 20px;
            transition: background-color 0.3s ease;
        }

        .mobile-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .mobile-nav .btn-rastrear {
            background-color: #f39c12;
            margin: 20px auto;
            display: inline-block;
            border-radius: 8px;
            max-width: 200px;
        }
        
        /* --- ESTILOS DO CONTEÚDO --- */
        .header-alert { 
            background-color: #e6f7ff; 
            color: #004085; 
            border-bottom: 1px solid #b8daff; 
            padding: 12px 15px; 
            text-align: center; 
            font-size: 0.9em; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            gap: 10px; 
            flex-wrap: wrap; 
        }
        
        .header-alert svg { 
            width: 20px; 
            height: 20px; 
            fill: #004085; 
            flex-shrink: 0; 
        }
        
        .header-alert a { 
            color: #004085; 
            font-weight: bold; 
            text-decoration: none; 
        }
        
        .header-alert a:hover { 
            text-decoration: underline; 
        }
        
        .video-carousel-section { 
            padding: 30px 0; 
            background-color: #e9ecef; 
        }
        
        .video-carousel-title { 
            text-align: center; 
            font-size: 1.8em; 
            color: #003D7A; 
            margin-bottom: 20px; 
            font-family: 'Montserrat', sans-serif;
        }
        
        .video-carousel-container { 
            max-width: 90%; 
            margin: 0 auto; 
            position: relative; 
            overflow: hidden; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        
        .video-carousel-slide { 
            display: none; 
            width: 100%; 
            position: relative; 
            padding-bottom: 56.25%; 
            height: 0; 
            background-color: #000; 
        }
        
        .video-carousel-slide iframe { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            border: none; 
        }
        
        .video-carousel-slide.active { 
            display: block; 
        }
        
        .video-carousel-prev, .video-carousel-next { 
            cursor: pointer; 
            position: absolute; 
            top: 50%; 
            width: auto; 
            padding: 12px; 
            margin-top: -20px; 
            color: white; 
            font-weight: bold; 
            font-size: 18px; 
            transition: 0.6s ease; 
            border-radius: 0 3px 3px 0; 
            user-select: none; 
            background-color: rgba(0, 0, 0, 0.4); 
            z-index: 10; 
        }
        
        .video-carousel-next { 
            right: 0; 
            border-radius: 3px 0 0 3px; 
        }
        
        .video-carousel-prev { 
            left: 0; 
        }
        
        .video-carousel-prev:hover, .video-carousel-next:hover { 
            background-color: rgba(0, 0, 0, 0.7); 
        }
        
        .video-carousel-dots { 
            text-align: center; 
            padding: 15px 0 0 0; 
        }
        
        .video-carousel-dot { 
            cursor: pointer; 
            height: 12px; 
            width: 12px; 
            margin: 0 3px; 
            background-color: #bbb; 
            border-radius: 50%; 
            display: inline-block; 
            transition: background-color 0.6s ease; 
        }
        
        .video-carousel-dot.active, .video-carousel-dot:hover { 
            background-color: #0056b3; 
        }
        
        .testimonials-title-section { 
            text-align: center; 
            padding: 40px 15px; 
        }
        
        .testimonials-title-section h2 { 
            font-size: 1.8em; 
            margin-bottom: 10px; 
            color: #003D7A; 
            font-family: 'Montserrat', sans-serif;
        }
        
        .testimonials-title-section p { 
            font-size: 1em; 
            color: #555; 
            max-width: 100%; 
            margin: 0 auto; 
        }
        
        .testimonials-section { 
            padding: 0 0 40px 0; 
        }
        
        .testimonials-grid { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 25px; 
        }
        
        .testimonial-card { 
            background-color: #ffffff; 
            border-radius: 12px; 
            box-shadow: 0 6px 20px rgba(0, 61, 122, 0.1); 
            overflow: hidden; 
            display: flex; 
            flex-direction: column; 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
        }
        
        .testimonial-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 25px rgba(0, 61, 122, 0.12); 
        }
        
        .testimonial-avatar-wrapper { 
            text-align: center; 
            padding-top: 25px; 
        }
        
        .testimonial-avatar img { 
            width: 120px; 
            height: 120px; 
            border-radius: 12px; 
            object-fit: cover; 
            border: 4px solid #0056b3; 
            box-shadow: 0 3px 10px rgba(0,0,0,0.1); 
        }
        
        .testimonial-content { 
            padding: 20px 25px 25px 25px; 
            text-align: center; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
        }
        
        .testimonial-text { 
            font-size: 0.95em; 
            color: #495057; 
            line-height: 1.6; 
            flex-grow: 1; 
            margin-top: 15px; 
            font-style: italic; 
        }
        
        .testimonial-text::before { 
            content: '"'; 
            font-family: 'Montserrat', sans-serif; 
            font-size: 3em; 
            color: #FFC107; 
            font-weight: 700; 
            line-height: 0.5; 
            display: block; 
            margin-bottom: 8px; 
        }
        
        .testimonial-text::after { 
            content: '"'; 
            font-family: 'Montserrat', sans-serif; 
            font-size: 3em; 
            color: #FFC107; 
            font-weight: 700; 
            line-height: 0; 
            display: block; 
            margin-top: 8px; 
            text-align: right; 
        }
        
        .testimonial-author { 
            margin-top: 20px; 
        }
        
        .author-name { 
            font-family: 'Montserrat', sans-serif; 
            font-weight: 700; 
            font-size: 1.1em; 
            color: #003D7A; 
        }
        
        .author-role { 
            font-size: 0.85em; 
            color: #6c757d; 
            margin-top: 4px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        
        .employee-verification-cta { 
            padding: 35px 15px; 
            background-color: #fff; 
            text-align: center; 
            margin: 40px 0; 
            border-top: 1px solid #e0e0e0; 
            border-bottom: 1px solid #e0e0e0; 
        }
        
        .employee-verification-cta h2 { 
            font-size: 1.7em; 
            color: #003D7A; 
            margin-bottom: 15px; 
        }
        
        .employee-verification-cta p { 
            font-size: 1.05em; 
            color: #333; 
            margin-bottom: 25px; 
            max-width: 650px; 
            margin-left: auto; 
            margin-right: auto; 
            line-height: 1.7; 
        }
        
        .btn-verificar-equipe { 
            display: inline-block; 
            background-color: #0056b3; 
            color: #fff; 
            padding: 14px 30px; 
            font-size: 1.1em; 
            font-weight: bold; 
            text-decoration: none; 
            border-radius: 8px; 
            transition: background-color 0.3s ease, transform 0.2s ease; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        
        .btn-verificar-equipe:hover { 
            background-color: #003D7A; 
            transform: translateY(-2px); 
        }

        /* --- NOVA SEÇÃO RECLAME AQUI v2 --- */
        .ra-v2-wrapper { 
            background-color: #f0f2f5; 
            padding: 50px 15px; 
            font-family: 'Roboto', sans-serif; 
            font-size: 14px; 
            margin: 40px 0; 
        }
        
        .ra-v2-container { 
            max-width: 1100px; 
            margin: 0 auto; 
        }
        
        .ra-v2-header-banner {
            background-image: url('imagens/reclame/banner reclame.jpg');
            background-size: cover;
            background-position: center;
            border-radius: 16px;
            position: relative;
            height: 140px;
            overflow: hidden;
        }
        
        .ra-v2-company-card { 
            background-color: #fff; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
            padding: 15px; 
            display: flex; 
            flex-wrap: wrap; 
            align-items: center; 
            gap: 20px; 
            max-width: 95%; 
            margin: -60px auto 0 auto; 
            position: relative; 
        }
        
        .ra-v2-company-card .logo { 
            width: 70px; 
            height: 70px; 
            border-radius: 8px; 
            border: 1px solid #eee; 
            padding: 5px; 
        }
        
        .ra-v2-company-info h3 { 
            margin: 0; 
            font-size: 1.5em; 
            color: #333; 
            display: flex; 
            align-items: center; 
        }
        
        .ra-v2-company-info .verified-checkmark { 
            height: 20px; 
            margin-left: 8px; 
        }
        
        .ra-v2-tags { 
            font-size: 0.9em; 
            color: #666; 
            margin: 5px 0; 
        }
        
        .ra-v2-badges { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 10px; 
            margin-top: 10px; 
        }
        
        .ra-v2-badge { 
            display: flex; 
            align-items: center; 
            gap: 5px; 
            padding: 4px 8px; 
            border-radius: 16px; 
            font-weight: bold; 
            font-size: 0.8em; 
        }
        
        .ra-v2-badge.ra1000 { 
            background-color: #E5F8EE; 
            color: #006B33; 
            border: 1px solid #A3E9C5; 
        }
        
        .ra-v2-badge.verificada { 
            background-color: #E5F8EE; 
            color: #006B33; 
            border: 1px solid #A3E9C5; 
        }
        
        .ra-v2-badge img { 
            height: 14px; 
        }
        
        .ra-v2-main-content { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 20px; 
            margin-top: 30px; 
        }
        
        .ra-v2-card { 
            background-color: #fff; 
            border-radius: 8px; 
            padding: 20px; 
            border: 1px solid #e9ecef; 
        }
        
        .ra-v2-left-col h4, .ra-v2-right-col h4, .ra-v2-mid-col h4 { 
            font-size: 1.1em; 
            color: #4a4a4a; 
            margin-bottom: 15px; 
            padding-bottom: 10px; 
            border-bottom: 1px solid #eee; 
        }
        
        .ra-v2-left-col .reputation-box { 
            background-color: #f8f9fa; 
            border: 1px solid #e9ecef; 
            padding: 15px; 
            border-radius: 8px; 
            margin-top: 10px; 
        }
        
        .ra-v2-left-col .reputation-box .score { 
            font-size: 2em; 
            font-weight: bold; 
            color: #00a859; 
        }
        
        .ra-v2-link { 
            color: #0073b2; 
            font-weight: bold; 
            text-decoration: none; 
            margin-top: 10px; 
            display: inline-block; 
        }
        
        .ra-v2-right-col ul { 
            list-style: none; 
            padding: 0; 
        }
        
        .ra-v2-right-col li { 
            display: flex; 
            align-items: flex-start; 
            gap: 10px; 
            margin-bottom: 15px; 
            font-size: 0.9em; 
            color: #4a4a4a; 
        }
        
        .ra-v2-right-col li img { 
            width: 20px; 
            margin-top: 2px; 
        }
        
        .ra-v2-mid-col .complaint-card { 
            border-bottom: 1px solid #eee; 
            padding-bottom: 15px; 
            margin-bottom: 15px; 
        }
        
        .ra-v2-mid-col .complaint-card:last-child { 
            border-bottom: none; 
            margin-bottom: 0; 
            padding-bottom: 0; 
        }
        
        .ra-v2-mid-col .complaint-card h5 { 
            font-size: 1em; 
            font-weight: bold; 
            color: #00558b; 
            margin-bottom: 5px; 
        }
        
        .ra-v2-mid-col .complaint-card p { 
            font-size: 0.9em; 
            color: #595959; 
            line-height: 1.5; 
            margin-bottom: 10px;
        }
        
        .ra-v2-mid-col .complaint-card footer { 
            font-size: 0.8em; 
            color: #919191; 
        }
        
        .complaint-card-footer { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-top: 15px; 
            flex-wrap: wrap; 
            gap: 10px;
        }
        
        .response-tag { 
            background-color: #4a5a9c; 
            color: #fff; 
            font-size: 0.8em; 
            font-weight: bold; 
            padding: 6px 12px; 
            border-radius: 20px; 
            display: flex; 
            align-items: center; 
            gap: 6px; 
        }
        
        .response-tag svg { 
            width: 14px; 
            height: 14px; 
            fill: currentColor; 
        }
        
        .reactions { 
            display: flex; 
            align-items: center; 
            gap: 5px; 
        }
        
        .reactions span { 
            font-size: 0.85em; 
            color: #666; 
        }
        
        .reaction-item { 
            width: 32px; 
            height: 32px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer; 
            transition: transform 0.2s; 
        }
        
        .reaction-item:hover { 
            transform: scale(1.1); 
        }
        
        .reaction-item svg { 
            width: 18px; 
            height: 18px; 
            color: #666; 
        }
        
        .reaction-like { 
            background-color: #e6f7ff; 
        }
        
        .reaction-also { 
            background-color: #e6fbf5; 
        }
        
        .reaction-angry { 
            background-color: #fff0e6; 
        }

        /* --- RODAPÉ --- */
        .footer { 
            background-color: #222222; 
            color: #a9a9a9; 
            padding: 60px 0 20px 0; 
            font-size: 0.9em; 
        }
        
        .footer-container { 
            max-width: 1140px; 
            margin: 0 auto; 
            padding: 0 15px; 
            display: flex; 
            flex-wrap: wrap; 
            justify-content: space-between; 
            gap: 30px; 
        }
        
        .footer-column { 
            flex: 1; 
            min-width: 180px; 
        }
        
        .footer-column h4 { 
            color: #ffffff; 
            font-size: 1em; 
            margin-bottom: 20px; 
            font-weight: 500; 
        }
        
        .footer-column ul { 
            list-style: none; 
            padding: 0; 
            margin: 0; 
        }
        
        .footer-column ul li { 
            margin-bottom: 12px; 
        }
        
        .footer-column a { 
            color: #a9a9a9; 
            text-decoration: none; 
            transition: color 0.3s; 
        }
        
        .footer-column a:hover { 
            color: #ffffff; 
        }
        
        .footer-logo-column { 
            flex-basis: 100%; 
            max-width: 200px; 
        }
        
        .footer-logo-column img { 
            max-width: 150px; 
            margin-bottom: 20px; 
        }
        
        .selector-box { 
            background-color: #333; 
            border: 1px solid #444; 
            border-radius: 4px; 
            padding: 10px; 
            margin-bottom: 15px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            color: #fff; 
        }
        
        .selector-box .flag-icon { 
            width: 24px; 
            height: auto; 
            margin-right: 10px; 
            border-radius: 2px; 
        }
        
        .sub-footer { 
            border-top: 1px solid #444; 
            margin-top: 40px; 
            padding-top: 20px; 
            display: flex; 
            flex-wrap: wrap; 
            justify-content: space-between; 
            align-items: center; 
            font-size: 0.8em; 
        }
        
        .sub-footer-links { 
            list-style: none; 
            padding: 0; 
            margin: 0; 
            display: flex; 
            flex-wrap: wrap; 
            gap: 20px; 
        }

        /* --- BOTÃO VOLTAR AO TOPO --- */
        .back-to-top-btn { 
            position: fixed; 
            bottom: 25px; 
            right: 25px; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            background-color: #f39c12; 
            color: #222; 
            padding: 12px 18px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 600; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.2); 
            opacity: 0; 
            visibility: hidden; 
            transition: opacity 0.4s, visibility 0.4s; 
            z-index: 1000; 
        }
        
        .back-to-top-btn.show { 
            opacity: 1; 
            visibility: visible; 
        }
        
        .back-to-top-btn svg { 
            width: 16px; 
            height: 16px; 
            fill: #222; 
        }

        /* --- MEDIA QUERIES RESPONSIVAS --- */
        
        /* Mobile - até 768px */
        @media (max-width: 768px) {
            .header {
                padding: 15px 0;
            }
            
            .container-header {
                padding: 0 15px;
                gap: 15px;
            }
            
            .logo-header-img {
                max-width: 80px;
            }
            
            /* Ocultar texto do cabeçalho no mobile */
            .header-text-content {
                display: none;
            }
            
            /* Mostrar menu hambúrguer no mobile */
            .header-nav {
                display: none;
            }
            
            .hamburger-menu {
                display: flex;
            }
            
            .container {
                padding: 10px;
            }
            
            .header-alert {
                font-size: 0.8em;
                padding: 10px;
                text-align: left;
            }
            
            .header-alert p {
                font-size: 0.85em;
            }
            
            .video-carousel-title {
                font-size: 1.5em;
                padding: 0 10px;
            }
            
            .video-carousel-container {
                max-width: 95%;
            }
            
            .video-carousel-prev, .video-carousel-next {
                padding: 8px;
                font-size: 14px;
            }
            
            .video-carousel-dot {
                height: 8px;
                width: 8px;
                margin: 0 2px;
            }
            
            .testimonials-title-section {
                padding: 30px 15px;
            }
            
            .testimonials-title-section h2 {
                font-size: 1.5em;
            }
            
            .testimonials-title-section p {
                font-size: 0.9em;
            }
            
            .testimonials-grid {
                gap: 20px;
            }
            
            .testimonial-avatar img {
                width: 100px;
                height: 100px;
            }
            
            .testimonial-content {
                padding: 15px 20px 20px 20px;
            }
            
            .testimonial-text {
                font-size: 0.9em;
            }
            
            .testimonial-text::before,
            .testimonial-text::after {
                font-size: 2.5em;
            }
            
            .author-name {
                font-size: 1em;
            }
            
            .author-role {
                font-size: 0.8em;
            }
            
            .employee-verification-cta {
                padding: 25px 15px;
            }
            
            .employee-verification-cta h2 {
                font-size: 1.4em;
            }
            
            .employee-verification-cta p {
                font-size: 0.95em;
            }
            
            .btn-verificar-equipe {
                padding: 12px 25px;
                font-size: 1em;
            }
            
            .ra-v2-wrapper {
                padding: 30px 10px;
            }
            
            .ra-v2-header-banner {
                height: 100px;
            }
            
            .ra-v2-company-card {
                padding: 12px;
                gap: 15px;
                margin: -40px auto 0 auto;
            }
            
            .ra-v2-company-card .logo {
                width: 50px;
                height: 50px;
            }
            
            .ra-v2-company-info h3 {
                font-size: 1.2em;
                flex-wrap: wrap;
            }
            
            .ra-v2-company-info .verified-checkmark {
                height: 16px;
            }
            
            .ra-v2-tags {
                font-size: 0.8em;
            }
            
            .ra-v2-badges {
                gap: 8px;
            }
            
            .ra-v2-badge {
                font-size: 0.7em;
                padding: 3px 6px;
            }
            
            .ra-v2-badge img {
                height: 12px;
            }
            
            .ra-v2-main-content {
                gap: 15px;
                margin-top: 20px;
            }
            
            .ra-v2-card {
                padding: 15px;
            }
            
            .ra-v2-left-col h4, .ra-v2-right-col h4, .ra-v2-mid-col h4 {
                font-size: 1em;
            }
            
            .ra-v2-left-col .reputation-box .score {
                font-size: 1.6em;
            }
            
            .ra-v2-right-col li {
                font-size: 0.85em;
            }
            
            .ra-v2-right-col li img {
                width: 16px;
            }
            
            .ra-v2-mid-col .complaint-card h5 {
                font-size: 0.9em;
            }
            
            .ra-v2-mid-col .complaint-card p {
                font-size: 0.85em;
            }
            
            .ra-v2-mid-col .complaint-card footer {
                font-size: 0.75em;
            }
            
            .complaint-card-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .response-tag {
                font-size: 0.7em;
                padding: 4px 8px;
            }
            
            .response-tag svg {
                width: 12px;
                height: 12px;
            }
            
            .reaction-item {
                width: 28px;
                height: 28px;
            }
            
            .reaction-item svg {
                width: 16px;
                height: 16px;
            }
            
            .reactions span {
                font-size: 0.8em;
            }
            
            .footer {
                padding: 40px 0 15px 0;
                font-size: 0.8em;
            }
            
            .footer-container {
                padding: 0 15px;
                gap: 25px;
                flex-direction: column;
            }
            
            .footer-column {
                min-width: auto;
                text-align: center;
            }
            
            .footer-column h4 {
                font-size: 0.9em;
                margin-bottom: 15px;
            }
            
            .footer-logo-column {
                max-width: none;
                text-align: center;
            }
            
            .footer-logo-column img {
                max-width: 120px;
            }
            
            .selector-box {
                max-width: 200px;
                margin: 0 auto 15px auto;
            }
            
            .sub-footer {
                margin-top: 25px;
                padding-top: 15px;
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .sub-footer-links {
                gap: 15px;
                justify-content: center;
            }
            
            .back-to-top-btn {
                bottom: 20px;
                right: 20px;
                padding: 10px 15px;
                font-size: 0.8em;
            }
            
            .back-to-top-btn svg {
                width: 14px;
                height: 14px;
            }
        }
        
        /* Tablets - 769px a 992px */
        @media (min-width: 769px) and (max-width: 992px) {
            .header-nav ul {
                gap: 20px;
            }
            
            .header-nav a {
                font-size: 0.9rem;
            }
            
            .btn-rastrear {
                padding: 10px 20px;
            }
            
            .testimonials-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            }
            
            .ra-v2-main-content {
                grid-template-columns: 220px 1fr;
            }
        }
        
        /* Desktop médio - 993px a 1200px */
        @media (min-width: 993px) and (max-width: 1200px) {
            .testimonials-grid {
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            }
            
            .ra-v2-main-content {
                grid-template-columns: 240px 1fr 260px;
            }
        }
        
        /* Desktop grande - acima de 1200px */
        @media (min-width: 1201px) {
            .testimonials-grid {
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            }
            
            .ra-v2-main-content {
                grid-template-columns: 250px 1fr 280px;
            }
        }
        
        /* Media queries específicas para Reclame Aqui */
        @media (min-width: 768px) { 
            .ra-v2-main-content { 
                grid-template-columns: 250px 1fr; 
            } 
        }
        
        @media (min-width: 992px) { 
            .ra-v2-main-content { 
                grid-template-columns: 250px 1fr 280px; 
            } 
        }

        /* Landscape mobile */
        @media (max-height: 500px) and (orientation: landscape) {
            .mobile-nav {
                padding-top: 60px;
            }
            
            .mobile-nav li {
                margin: 10px 0;
            }
            
            .mobile-nav a {
                padding: 10px 20px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <div class="container-header">
            <div class="logo-e-texto">
                <img src="<?php echo htmlspecialchars($url_logo_principal); ?>" alt="Copart Logo" class="logo-header-img">
                <div class="header-text-content">
                    <h1 class="site-title">COPART LEILÕES</h1>
                    <p class="site-slogan">Veiculos e Motocicletas com as Melhores Condições do Mercado.</p>
                </div>
            </div>
            
            <nav class="header-nav">
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="veiculos.php">Veículos</a></li>
                    <li><a href="#depoimentos-video">Depoimentos</a></li>
                    <li><a href="funcionarios.php">Equipe</a></li>
                    <li><a href="rastrear.php" class="btn-rastrear">Rastrear</a></li>
                </ul>
            </nav>
            
            <div class="hamburger-menu" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        
        <nav class="mobile-nav" id="mobileNav">
            <ul>
                <li><a href="index.php" onclick="closeMobileMenu()">Início</a></li>
                <li><a href="veiculos.php" onclick="closeMobileMenu()">Veículos</a></li>
                <li><a href="#depoimentos-video" onclick="closeMobileMenu()">Depoimentos</a></li>
                <li><a href="funcionarios.php" onclick="closeMobileMenu()">Equipe</a></li>
                <li><a href="rastrear.php" class="btn-rastrear" onclick="closeMobileMenu()">Rastrear</a></li>
            </ul>
        </nav>
    </header>

    <div class="header-alert">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"></path></svg>
        <p><strong>Atenção:</strong> Para sua segurança, confirme sempre nossos dados oficiais. CNPJ: 06.970.796.0001-91 | Para dúvidas, ligue: <a href="tel:+551124004004">(11) 2400-4004</a></p>
    </div>

    <section class="video-carousel-section" id="depoimentos-video">
        <div class="container">
            <h2 class="video-carousel-title">Depoimentos em Vídeo</h2>
            <div class="video-carousel-container">
                <div class="video-carousel-slide active"><iframe src="https://www.youtube.com/embed/yjkfiMKBeJ0" title="Depoimento em Vídeo 1" allowfullscreen></iframe></div>
                <div class="video-carousel-slide"><iframe src="https://www.youtube.com/embed/5LBjtk0TICM" title="Depoimento em Vídeo 2" allowfullscreen></iframe></div>
                <div class="video-carousel-slide"><iframe src="https://www.youtube.com/embed/0gFj7hBQDY8" title="Depoimento em Vídeo 3" allowfullscreen></iframe></div>
                <div class="video-carousel-slide"><iframe src="https://www.youtube.com/embed/GC_ECegrK8A" title="Depoimento em Vídeo 4" allowfullscreen></iframe></div>
                <div class="video-carousel-slide"><iframe src="https://www.youtube.com/embed/o0YwXvwF3G8" title="Depoimento em Vídeo 5" allowfullscreen></iframe></div>
                <div class="video-carousel-slide"><iframe src="https://www.youtube.com/embed/J1s0_eOyzy0" title="Depoimento em Vídeo 6" allowfullscreen></iframe></div>
                <div class="video-carousel-slide"><iframe src="https://www.youtube.com/embed/I1EeYPLFQGs" title="Depoimento em Vídeo 7" allowfullscreen></iframe></div>
                <div class="video-carousel-slide"><iframe src="https://www.youtube.com/embed/QdYsgkdLFHw" title="Depoimento em Vídeo 8" allowfullscreen></iframe></div>
                <div class="video-carousel-slide"><iframe src="https://www.youtube.com/embed/O1-4xiz0MBk" title="Depoimento em Vídeo 9" allowfullscreen></iframe></div>
                <div class="video-carousel-slide"><iframe src="https://www.youtube.com/embed/QKXt2qTtfF0" title="Depoimento em Vídeo 10" allowfullscreen></iframe></div>
                <a class="video-carousel-prev" onclick="changeSlide(-1)">&#10094;</a>
                <a class="video-carousel-next" onclick="changeSlide(1)">&#10095;</a>
            </div>
            <div class="video-carousel-dots">
                <span class="video-carousel-dot active" onclick="currentSlide(1)"></span>
                <span class="video-carousel-dot" onclick="currentSlide(2)"></span>
                <span class="video-carousel-dot" onclick="currentSlide(3)"></span>
                <span class="video-carousel-dot" onclick="currentSlide(4)"></span>
                <span class="video-carousel-dot" onclick="currentSlide(5)"></span>
                <span class="video-carousel-dot" onclick="currentSlide(6)"></span>
                <span class="video-carousel-dot" onclick="currentSlide(7)"></span>
                <span class="video-carousel-dot" onclick="currentSlide(8)"></span>
                <span class="video-carousel-dot" onclick="currentSlide(9)"></span>
                <span class="video-carousel-dot" onclick="currentSlide(10)"></span>
            </div>
        </div>
    </section>

    <section class="testimonials-title-section">
        <div class="container">
            <h2>Quem Confia, Recomenda! (Depoimentos em Texto)</h2>
            <p>Veja o que nossos clientes dizem sobre a experiência com a Copart Leilões.</p>
        </div>
    </section>

    <main class="testimonials-section">
        <div class="container">
            <div class="testimonials-grid">

    <?php if ($depoimentos && $depoimentos->num_rows > 0): ?>
        <?php while($dep = $depoimentos->fetch_assoc()): ?>
            <div class="testimonial-card">
                <div class="testimonial-avatar-wrapper">
                    <div class="testimonial-avatar">
                        <img src="<?php echo !empty($dep['foto_url']) ? htmlspecialchars($dep['foto_url']) : 'imagens/cliente-padrao.png'; ?>" alt="Foto do Cliente">
                    </div>
                </div>
                <div class="testimonial-content">
                    <p class="testimonial-text"><?php echo htmlspecialchars($dep['texto']); ?></p>
                    <div class="testimonial-author">
                        <div class="author-name"><?php echo htmlspecialchars($dep['nome_cliente']); ?></div>
                        <div class="author-role">Cliente Verificado</div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; grid-column: 1 / -1;">Nenhum depoimento encontrado no momento.</p>
    <?php endif; ?>

            </div> 
        </div> 
    </main>

    <section class="employee-verification-cta">
        <div class="container">
            <h2>Conheça e Verifique Nossa Equipe</h2>
            <p>Para sua segurança, confira a lista de alguns dos nossos principais vendedores.</p>
            <a href="funcionarios.php" class="btn-verificar-equipe" target="_blank">Verificar Equipe</a>
        </div>
    </section>

    <div class="ra-v2-wrapper">
        <div class="ra-v2-container">
            <div class="ra-v2-header-banner">
                </div>
            <div class="ra-v2-company-card">
                <img src="imagens/reclame/logo_copart.png" alt="Logo Copart" class="logo">
                <div class="ra-v2-company-info">
                    <h3>Copart do Brasil <img src="imagens/reclame/seal-ra-verified.svg" alt="Verificado" class="verified-checkmark"></h3>
                    <div class="ra-v2-tags">
                        <span>Veículos e Acessórios</span> • <span>+ 140 mil de visualizações</span>
                    </div>
                    <div class="ra-v2-badges">
                        <div class="ra-v2-badge ra1000"><img src="imagens/reclame/download.png" alt="RA1000"> RA1000</div>
                        <div class="ra-v2-badge verificada"><img src="imagens/reclame/seal-ra-verified.svg" alt="Verificada"> Verificada</div>
                    </div>
                </div>
            </div>
            <div class="ra-v2-main-content">
                <div class="ra-v2-left-col">
                    <div class="ra-v2-card">
                        <h4>Copart do Brasil é confiável?</h4>
                        <p style="font-size:0.9em; margin-bottom:10px;">Essa empresa é verificada e possui o selo de confiança do Reclame AQUI.</p>
                        <a href="#" class="ra-v2-link">Saiba mais</a>
                    </div>
                     <div class="ra-v2-card" style="margin-top:20px;">
                        <h4>Qual a reputação da Copart do Brasil?</h4>
                        <div class="reputation-box">
                            <span style="text-transform:uppercase; font-size:0.8em; font-weight:bold;">Reputação</span>
                            <div class="score">RA1000</div>
                            <p style="font-size:0.9em;">A empresa atingiu a reputação máxima no Reclame AQUI. Sua nota média nos últimos 6 meses é <strong>9.2/10</strong>.</p>
                        </div>
                         <a href="#" class="ra-v2-link">Saiba mais</a>
                    </div>
                </div>
                <div class="ra-v2-card ra-v2-mid-col">
                    <h4>O que estão falando sobre Copart do Brasil</h4>
                    <div class="complaint-card">
                        <h5>carro impecável e financiamento facilitado!</h5>
                        <p>Arrematei um carro na Copart Leilões e o veículo estava perfeito, como anunciado. O grande diferencial foi o suporte para o financiamento...</p>
                        <footer>Carlos M. - Piracicaba - SP | há 2 dias</footer>
                        <div class="complaint-card-footer">
                            <span class="response-tag"><svg viewBox="0 0 24 24"><path d="M12,2A2,2 0 0,1 14,4C14,4.24 13.96,4.47 13.88,4.68C15.96,5.5 17.5,7.55 17.5,10V11H18.5C19.88,11 21,12.12 21,13.5V17.5C21,18.88 19.88,20 18.5,20H5.5C4.12,20 3,18.88 3,17.5V13.5C3,12.12 4.12,11 5.5,11H6.5V10C6.5,7.55 8.04,5.5 10.12,4.68C10.04,4.47 10,4.24 10,4A2,2 0 0,1 12,2M12,4A2,2 0 0,0 10,6A2,2 0 0,0 12,8A2,2 0 0,0 14,6A2,2 0 0,0 12,4Z"></path></svg> Respondida</span>
                            <div class="reactions">
                                <div class="reaction-item reaction-like"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="m7.48 18.35 3.1 2.4c.4.4 1.3.6 1.9.6h3.8c1.2 0 2.5-.9 2.8-2.1l2.4-7.3c.5-1.4-.4-2.6-1.9-2.6h-4c-.6 0-1.1-.5-1-1.2l.5-3.2c.2-.9-.4-1.9-1.3-2.2-.8-.3-1.8.1-2.2.7l-4.1 6.1" stroke="currentColor" stroke-width="2" stroke-miterlimit="10"></path><path d="M2.38 18.35v-9.8c0-1.4.6-1.9 2-1.9h1c1.4 0 2 .5 2 1.9v9.8c0 1.4-.6 1.9-2 1.9h-1c-1.4 0-2-.5-2-1.9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                <div class="reaction-item reaction-also"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2"></path><path d="M14 10V4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v2"></path><path d="M10 10.5V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2v8"></path><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"></path></svg></div>
                                <div class="reaction-item reaction-angry"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16 16s-1.5-2-4-2-4 2-4 2"></path><path d="M7.5 8 10 9"></path><path d="m14 9 2.5-1"></path></svg></div>
                                <span>deixe sua reação</span>
                            </div>
                        </div>
                    </div>
                    <div class="complaint-card">
                        <h5>Compra transparente e ágil, com suporte da BV!</h5>
                        <p>Primeira vez comprando em leilão e a Copart Leilões tornou o processo de compra do meu carro muito simples. A plataforma é clara e o atendimento foi excelente...</p>
                        <footer>José R. - Uberlândia - MG | há 1 semana</footer>
                        <div class="complaint-card-footer">
                            <span class="response-tag"><svg viewBox="0 0 24 24"><path d="M12,2A2,2 0 0,1 14,4C14,4.24 13.96,4.47 13.88,4.68C15.96,5.5 17.5,7.55 17.5,10V11H18.5C19.88,11 21,12.12 21,13.5V17.5C21,18.88 19.88,20 18.5,20H5.5C4.12,20 3,18.88 3,17.5V13.5C3,12.12 4.12,11 5.5,11H6.5V10C6.5,7.55 8.04,5.5 10.12,4.68C10.04,4.47 10,4.24 10,4A2,2 0 0,1 12,2M12,4A2,2 0 0,0 10,6A2,2 0 0,0 12,8A2,2 0 0,0 14,6A2,2 0 0,0 12,4Z"></path></svg> Respondida</span>
                            <div class="reactions">
                                <div class="reaction-item reaction-like"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="m7.48 18.35 3.1 2.4c.4.4 1.3.6 1.9.6h3.8c1.2 0 2.5-.9 2.8-2.1l2.4-7.3c.5-1.4-.4-2.6-1.9-2.6h-4c-.6 0-1.1-.5-1-1.2l.5-3.2c.2-.9-.4-1.9-1.3-2.2-.8-.3-1.8.1-2.2.7l-4.1 6.1" stroke="currentColor" stroke-width="2" stroke-miterlimit="10"></path><path d="M2.38 18.35v-9.8c0-1.4.6-1.9 2-1.9h1c1.4 0 2 .5 2 1.9v9.8c0 1.4-.6 1.9-2 1.9h-1c-1.4 0-2-.5-2-1.9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                <div class="reaction-item reaction-also"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2"></path><path d="M14 10V4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v2"></path><path d="M10 10.5V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2v8"></path><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"></path></svg></div>
                                <div class="reaction-item reaction-angry"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16 16s-1.5-2-4-2-4 2-4 2"></path><path d="M7.5 8 10 9"></path><path d="m14 9 2.5-1"></path></svg></div>
                                <span>deixe sua reação</span>
                            </div>
                        </div>
                    </div>
                    <div class="complaint-card">
                        <h5>Excelente carro e condições de pagamento!</h5>
                        <p>Estava procurando um carro específico e encontrei na Copart Leilões. Além do equipamento estar em ótimo estado, o processo de compra foi muito claro...</p>
                        <footer>Antônio F. - Cuiabá - MT | há 3 semanas</footer>
                        <div class="complaint-card-footer">
                            <span class="response-tag"><svg viewBox="0 0 24 24"><path d="M12,2A2,2 0 0,1 14,4C14,4.24 13.96,4.47 13.88,4.68C15.96,5.5 17.5,7.55 17.5,10V11H18.5C19.88,11 21,12.12 21,13.5V17.5C21,18.88 19.88,20 18.5,20H5.5C4.12,20 3,18.88 3,17.5V13.5C3,12.12 4.12,11 5.5,11H6.5V10C6.5,7.55 8.04,5.5 10.12,4.68C10.04,4.47 10,4.24 10,4A2,2 0 0,1 12,2M12,4A2,2 0 0,0 10,6A2,2 0 0,0 12,8A2,2 0 0,0 14,6A2,2 0 0,0 12,4Z"></path></svg> Respondida</span>
                            <div class="reactions">
                                <div class="reaction-item reaction-like"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="m7.48 18.35 3.1 2.4c.4.4 1.3.6 1.9.6h3.8c1.2 0 2.5-.9 2.8-2.1l2.4-7.3c.5-1.4-.4-2.6-1.9-2.6h-4c-.6 0-1.1-.5-1-1.2l.5-3.2c.2-.9-.4-1.9-1.3-2.2-.8-.3-1.8.1-2.2.7l-4.1 6.1" stroke="currentColor" stroke-width="2" stroke-miterlimit="10"></path><path d="M2.38 18.35v-9.8c0-1.4.6-1.9 2-1.9h1c1.4 0 2 .5 2 1.9v9.8c0 1.4-.6 1.9-2 1.9h-1c-1.4 0-2-.5-2-1.9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></div>
                                <div class="reaction-item reaction-also"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2"></path><path d="M14 10V4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v2"></path><path d="M10 10.5V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2v8"></path><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"></path></svg></div>
                                <div class="reaction-item reaction-angry"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M16 16s-1.5-2-4-2-4 2-4 2"></path><path d="M7.5 8 10 9"></path><path d="m14 9 2.5-1"></path></svg></div>
                                <span>deixe sua reação</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ra-v2-right-col">
                    <div class="ra-v2-card">
                        <h4>Desempenho da Copart do Brasil</h4>
                        <ul>
                            <li><img src="imagens/reclame/mdibullhornicon.svg"> Esta empresa resolveu 888 reclamações em 5 anos.</li>
                            <li><img src="imagens/reclame/mdicheckcircleicon.svg"> Respondeu 91.2% das reclamações recebidas.</li>
                            <li><img src="imagens/reclame/mdicommentquestionicon.svg"> Há 4 reclamações aguardando resposta.</li>
                            <li><img src="imagens/reclame/handshakeicon.svg"> 93.6% voltariam a fazer negócio.</li>
                            <li><img src="imagens/reclame/mdistarboxicon.svg"> A empresa resolveu 97% das reclamações recebidas.</li>
                            <li><img src="imagens/reclame/mditimericon.svg"> O tempo médio de resposta é 24 dias e 6 horas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo-column">
                <img src="imagens/logo.png" alt="Copart Logo">
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
                <ul>
                    <li><a href="#">Sobre Nós</a></li>
                    <li><a href="#">Nossa História</a></li>
                    <li><a href="#">Carreiras</a></li>
                    <li><a href="#">Imprensa</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Serviços</h4>
                <ul>
                    <li><a href="#">Leilões de Carros</a></li>
                    <li><a href="#">Leilões de Motos</a></li>
                    <li><a href="#">Rastreamento</a></li>
                    <li><a href="#">Suporte</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Suporte</h4>
                <ul>
                    <li><a href="#">Central de Ajuda</a></li>
                    <li><a href="#">Contato</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Termos de Uso</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Política de Privacidade</a></li>
                    <li><a href="#">Termos e Condições</a></li>
                    <li><a href="#">Cookies</a></li>
                    <li><a href="#">Compliance</a></li>
                </ul>
            </div>
        </div>

        <div class="sub-footer">
            <div class="container">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <p>&copy; 2024 Copart Leilões. Todos os direitos reservados.</p>
                    <ul class="sub-footer-links">
                        <li><a href="#">Privacidade</a></li>
                        <li><a href="#">Termos</a></li>
                        <li><a href="#">Cookies</a></li>
                        <li><a href="#">Acessibilidade</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <a href="#" class="back-to-top-btn" id="backToTopBtn">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
        </svg>
        Topo
    </a>

    <script>
        // Controle do carrossel de vídeos
        let slideIndex = 1;
        showSlide(slideIndex);

        function changeSlide(n) {
            showSlide(slideIndex += n);
        }

        function currentSlide(n) {
            showSlide(slideIndex = n);
        }

        function showSlide(n) {
            let slides = document.getElementsByClassName("video-carousel-slide");
            let dots = document.getElementsByClassName("video-carousel-dot");
            
            if (n > slides.length) { slideIndex = 1; }
            if (n < 1) { slideIndex = slides.length; }
            
            for (let i = 0; i < slides.length; i++) {
                slides[i].classList.remove("active");
            }
            
            for (let i = 0; i < dots.length; i++) {
                dots[i].classList.remove("active");
            }
            
            slides[slideIndex - 1].classList.add("active");
            dots[slideIndex - 1].classList.add("active");
        }

        // Auto-play do carrossel (opcional)
        setInterval(function() {
            changeSlide(1);
        }, 10000); // Muda slide a cada 10 segundos

        // Controle do menu hambúrguer
        function toggleMobileMenu() {
            const hamburger = document.querySelector('.hamburger-menu');
            const mobileNav = document.getElementById('mobileNav');
            
            hamburger.classList.toggle('active');
            mobileNav.classList.toggle('active');
            
            // Previne scroll do body quando menu está aberto
            if (mobileNav.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        function closeMobileMenu() {
            const hamburger = document.querySelector('.hamburger-menu');
            const mobileNav = document.getElementById('mobileNav');
            
            hamburger.classList.remove('active');
            mobileNav.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Fecha menu mobile ao clicar fora
        document.addEventListener('click', function(event) {
            const hamburger = document.querySelector('.hamburger-menu');
            const mobileNav = document.getElementById('mobileNav');
            
            if (!hamburger.contains(event.target) && !mobileNav.contains(event.target)) {
                if (mobileNav.classList.contains('active')) {
                    closeMobileMenu();
                }
            }
        });

        // Fecha menu mobile ao redimensionar para desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });

        // Controle do botão "Voltar ao Topo"
        window.addEventListener('scroll', function() {
            const backToTopBtn = document.getElementById('backToTopBtn');
            
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });

        // Smooth scroll para o topo
        document.getElementById('backToTopBtn').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Smooth scroll para âncoras
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Otimização para touch devices
        if ('ontouchstart' in window) {
            document.body.classList.add('touch-device');
        }

        // Lazy loading para iframes (opcional)
        const observerOptions = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const iframe = entry.target.querySelector('iframe');
                    if (iframe && !iframe.src) {
                        iframe.src = iframe.dataset.src;
                    }
                }
            });
        }, observerOptions);

        // Observa slides para lazy loading
        document.querySelectorAll('.video-carousel-slide').forEach(slide => {
            observer.observe(slide);
        });
    </script>

</body>
</html>