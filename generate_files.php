<?php
header('Content-Type: application/json');

$base_dir = __DIR__;
$base_url = "http://191.252.102.88/apiraife/com.raiferoleplay.game/";

function listarArquivos($dir, $base_dir, $base_url) {
    $arquivos = [];
    $itens = scandir($dir);

    foreach ($itens as $item) {
        if ($item === '.' || $item === '..') continue;
        if ($item === 'generate_files.php') continue;

        $caminho_completo = $dir . '/' . $item;
        $caminho_relativo = str_replace($base_dir . '/', '', $caminho_completo);

        if (is_dir($caminho_completo)) {
            $arquivos = array_merge(
                $arquivos,
                listarArquivos($caminho_completo, $base_dir, $base_url)
            );
        } else {
            $arquivos[] = [
                "name" => $item,
                "size" => filesize($caminho_completo),
                "path" => str_replace('\\', '/', $caminho_relativo),
                "url"  => $base_url . str_replace('\\', '/', $caminho_relativo)
            ];
        }
    }

    return $arquivos;
}

echo json_encode(
    ["files" => listarArquivos($base_dir, $base_dir, $base_url)],
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
);