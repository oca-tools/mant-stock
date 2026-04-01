<?php
// Controller base com recursos comuns
abstract class ControllerBase
{
    protected function render($view, $dados = [])
    {
        view($view, $dados);
    }

    protected function exigirCsrf()
    {
        validar_csrf();
    }
}
