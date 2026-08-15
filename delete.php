<?php
session_start();

require_once "conexao.php";

try {

    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

    if (!$id) {
        throw new Exception("ID inválido.");
    }

    $stmt = $pdo->prepare("DELETE FROM transacoes WHERE id = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception("Transação não encontrada.");
    }

    $_SESSION['mensagem'] = "Transação excluída com sucesso!";

} catch (Exception $e) {
    $_SESSION['erro'] = $e->getMessage();
} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro no banco de dados: " . $e->getMessage();
}

header("Location: index.php");
exit;