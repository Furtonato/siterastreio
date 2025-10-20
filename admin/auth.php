<?php
session_start();

// --- DADOS DE LOGIN DO ADMINISTRADOR ---
// Você pode alterar o usuário e a senha aqui
$admin_user = 'admin';
$admin_pass = 'admin123';
// ------------------------------------

// Pega os dados enviados pelo formulário
$username_posted = $_POST['username'];
$password_posted = $_POST['password'];

// Verifica se o usuário e a senha estão corretos
if ($username_posted === $admin_user && $password_posted === $admin_pass) {
    // Se estiverem corretos, cria a sessão e redireciona para o painel
    $_SESSION['admin_logged_in'] = true;
    header('Location: dashboard.php');
    exit();
} else {
    // Se estiverem errados, cria uma mensagem de erro e volta para o login
    $_SESSION['login_error'] = 'Usuário ou Senha inválidos.';
    header('Location: index.php');
    exit();
}
?>