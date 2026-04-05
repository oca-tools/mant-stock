<?php
// Testes smoke sem dependencia de banco para validar utilitarios basicos.

require __DIR__ . '/../app/services/PeriodoService.php';
require __DIR__ . '/../app/controllers/ControllerBase.php';

class ControllerTesteCsv extends ControllerBase
{
    public function testarSanitizacao($valor)
    {
        return $this->sanitizarCelulaCsv($valor);
    }
}

function assert_true($condicao, $mensagem)
{
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
}

$periodo = PeriodoService::periodoIndexado('2026-04-30', '2026-04-01');
assert_true($periodo['inicio_data'] === '2026-04-01', 'PeriodoService nao ordenou inicio/fim corretamente.');
assert_true($periodo['fim_data'] === '2026-04-30', 'PeriodoService nao ordenou inicio/fim corretamente.');
assert_true($periodo['inicio_dt'] === '2026-04-01 00:00:00', 'PeriodoService inicio_dt invalido.');
assert_true(strpos($periodo['fim_dt'], '2026-05-01') === 0, 'PeriodoService fim_dt invalido.');

$controllerTeste = new ControllerTesteCsv();
assert_true($controllerTeste->testarSanitizacao('=2+2') === "'=2+2", 'Protecao CSV para formula nao aplicada.');
assert_true($controllerTeste->testarSanitizacao('+cmd') === "'+cmd", 'Protecao CSV para comando nao aplicada.');
assert_true($controllerTeste->testarSanitizacao('texto normal') === 'texto normal', 'Texto comum nao deveria ser alterado.');

echo "Testes smoke executados com sucesso.\n";
