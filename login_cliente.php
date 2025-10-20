<?php
// Inicia a sessão
session_start();

// Inclui o arquivo de conexão com o banco de dados.
require_once 'admin/db.php';

// Pega o CPF e a senha enviados pelo formulário
$cpf_input = $_POST['username'];
$senha = $_POST['password'];

// Limpa o CPF, removendo tudo que não for número
$cpf = preg_replace('/[^0-9]/', '', $cpf_input);

// Validação básica para ver se os campos não estão vazios
if (empty($cpf) || empty($senha)) {
    $_SESSION['login_error'] = "CPF e Senha são obrigatórios.";
    header('Location: rastrear.php');
    exit();
}

// Prepara a consulta SQL para buscar o usuário pelo CPF já limpo
$sql = "SELECT id, nome, senha FROM usuarios WHERE cpf = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $cpf);
$stmt->execute();
$resultado = $stmt->get_result();

// Verifica se encontrou algum usuário com o CPF informado
if ($resultado->num_rows === 1) {
    // Se encontrou, pega os dados do usuário
    $usuario = $resultado->fetch_assoc();

    // Verifica se a senha digitada corresponde ao hash salvo no banco de dados
    if (password_verify($senha, $usuario['senha'])) {
        // Se a senha estiver correta, o login é bem-sucedido!
        
        unset($_SESSION['login_error']);

        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_name'] = $usuario['nome'];

        // Redireciona para o dashboard do cliente
        header("Location: dashboard.php");
        exit();
    }
}

$_SESSION['login_error'] = "CPF ou Senha inválidos.";
header("Location: rastrear.php");
exit();

?>