<?php

declare (strict_types=1);
class Carteira{   
    
    private $saldo = 0;
    private $transacoes = [];

    public function getSaldo()
    {
        return $this->saldo;
    }

    public function adicionarTransacao($transacao)
    {
        if ($transacao instanceof Receita) {

            $this->saldo += $transacao->getValor();

        } elseif ($transacao instanceof Despesa) {

            if ($transacao->getValor() > $this->saldo) {
                throw new Exception(
                    "Saldo insuficiente para realizar esta despesa."
                );
            }

            $this->saldo -= $transacao->getValor();
        }

        $this->transacoes[] = $transacao;
    }

    public function getTransacoes()
    {
        return $this->transacoes;
    }
}