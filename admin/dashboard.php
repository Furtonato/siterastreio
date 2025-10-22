<?php
ini_set('display_errors', 1); // Força a exibição de erros (temporário)
error_reporting(E_ALL); // Mostra todos os tipos de erro (temporário)

// Corrigido o caminho para db.php
require_once('db.php'); 

// Inclui o header DEPOIS de conectar ao DB e iniciar sessão (se necessário no header)
include 'admin_header.php'; 
?>

<head>
    <title>Painel de Controle</title>
</head>

<div class="page-header">
    <h1>Painel de Controle</h1>
</div>

<div class="card">
    <h2>Bem-vindo!</h2>
    <p>Selecione uma das opções no menu à esquerda para começar.</p>
</div>

<?php include 'admin_footer.php'; ?>