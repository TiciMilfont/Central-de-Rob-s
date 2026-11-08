<?php

function lerRobos() {

    $arquivo = __DIR__ . "/robos.json";

    if (!file_exists($arquivo)) {
        return [];
    }

    $dados = file_get_contents($arquivo);

    return json_decode($dados, true) ?? [];
}

function salvarRobos($robos) {

    $arquivo = __DIR__ . "/robos.json";

    $json = json_encode($robos, JSON_PRETTY_PRINT);

    file_put_contents($arquivo, $json, LOCK_EX);
}