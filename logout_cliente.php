<?php
// Inicia a sessão para poder destruí-la
session_start();

// Destrói todas as informações da sessão do cliente
session_destroy();

// Redireciona de volta para a página de login
header('Location: index.php');
exit();
?>