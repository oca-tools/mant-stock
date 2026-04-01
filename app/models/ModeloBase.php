<?php
// Modelo base com utilitarios de banco
abstract class ModeloBase
{
    protected $db;

    public function __construct()
    {
        $this->db = Conexao::obter();
    }
}
