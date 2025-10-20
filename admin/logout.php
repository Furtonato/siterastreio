<?php
// 1. Inicia a sessão para poder manipulá-la.
session_start();

// 2. Destrói TODAS as informações da sessão.
// Isso efetivamente "esquece" que o usuário estava logado.
session_destroy();

// 3. Redireciona o usuário de volta para a página de login do admin.
header('Location: index.php');
exit();
?>