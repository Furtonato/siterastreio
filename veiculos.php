<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'admin/db.php';

// Busca todos os veículos marcados como 'disponivel' na vitrine
$veiculos = $conexao->query("SELECT * FROM vitrine_veiculos WHERE status = 'disponivel' ORDER BY id DESC");

// Busca o logo principal nas configurações
$resultado_logo = $conexao->query("SELECT valor FROM configuracoes WHERE chave = 'url_logo_principal'");
$url_logo_principal = $resultado_logo->fetch_assoc()['valor'] ?? 'imagens/logo.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossos Veículos - Copart Leilões</title>
    <link rel="stylesheet" href="css/dynamic_style.php">
    <style>
        /* --- ESTILOS ESPECÍFICOS PARA A PÁGINA DA VITRINE --- */
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&family=Roboto:wght@400;700&display=swap');

        body { 
            font-family: 'Roboto', sans-serif; 
            margin: 0; 
            background-color: #f8f9fa; 
            color: #343a40; 
        }

        .container { 
            max-width: 1140px; 
            margin: 0 auto; 
            padding: 20px 15px; 
        }

        h1, h2, h3, h4 { 
            font-family: 'Montserrat', sans-serif; 
            font-weight: 700; 
        }
        
        .page-title {
            text-align: center;
            font-size: 2.5em;
            color: #003D7A;
            margin-bottom: 30px;
        }

        /* Grade da Vitrine */
        .vitrine-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        /* Card do Veículo */
        .vehicle-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 61, 122, 0.15);
        }

        .vehicle-card .image-container {
            width: 100%;
            padding-top: 66.66%; /* Proporção 3:2 */
            position: relative;
            background-color: #e9ecef;
        }
        .vehicle-card .image-container img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vehicle-card .content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        
        .vehicle-card h3 {
            font-size: 1.1em;
            margin: 0 0 5px 0;
            color: #003D7A;
        }

        .vehicle-card .subtitulo {
            font-size: 0.8em;
            color: #6c757d;
            margin: 0 0 15px 0;
            text-transform: uppercase;
            font-weight: 500;
        }

        .vehicle-card .preco {
            font-size: 1.6em;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 15px;
        }

        .vehicle-card .details {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #e9ecef;
            padding-top: 15px;
            margin-top: auto; /* Empurra para o final do card */
            font-size: 0.9em;
        }
        .vehicle-card .details span {
            font-weight: 500;
            color: #495057;
        }
        
        .no-vehicles {
            text-align: center;
            padding: 50px 20px;
            font-size: 1.2em;
            color: #555;
        }

    </style>
</head>
<body>

    <header class="header">
        <div class="container-header">
            <div class="logo-e-texto">
                <img src="<?php echo htmlspecialchars($url_logo_principal); ?>" alt="Copart Logo" class="logo-header-img">
                <div class="header-text-content">
                    <h1 class="site-title">COPART LEILÕES</h1>
                    <p class="site-slogan">Veiculos e Motocicletas com as Melhores Condições do Mercado.</p>
                </div>
            </div>
            
            <nav class="header-nav">
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="veiculos.php">Veículos</a></li>
                    <li><a href="#depoimentos-video">Depoimentos</a></li>
                    <li><a href="funcionarios.php">Equipe</a></li>
                    <li><a href="rastrear.php" class="btn-rastrear">Rastrear</a></li>
                </ul>
            </nav>
            
            </div>
    </header>

    <div class="container">
        <h1 class="page-title">Nossos Veículos</h1>

        <div class="vitrine-grid">

            <?php if ($veiculos && $veiculos->num_rows > 0): ?>
                <?php while($veiculo = $veiculos->fetch_assoc()): ?>
                    <a href="detalhes_veiculo.php?id=<?php echo $veiculo['id']; ?>" class="vehicle-card">
                        <div class="image-container">
                            <img src="<?php echo htmlspecialchars($veiculo['foto_principal']); ?>" alt="<?php echo htmlspecialchars($veiculo['titulo']); ?>">
                        </div>
                        <div class="content">
                            <h3><?php echo htmlspecialchars($veiculo['titulo']); ?></h3>
                            <?php if (!empty($veiculo['subtitulo'])): ?>
                                <p class="subtitulo"><?php echo htmlspecialchars($veiculo['subtitulo']); ?></p>
                            <?php endif; ?>
                            <div class="preco">R$ <?php echo number_format($veiculo['preco'], 2, ',', '.'); ?></div>
                            <div class="details">
                                <span><?php echo htmlspecialchars($veiculo['ano_fabricacao']); ?>/<?php echo htmlspecialchars($veiculo['ano_modelo']); ?></span>
                                <span><?php echo number_format($veiculo['quilometragem'], 0, '', '.'); ?> km</span>
                                <span><?php echo htmlspecialchars($veiculo['cambio']); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-vehicles">
                    <p>Nenhum veículo disponível na vitrine no momento.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>

    </body>
</html>