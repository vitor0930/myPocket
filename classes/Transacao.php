<?php

declare (strict_types=1);
abstract class Transacao
{
    protected $valor;
    protected $descricao;
    protected $data;

    public function __construct($valor, $descricao, $data)
    {
        $this->valor = $valor;
        $this->descricao = $descricao;
        $this->data = $data;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getData()
    {
        return $this->data;
    }

    abstract public function getTipo();
}
?>