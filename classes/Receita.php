<?php
declare (strict_types=1);
require_once 'Transacao.php';
class Receita extends Transacao {
    public function getTipo() {
        return 'Entrada';
    }
}