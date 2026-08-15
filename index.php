<?php
session_start();

require_once "conexao.php";


$stmt = $pdo->query("SELECT * FROM transacoes ORDER BY data DESC, id DESC");
$transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);


$saldoStmt = $pdo->query("
    SELECT 
        COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END), 0) -
        COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END), 0) AS saldo
    FROM transacoes
");
$saldo = $saldoStmt->fetch(PDO::FETCH_ASSOC)['saldo'];
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
        R$ <?= number_format($saldo, 2, ',', '.') ?>
    </div>

    <?php if(isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['mensagem']) ?>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['erro']) ?>
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
            <label>Data</label>
            <input type="date" name="data" class="form-control"
                   value="<?= date('Y-m-d') ?>"
                   max="<?= date('Y-m-d') ?>" required>
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

        <?php foreach($transacoes as $t): ?>

    <?php
    $classe = ($t['tipo'] === 'receita')
        ? "list-group-item-success"
        : "list-group-item-danger";

    $tipoLabel = ($t['tipo'] === 'receita') ? "Entrada" : "Saída";
    $dataFormatada = date('d/m/Y', strtotime($t['data']));
    ?>

    <li class="list-group-item <?= $classe ?> d-flex justify-content-between align-items-center">

        <div>
            <strong><?= $tipoLabel ?></strong> |
            <?= htmlspecialchars($t['descricao']) ?> |
            R$ <?= number_format($t['valor'], 2, ',', '.') ?> |
            <?= $dataFormatada ?>
        </div>

        <div>
            <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
            <a href="delete.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Tem certeza que deseja excluir esta transação?');">Excluir</a>
        </div>

    </li>

        <?php endforeach; ?>

        <?php if (empty($transacoes)): ?>
            <li class="list-group-item text-muted">Nenhuma transação cadastrada.</li>
        <?php endif; ?>

    </ul>

</div>

</body>
</html>