<?php
session_start();
// Se o arquivo login.php está dentro da pasta /admin/, este caminho está correto.
// Se o login.php está na raiz, o caminho seria 'admin/db.php'
require_once 'db.php'; 

// Pega os dados do formulário
$usuario_digitado = $_POST['usuario'];
$senha_digitada = $_POST['senha'];

// Validação básica
if (empty($usuario_digitado) || empty($senha_digitada)) {
    $_SESSION['login_error'] = 'Usuário e senha são obrigatórios.';
    header('Location: index.php');
    exit();
}

// Prepara a consulta para buscar o admin pelo nome de usuário
$sql = "SELECT id, usuario, senha FROM administrators WHERE usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $usuario_digitado);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 1) {
    // Se encontrou o usuário, pega os dados dele
    $admin = $resultado->fetch_assoc();

    // Verifica se a senha digitada corresponde à senha criptografada no banco
    if (password_verify($senha_digitada, $admin['senha'])) {
        // Login bem-sucedido!
        
        // Limpa qualquer erro antigo
        unset($_SESSION['login_error']);

        // Guarda na sessão que o admin está logado E QUAL é o ID dele
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id']; 

        // Redireciona para o painel
        header('Location: dashboard.php');
        exit();
    }
}

// Se o usuário não foi encontrado ou a senha estava errada
$_SESSION['login_error'] = 'Usuário ou senha inválidos.';
header('Location: index.php');
exit();
?>