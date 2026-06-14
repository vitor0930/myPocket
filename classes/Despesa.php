<?php
declare (strict_types=1);
require_once 'Transacao.php';
class Despesa extends Transacao {
    public function getTipo() {
        return 'despesa';
    }
}