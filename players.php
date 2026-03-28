<?php
header('Content-Type: application/json');

// Configurações do servidor SA-MP
$server_ip   = "190.102.40.7"; // seu IP
$server_port = 8821;              // porta do server

// Função para pegar informações do servidor SA-MP
function query_samp($ip, $port) {
    $sock = @fsockopen('udp://'.$ip, $port, $errno, $errstr, 2);
    if (!$sock) return false;

    // Pacote para query de informações básicas
    fwrite($sock, "SAMP".chr(strtok($ip, ".")).chr(strtok(".")).chr(strtok(".")).chr(strtok(".")).chr($port & 0xFF).chr($port >> 8).'i');

    $data = fread($sock, 2048);
    fclose($sock);

    if (!$data) return false;

    // Pega jogadores online (bytes 11 e 12)
    $players = ord($data[11]) + (ord($data[12]) << 8);

    return [
        "players1" => $players,
        "ping"     => rand(60, 180), // simulação de ping
        "doubling" => 1,
        "new"      => 1
    ];
}

// Pega status do servidor
$status = query_samp($server_ip, $server_port);

if (!$status) {
    $status = [
        "players1" => 0,
        "ping"     => 999,
        "doubling" => 0,
        "new"      => 0
    ];
}

// Retorna em JSON no formato pedido
echo json_encode(["servers" => [$status]], JSON_UNESCAPED_UNICODE);