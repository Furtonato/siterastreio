<?php
session_start();
require_once 'db.php';
// Lógica de segurança aqui...

// Lógica para salvar as configurações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cor_primaria = $_POST['cor_primaria'];
    // Query inteligente que atualiza se a chave 'cor_primaria' já existir, ou insere se não existir.
    $sql = "INSERT INTO configuracoes (chave, valor) VALUES ('cor_primaria', ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $cor_primaria);
    $stmt->execute();
}

// Lógica para buscar a cor atual
$cor_atual = $conexao->query("SELECT valor FROM configuracoes WHERE chave = 'cor_primaria'")->fetch_assoc()['valor'] ?? '#007bff';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Personalizar Site</title>
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
    <div class="admin-dashboard-container">
        <h1>Personalizar Aparência (Whitelabel)</h1>
        <form method="POST">
            <div class="form-group">
                <label for="cor_primaria">Cor Primária:</label>
                <input type="color" id="cor_primaria" name="cor_primaria" value="<?php echo $cor_atual; ?>">
            </div>
            <button type="submit">Salvar Cor</button>
        </form>
    </div>
</body>
</html>