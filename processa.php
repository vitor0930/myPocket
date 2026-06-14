<?php
session_start();

require_once "classes/Receita.php";
require_once "classes/Despesa.php";
require_once "classes/Carteira.php";

if (!isset($_SESSION['carteira'])) {
    $_SESSION['carteira'] = serialize(new Carteira());
}

$carteira = unserialize($_SESSION['carteira']);

try {

    $tipo = $_POST['tipo'];
    $valor = floatval($_POST['valor']);
    $descricao = $_POST['descricao'];
    $data = date('d/m/Y');

    if ($tipo == "receita") {
        $transacao = new Receita($valor, $descricao, $data);
    } else {
        $transacao = new Despesa($valor, $descricao, $data);
    }

    $carteira->adicionarTransacao($transacao);

    $_SESSION['mensagem'] = "Transação cadastrada com sucesso!";

} catch (Exception $e) {

    $_SESSION['erro'] = $e->getMessage();
}

$_SESSION['carteira'] = serialize($carteira);

header("Location: index.php");
exit;