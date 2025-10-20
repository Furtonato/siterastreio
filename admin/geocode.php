<?php
// Define que a resposta será em formato JSON
header('Content-Type: application/json');

// Verifica se um endereço foi enviado
if (!isset($_GET['address']) || empty($_GET['address'])) {
    echo json_encode([]); // Retorna um array vazio se não houver endereço
    exit();
}

$address = $_GET['address'];
$encoded_address = urlencode($address);

// Monta a URL da API do Nominatim
$url = "https://nominatim.openstreetmap.org/search?format=json&q={$encoded_address}";

// Prepara a chamada para a API usando cURL (método mais robusto)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// IMPORTANTE: Define um "User-Agent", exigido por muitas APIs públicas para evitar abusos
curl_setopt($ch, CURLOPT_USERAGENT, 'CopartRastreamento/1.0');
curl_setopt($ch, CURLOPT_HEADER, 0);

// Executa a chamada e obtém a resposta
$response = curl_exec($ch);
curl_close($ch);

// Retorna a resposta da API diretamente para o JavaScript
echo $response;
?>