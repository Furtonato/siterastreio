<?php
// Configurações do banco de dados
$servidor = 'localhost';     // O servidor onde o banco de dados está (geralmente localhost)
$usuario_db = 'root';        // O usuário do banco de dados (padrão do XAMPP é 'root')
$senha_db = '';              // A senha do banco de dados (padrão do XAMPP é em branco)
$banco = 'rastreamento_db';  // O nome do banco de dados que criamos

// Cria a conexão usando o driver MySQLi
$conexao = new mysqli($servidor, $usuario_db, $senha_db, $banco);

// Checa se a conexão teve algum erro
if ($conexao->connect_error) {
    // Se houver um erro, o script para de ser executado e exibe a mensagem de erro.
    die("Falha na conexão com o banco de dados: " . $conexao->connect_error);
}

// Garante que a comunicação entre PHP e MySQL seja em UTF-8 para suportar acentos
$conexao->set_charset("utf8mb4");

// Se o script chegou até aqui, a conexão foi bem-sucedida.
// A variável $conexao pode ser usada em outros arquivos para interagir com o banco.
?>