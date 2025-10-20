<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Proteção: se não estiver logado, redireciona para o login do admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
    <div class="admin-container">
        <nav class="sidebar">
            <div class="sidebar-header">
                Painel Admin
            </div>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="dashboard.php">
                        <svg viewBox="0 0 24 24"><path d="M13,3V9H21V3M13,21H21V11H13M3,21H11V15H3M3,13H11V3H3V13Z"></path></svg>
                        <span>Painel</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gerenciar_clientes.php">
                        <svg viewBox="0 0 24 24"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"></path></svg>
                        <span>Gerenciar Clientes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gerenciar_vitrine.php">
                        <svg viewBox="0 0 24 24"><path d="M18.92,6.01C18.72,5.42 18.16,5 17.5,5H6.5C5.84,5 5.28,5.42 5.08,6.01L3,12V18C3,18.55 3.45,19 4,19H5C5.55,19 6,18.55 6,18V17H18V18C18,18.55 18.45,19 19,19H20C20.55,19 21,18.55 21,18V12L18.92,6.01M6.5,15C5.67,15 5,14.33 5,13.5C5,12.67 5.67,12 6.5,12C7.33,12 8,12.67 8,13.5C8,14.33 7.33,15 6.5,15M17.5,15C16.67,15 16,14.33 16,13.5C16,12.67 16.67,12 17.5,12C18.33,12 19,12.67 19,13.5C19,14.33 18.33,15 17.5,15M5,10L6.5,6H17.5L19,10H5Z"></path></svg>
                        <span>Gerenciar Vitrine</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gerenciar_funcionarios.php">
                        <svg viewBox="0 0 24 24"><path d="M15,14C17.67,14 23,15.33 23,18V20H7V18C7,15.33 12.33,14 15,14M15,12A4,4 0 0,0 19,8A4,4 0 0,0 15,4A4,4 0 0,0 11,8A4,4 0 0,0 15,12M5,13.28L1,10.74L2.46,9.28L5,11.82L9.46,7.36L10.92,8.82L5,14.74"></path></svg>
                        <span>Gerenciar Funcionários</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gerenciar_depoimentos.php">
                        <svg viewBox="0 0 24 24"><path d="M22,4C22,2.89 21.1,2 20,2H4C2.9,2 2,2.89 2,4V16C2,17.1 2.9,18 4,18H18L22,22V4Z"></path></svg>
                        <span>Gerenciar Depoimentos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gerenciar_motorista.php">
                        <svg viewBox="0 0 24 24"><path d="M5,16C5,16.55 5.45,17 6,17H18C18.55,17 19,16.55 19,16V15H5V16M19,8H5A2,2 0 0,0 3,10V12A2,2 0 0,0 5,14H19A2,2 0 0,0 21,12V10A2,2 0 0,0 19,8M7,12.5A1.5,1.5 0 0,1 5.5,11A1.5,1.5 0 0,1 7,9.5A1.5,1.5 0 0,1 8.5,11A1.5,1.5 0 0,1 7,12.5M17,12.5A1.5,1.5 0 0,1 15.5,11A1.5,1.5 0 0,1 17,9.5A1.5,1.5 0 0,1 18.5,11A1.5,1.5 0 0,1 17,12.5M12,6C13.66,6 15,4.66 15,3C15,1.34 13.66,0 12,0C10.34,0 9,1.34 9,3C9,4.66 10.34,6 12,6Z"></path></svg>
                        <span>Gerenciar Motorista</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="configurar_patio.php">
                        <svg viewBox="0 0 24 24"><path d="M12,2C15.86,2 19,5.13 19,9C19,14.25 12,22 12,22C12,22 5,14.25 5,9C5,5.13 8.13,2 12,2M12,11.5A2.5,2.5 0 0,0 14.5,9A2.5,2.5 0 0,0 12,6.5A2.5,2.5 0 0,0 9.5,9A2.5,2.5 0 0,0 12,11.5Z"></path></svg>
                        <span>Localização do Pátio</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="personalizacao.php">
                        <svg viewBox="0 0 24 24"><path d="M12,16A2,2 0 0,1 14,18A2,2 0 0,1 12,20A2,2 0 0,1 10,18A2,2 0 0,1 12,16M12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10M12,4A2,2 0 0,1 14,6A2,2 0 0,1 12,8A2,2 0 0,1 10,6A2,2 0 0,1 12,4Z"></path></svg>
                        <span>Personalização</span>
                    </a>
                </li>
            </ul>
            <ul class="sidebar-nav nav-footer">
                <li class="nav-item">
                    <a href="logout.php">
                        <svg viewBox="0 0 24 24"><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"></path></svg>
                        <span>Sair</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <main class="main-content">