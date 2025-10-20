<?php
// Inicia a sessão para podermos exibir mensagens de erro
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastreamento de Veículos | Copart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            /* FUNDO EM DEGRADÊ COM CORES DA COPART */
            background: linear-gradient(135deg, #0d47a1 0%, #263238 100%);
        }

        .login-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            padding: 50px 40px;
            
            /* EFEITO DE VIDRO (GLASSMORPHISM) */
            background: rgba(0, 0, 10, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);

            color: #fff;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo-container img {
            max-width: 180px; /* Ajuste o tamanho do logo se necessário */
        }
        
        /* MENSAGEM DE ERRO */
        .client-error-message {
            background: rgba(255, 0, 0, 0.6);
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: rgba(0, 0, 10, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group input:focus {
            outline: none;
            border-color: #1e88e5; /* Azul mais claro no foco */
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .remember-me label {
             margin-left: 8px;
             cursor: pointer;
        }

        .forgot-password {
            color: #1e88e5;
            text-decoration: none;
        }

        /* BOTÃO AZUL COPART */
        .login-button {
            width: 100%;
            padding: 15px;
            background: #0056b3;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background: #003D7A;
        }
    </style>
</head>
<body>
    
    <div class="login-container">
        <div class="logo-container">
            <img src="imagens/logo.png" alt="Copart Logo">
        </div>

        <?php
            // Verifica se existe uma mensagem de erro na sessão e a exibe
            if (isset($_SESSION['login_error'])) {
                echo '<p class="client-error-message">' . $_SESSION['login_error'] . '</p>';
                // Limpa a mensagem da sessão
                unset($_SESSION['login_error']);
            }
        ?>

        <form action="login_cliente.php" method="post">
            <div class="input-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" id="username" name="username" placeholder="Digite seu CPF" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" id="password" name="password" placeholder="Senha" required>
            </div>

            <div class="options">
                <div class="remember-me">
                    <input type="checkbox" id="remember">
                    <label for="remember">Lembrar login</label>
                </div>
                <a href="#" class="forgot-password">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="login-button">Entrar</button>
        </form>
    </div>

</body>
</html>