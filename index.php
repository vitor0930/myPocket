<?php
session_start();

require_once "classes/Transacao.php";
require_once "classes/Receita.php";
require_once "classes/Despesa.php";
require_once "classes/Carteira.php";

if (!isset($_SESSION['carteira'])) {
    $_SESSION['carteira'] = serialize(new Carteira());
}

$carteira = unserialize($_SESSION['carteira']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MyPocket</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <h1 class="mb-4">MyPocket</h1>

    <div class="alert alert-primary">
        <strong>Saldo Atual:</strong>
        R$ <?= number_format($carteira->getSaldo(), 2, ',', '.') ?>
    </div>

    <?php if(isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['mensagem']; ?>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['erro']; ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <form action="processa.php" method="POST">

        <div class="mb-3">
            <label>Descrição</label>
            <input type="text" name="descricao" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Valor</label>
            <input type="number" step="0.01" name="valor" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Tipo</label>
            <select name="tipo" class="form-control">
                <option value="receita">Receita</option>
                <option value="despesa">Despesa</option>
            </select>
        </div>

        <button class="btn btn-success">
            Salvar
        </button>

    </form>

    <hr>

    <h3>Extrato</h3>

    <ul class="list-group">

        <?php foreach($carteira->getTransacoes() as $t): ?>

            <?php
            $classe = ($t->getTipo() == "Entrada")
                ? "list-group-item-success"
                : "list-group-item-danger";
            ?>

            <li class="list-group-item <?= $classe ?>">

                <strong><?= $t->getTipo() ?></strong> |
                <?= $t->getDescricao() ?> |
                R$ <?= number_format($t->getValor(), 2, ',', '.') ?> |
                <?= $t->getData() ?>

            </li>

        <?php endforeach; ?>

    </ul>

</div>

</body>
</html>