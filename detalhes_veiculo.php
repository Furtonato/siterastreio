<?php
// Inclui o arquivo de conexão
require_once 'admin/db.php';

// Pega o ID do veículo da URL. Se não houver, redireciona.
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: veiculos.php");
    exit();
}
$id_veiculo = $_GET['id'];

// Busca todos os dados do veículo específico no banco de dados
$stmt = $conexao->prepare("SELECT * FROM vitrine_veiculos WHERE id = ? AND status = 'disponivel'");
$stmt->bind_param("i", $id_veiculo);
$stmt->execute();
$resultado = $stmt->get_result();

// Se não encontrar nenhum veículo com esse ID, redireciona
if ($resultado->num_rows === 0) {
    header("Location: veiculos.php");
    exit();
}
$veiculo = $resultado->fetch_assoc();

// Busca o logo principal nas configurações para o cabeçalho
$resultado_logo = $conexao->query("SELECT valor_config FROM configuracoes WHERE nome_config = 'url_logo_principal'");
$url_logo_principal = $resultado_logo->fetch_assoc()['valor_config'] ?? 'imagens/logo.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($veiculo['titulo']); ?> - Copart Leilões</title>
    <link rel="stylesheet" href="css/dynamic_style.php">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&family=Roboto:wght@400;700&display=swap');
        body { font-family: 'Roboto', sans-serif; margin: 0; background-color: #f4f7f9; color: #333; }
        .container { max-width: 1140px; margin: 0 auto; padding: 20px 15px; }
        h1, h2, h3 { font-family: 'Montserrat', sans-serif; font-weight: 700; color: #003D7A; }

        /* Estilos do cabeçalho (pode ser copiado do index.php se quiser) */
        .header { background: linear-gradient(to right, #004080, #001f3f); padding: 20px 0; color: #fff; }
        .container-header { display: flex; align-items: center; justify-content: space-between; max-width: 1140px; margin: 0 auto; padding: 0 15px; }
        .logo-header-img { max-width: 130px; }
        .header-nav a { color: #fff; text-decoration: none; margin-left: 20px; font-family: 'Montserrat'; }
        .btn-rastrear { background-color: #f39c12; padding: 10px 20px; border-radius: 5px; font-weight: bold; }

        /* Layout da Página de Detalhes */
        .details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }
        .main-content, .sidebar-content {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        /* Galeria de Fotos */
        .gallery-main-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .gallery-thumbnails {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 10px;
        }
        .gallery-thumbnails img {
            width: 100%;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.3s;
        }
        .gallery-thumbnails img:hover, .gallery-thumbnails img.active {
            border-color: #0056b3;
        }

        /* Descrição e Opcionais */
        .description, .optionals {
            margin-top: 30px;
        }
        .description p {
            line-height: 1.7;
            color: #555;
        }
        .optionals ul {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            color: #444;
        }
        .optionals li {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
        }

        /* Sidebar com Detalhes */
        .sidebar-content h1 {
            font-size: 1.8em;
            margin-top: 0;
        }
        .sidebar-content .subtitulo {
            font-size: 0.9em;
            color: #6c757d;
            margin-top: -10px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .sidebar-content .preco {
            font-size: 2.5em;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 25px;
        }
        .quick-details {
            list-style: none;
            padding: 0;
            border-top: 1px solid #eee;
        }
        .quick-details li {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .quick-details strong { color: #555; }

        .contact-button {
            display: block;
            width: 100%;
            padding: 15px;
            margin-top: 25px;
            background-color: #28a745;
            color: #fff;
            text-align: center;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .contact-button:hover {
            background-color: #218838;
        }

        /* Responsividade */
        @media (max-width: 992px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 768px) {
            .optionals ul { grid-template-columns: 1fr; }
            .sidebar-content .preco { font-size: 2em; }
        }

    </style>
</head>
<body>
    <header class="header">
        <div class="container-header">
            <a href="index.php"><img src="<?php echo htmlspecialchars($url_logo_principal); ?>" alt="Copart Logo" class="logo-header-img"></a>
            <nav class="header-nav">
                <a href="index.php">Início</a>
                <a href="veiculos.php">Veículos</a>
                <a href="funcionarios.php">Equipe</a>
                <a href="rastrear.php" class="btn-rastrear">Rastrear</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="details-grid">

            <div class="main-content">
                <div class="gallery">
                    <div class="gallery-main-image">
                        <img id="mainImage" src="<?php echo htmlspecialchars($veiculo['foto_principal']); ?>" alt="<?php echo htmlspecialchars($veiculo['titulo']); ?>">
                    </div>
                    <?php 
                        // Transforma o texto das URLs da galeria em um array
                        $fotos_galeria = !empty($veiculo['fotos_galeria']) ? explode("\n", trim($veiculo['fotos_galeria'])) : [];
                        // Adiciona a foto principal no início da galeria se ela não estiver lá
                        if (!in_array($veiculo['foto_principal'], $fotos_galeria)) {
                            array_unshift($fotos_galeria, $veiculo['foto_principal']);
                        }
                    ?>
                    <?php if (!empty($fotos_galeria)): ?>
                        <div class="gallery-thumbnails">
                            <?php foreach ($fotos_galeria as $index => $foto_url): ?>
                                <img src="<?php echo htmlspecialchars(trim($foto_url)); ?>" onclick="changeImage('<?php echo htmlspecialchars(trim($foto_url)); ?>', this)" class="<?php echo $index === 0 ? 'active' : ''; ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($veiculo['descricao'])): ?>
                    <div class="description">
                        <h2>Descrição</h2>
                        <p><?php echo nl2br(htmlspecialchars($veiculo['descricao'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php 
                    // Transforma o texto dos opcionais em um array
                    $opcionais = !empty($veiculo['opcionais']) ? explode(',', trim($veiculo['opcionais'])) : [];
                ?>
                <?php if (!empty($opcionais)): ?>
                    <div class="optionals">
                        <h2>Opcionais</h2>
                        <ul>
                            <?php foreach ($opcionais as $opcional): ?>
                                <li><?php echo htmlspecialchars(trim($opcional)); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="sidebar-content">
                <h1><?php echo htmlspecialchars($veiculo['titulo']); ?></h1>
                <?php if (!empty($veiculo['subtitulo'])): ?>
                    <p class="subtitulo"><?php echo htmlspecialchars($veiculo['subtitulo']); ?></p>
                <?php endif; ?>
                <div class="preco">R$ <?php echo number_format($veiculo['preco'], 2, ',', '.'); ?></div>
                
                <ul class="quick-details">
                    <li><strong>Ano:</strong> <span><?php echo htmlspecialchars($veiculo['ano_fabricacao']) . '/' . htmlspecialchars($veiculo['ano_modelo']); ?></span></li>
                    <li><strong>KM:</strong> <span><?php echo number_format($veiculo['quilometragem'], 0, '', '.'); ?></span></li>
                    <li><strong>Câmbio:</strong> <span><?php echo htmlspecialchars($veiculo['cambio']); ?></span></li>
                    <li><strong>Local:</strong> <span><?php echo htmlspecialchars($veiculo['cidade']) . ' - ' . htmlspecialchars($veiculo['estado']); ?></span></li>
                </ul>

                <a href="#" class="contact-button">Entrar em Contato</a>
            </div>

        </div>
    </div>
    
    <script>
        function changeImage(newSrc, thumbElement) {
            // Troca a imagem principal
            document.getElementById('mainImage').src = newSrc;
            
            // Remove a classe 'active' de todas as miniaturas
            let thumbnails = document.querySelectorAll('.gallery-thumbnails img');
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            
            // Adiciona a classe 'active' na miniatura clicada
            thumbElement.classList.add('active');
        }
    </script>
</body>
</html>