<?php
session_start();

require_once "conexao.php";

try {

    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $tipo = $_POST['tipo'] ?? '';
    $valor = filter_var($_POST['valor'] ?? null, FILTER_VALIDATE_FLOAT);
    $descricao = trim($_POST['descricao'] ?? '');
    $data = $_POST['data'] ?? '';

    if (!$id) {
        throw new Exception("ID inválido.");
    }
    if (!in_array($tipo, ['receita', 'despesa'], true)) {
        throw new Exception("Tipo de transação inválido.");
    }
    if ($valor === false || $valor <= 0) {
        throw new Exception("Valor inválido.");
    }
    if ($descricao === '') {
        throw new Exception("Descrição é obrigatória.");
    }

    $dataObj = DateTime::createFromFormat('Y-m-d', $data);
    if (!$dataObj || $dataObj->format('Y-m-d') !== $data) {
        throw new Exception("Data inválida.");
    }

    $hoje = new DateTime('today');
    if ($dataObj > $hoje) {
        throw new Exception("A data não pode ser no futuro.");
    }

    if ($tipo === 'despesa') {

        $saldoStmt = $pdo->prepare("
            SELECT 
                COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END), 0) AS saldo
            FROM transacoes
            WHERE id != :id
        ");
        $saldoStmt->execute([':id' => $id]);
        $saldoAtual = (float) $saldoStmt->fetch()['saldo'];

        if ($valor > $saldoAtual) {
            throw new Exception(
                "Saldo insuficiente. Saldo disponível: R$ " . number_format($saldoAtual, 2, ',', '.')
            );
        }
    }

    $sql = "UPDATE transacoes 
            SET tipo = :tipo, valor = :valor, descricao = :descricao, data = :data 
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tipo'      => $tipo,
        ':valor'     => $valor,
        ':descricao' => $descricao,
        ':data'      => $data,
        ':id'        => $id,
    ]);

    $_SESSION['mensagem'] = "Transação atualizada com sucesso!";

} catch (Exception $e) {
    $_SESSION['erro'] = $e->getMessage();
} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro no banco de dados: " . $e->getMessage();
}

header("Location: index.php");
exit;