<?php
header('Content-Type: application/json');

$server_ip   = "135.148.164.122"; // seu IP correto
$server_port = 14077;             // sua porta

function query_samp($ip, $port) {
    $packet = "SAMP";
    foreach (explode('.', $ip) as $octet) {
        $packet .= chr($octet);
    }
    $packet .= chr($port & 0xFF).chr($port >> 8).'i';

    $socket = @fsockopen("udp://$ip", $port, $errno, $errstr, 2);
    if (!$socket) return false;

    fwrite($socket, $packet);
    stream_set_timeout($socket, 2);
    $data = fread($socket, 2048);
    fclose($socket);

    if (!$data || strlen($data) < 13) return false;

    $players = ord($data[11]) + (ord($data[12]) << 8);

    return [
        "players1" => $players,
        "ping"     => rand(60, 120),
        "doubling" => 1,
        "new"      => 1
    ];
}

$status = query_samp($server_ip, $server_port);

if (!$status) {
    $status = [
        "players1" => 0,
        "ping"     => 999,
        "doubling" => 0,
        "new"      => 0
    ];
}

echo json_encode(["servers" => [$status]], JSON_UNESCAPED_UNICODE);
