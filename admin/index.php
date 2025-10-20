<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Acesso Restrito</title>
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
            background: #001f3f; /* Azul escuro sólido */
        }

        .login-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            padding: 50px 40px;
            
            background: #003366; /* Um tom de azul um pouco mais claro */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);

            color: #fff;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo-container img {
            max-width: 150px; /* Ajuste o tamanho da imagem 171 se necessário */
        }
        
        /* MENSAGEM DE ERRO */
        .admin-error-message {
            background: rgba(220, 53, 69, 0.8);
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
            border-color: #3498db; 
        }

        .login-button {
            width: 100%;
            padding: 15px;
            background: #3498db;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background: #2980b9;
        }
        
        /***************************************/
        /* --- CSS PARA A ASSINATURA ABAIXO --- */
        /***************************************/
        .creator-credit {
            text-align: center;
            color: rgba(255, 255, 255, 0.6); /* Cor branca com um pouco de transparência */
            margin-top: 35px; /* Espaço entre o botão e o seu nome */
            font-size: 14px;
        }
    </style>
</head>
<body>
    
    <div class="login-container">
        <div class="logo-container">
            <img src="../imagens/logo-171.png" alt="Logo 171">
        </div>

        <?php
            // Exibe a mensagem de erro, se houver
            if (isset($_SESSION['login_error'])) {
                echo '<p class="admin-error-message">' . $_SESSION['login_error'] . '</p>';
                unset($_SESSION['login_error']);
            }
        ?>

        <form action="login.php" method="post">
            <div class="input-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" id="username" name="usuario" placeholder="Usuário" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" id="password" name="senha" placeholder="Senha" required>
            </div>

            <button type="submit" class="login-button">Entrar</button>
        </form>

        <p class="creator-credit">by muniz</p>

    </div>

</body>
</html>