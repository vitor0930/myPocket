<?php
session_start();

require_once "conexao.php";

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['erro'] = "ID inválido.";
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM transacoes WHERE id = :id");
$stmt->execute([':id' => $id]);
$transacao = $stmt->fetch();

if (!$transacao) {
    $_SESSION['erro'] = "Transação não encontrada.";
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MyPocket - Editar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <h1 class="mb-4">Editar Transação</h1>

    <form action="update.php" method="POST">

        <input type="hidden" name="id" value="<?= $transacao['id'] ?>">

        <div class="mb-3">
            <label>Descrição</label>
            <input type="text" name="descricao" class="form-control"
                   value="<?= htmlspecialchars($transacao['descricao']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Valor</label>
            <input type="number" step="0.01" name="valor" class="form-control"
                   value="<?= $transacao['valor'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Data</label>
            <input type="date" name="data" class="form-control"
                   value="<?= $transacao['data'] ?>" max="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="mb-3">
            <label>Tipo</label>
            <select name="tipo" class="form-control">
                <option value="receita" <?= $transacao['tipo'] === 'receita' ? 'selected' : '' ?>>Receita</option>
                <option value="despesa" <?= $transacao['tipo'] === 'despesa' ? 'selected' : '' ?>>Despesa</option>
            </select>
        </div>

        <button class="btn btn-primary">Salvar alterações</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>

    </form>

</div>

</body>
</html>