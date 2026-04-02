<?php
// Controller de inventarios mensais
class InventariosController extends ControllerBase
{
    public function index()
    {
        $inventarioModel = new Inventario();
        $competencia = $this->normalizarCompetencia($_GET['competencia'] ?? date('Y-m'));
        $ciclo = $inventarioModel->buscarCicloPorCompetencia($competencia);

        $inventarios = [];
        $resumo = [
            'total_contados' => 0,
            'total_divergencias' => 0,
            'total_sobras' => 0,
            'total_faltas' => 0
        ];
        if ($ciclo) {
            $inventarios = $inventarioModel->listarPorCompetencia($competencia);
            $resumo = $inventarioModel->resumoPorCompetencia($competencia);
        }

        $totalProdutos = $inventarioModel->contarProdutosAtivos();
        $resumo['total_pendentes'] = max($totalProdutos - (int)$resumo['total_contados'], 0);
        $pendentes = $inventarioModel->listarPendentesPorCompetencia($competencia, 10);
        $competenciasRecentes = $inventarioModel->listarCompetenciasRecentes(12);

        if (!in_array($competencia, $competenciasRecentes, true)) {
            array_unshift($competenciasRecentes, $competencia);
        }

        $this->render('inventarios/index', [
            'competencia' => $competencia,
            'competenciasRecentes' => $competenciasRecentes,
            'ciclo' => $ciclo,
            'inventarios' => $inventarios,
            'resumo' => $resumo,
            'totalProdutos' => $totalProdutos,
            'pendentes' => $pendentes
        ]);
    }

    public function abrirCiclo()
    {
        $this->exigirCsrf();

        $competencia = $this->normalizarCompetencia($_POST['competencia'] ?? date('Y-m'));
        $observacoesAbertura = trim($_POST['observacoes_abertura'] ?? '');

        $inventarioModel = new Inventario();
        $cicloExistente = $inventarioModel->buscarCicloPorCompetencia($competencia);
        if ($cicloExistente) {
            $mensagem = ($cicloExistente['status'] === 'aberto')
                ? 'O ciclo de inventario dessa competencia ja esta aberto.'
                : 'O ciclo de inventario dessa competencia ja foi fechado.';
            flash_set('inventarios', $mensagem, 'warning');
            $this->redirecionarParaCompetencia($competencia);
        }

        $idCiclo = $inventarioModel->abrirCiclo($competencia, (int)$_SESSION['usuario']['id'], $observacoesAbertura);
        LogService::registrar(
            $_SESSION['usuario']['id'],
            'inventario_abertura',
            'Abertura do ciclo mensal de inventario',
            'inventarios_mensais',
            $idCiclo,
            null,
            ['competencia' => $competencia, 'observacoes_abertura' => $observacoesAbertura]
        );

        flash_set('inventarios', 'Ciclo mensal aberto com sucesso para ' . $competencia . '.', 'success');
        $this->redirecionarParaCompetencia($competencia);
    }

    public function criar()
    {
        $competencia = $this->normalizarCompetencia($_GET['competencia'] ?? date('Y-m'));
        $this->renderFormularioContagem($competencia, null, [
            'produto_id' => '',
            'quantidade_real' => '',
            'motivo_ajuste' => ''
        ]);
    }

    public function armazenar()
    {
        $this->exigirCsrf();

        $competencia = $this->normalizarCompetencia($_POST['competencia'] ?? date('Y-m'));
        $produtoId = (int)($_POST['produto_id'] ?? 0);
        $quantidadeReal = (float)str_replace(',', '.', (string)($_POST['quantidade_real'] ?? '0'));
        $motivoAjuste = trim($_POST['motivo_ajuste'] ?? '');
        $dadosForm = [
            'produto_id' => $produtoId,
            'quantidade_real' => $_POST['quantidade_real'] ?? '',
            'motivo_ajuste' => $motivoAjuste
        ];

        if ($produtoId <= 0) {
            $this->renderFormularioContagem($competencia, 'Informe um produto valido.', $dadosForm);
            return;
        }

        if ($quantidadeReal < 0) {
            $this->renderFormularioContagem($competencia, 'A quantidade real nao pode ser negativa.', $dadosForm);
            return;
        }

        $inventarioModel = new Inventario();
        $ciclo = $inventarioModel->buscarCicloPorCompetencia($competencia);
        if (!$ciclo || $ciclo['status'] !== 'aberto') {
            flash_set('inventarios', 'Abra o ciclo mensal antes de registrar contagens.', 'warning');
            $this->redirecionarParaCompetencia($competencia);
        }

        $produtoModel = new Produto();
        $produto = $produtoModel->buscarPorId($produtoId);
        if (!$produto) {
            $this->renderFormularioContagem($competencia, 'Produto nao encontrado.', $dadosForm);
            return;
        }

        $quantidadeSistema = (float)$produto['estoque_atual'];
        $diferenca = round($quantidadeReal - $quantidadeSistema, 2);
        if (abs($diferenca) > 0.00001 && $motivoAjuste === '') {
            $this->renderFormularioContagem($competencia, 'Informe o motivo quando houver divergencia.', $dadosForm);
            return;
        }

        $resultado = $inventarioModel->salvarContagem([
            'inventario_mensal_id' => (int)$ciclo['id'],
            'produto_id' => $produtoId,
            'quantidade_sistema' => $quantidadeSistema,
            'quantidade_real' => $quantidadeReal,
            'diferenca' => $diferenca,
            'usuario_id' => (int)$_SESSION['usuario']['id'],
            'motivo_ajuste' => $motivoAjuste
        ]);

        LogService::registrar(
            $_SESSION['usuario']['id'],
            'inventario_contagem',
            $resultado['atualizado'] ? 'Contagem de inventario atualizada' : 'Contagem de inventario registrada',
            'inventarios',
            $resultado['id'],
            null,
            [
                'competencia' => $competencia,
                'produto_id' => $produtoId,
                'quantidade_sistema' => $quantidadeSistema,
                'quantidade_real' => $quantidadeReal,
                'diferenca' => $diferenca,
                'motivo_ajuste' => $motivoAjuste
            ]
        );

        $mensagem = $resultado['atualizado']
            ? 'Contagem atualizada com sucesso.'
            : 'Contagem registrada com sucesso.';
        flash_set('inventarios', $mensagem, 'success');
        $this->redirecionarParaCompetencia($competencia);
    }

    public function fecharCiclo()
    {
        $this->exigirCsrf();

        $competencia = $this->normalizarCompetencia($_POST['competencia'] ?? date('Y-m'));
        $observacoesFechamento = trim($_POST['observacoes_fechamento'] ?? '');

        $inventarioModel = new Inventario();
        $ciclo = $inventarioModel->buscarCicloPorCompetencia($competencia);
        if (!$ciclo) {
            flash_set('inventarios', 'Nao existe ciclo aberto para essa competencia.', 'warning');
            $this->redirecionarParaCompetencia($competencia);
        }
        if ($ciclo['status'] !== 'aberto') {
            flash_set('inventarios', 'Esse ciclo ja esta fechado.', 'warning');
            $this->redirecionarParaCompetencia($competencia);
        }

        $totalContados = $inventarioModel->contarRegistrosDoCiclo((int)$ciclo['id']);
        if ($totalContados === 0) {
            flash_set('inventarios', 'Registre ao menos uma contagem antes de fechar o ciclo.', 'warning');
            $this->redirecionarParaCompetencia($competencia);
        }

        $db = Conexao::obter();
        $produtoModel = new Produto();
        $movimentacaoModel = new Movimentacao();

        try {
            $db->beginTransaction();

            $itensParaAjuste = $inventarioModel->listarItensParaAjuste((int)$ciclo['id']);
            $totalAjustesAplicados = 0;

            foreach ($itensParaAjuste as $item) {
                $diferenca = (float)$item['diferenca'];
                if (abs($diferenca) <= 0.00001) {
                    continue;
                }

                $produtoModel->atualizarEstoque((int)$item['produto_id'], $diferenca);
                $movimentacaoModel->criar([
                    'produto_id' => (int)$item['produto_id'],
                    'tipo_movimentacao' => 'ajuste',
                    'quantidade' => abs($diferenca),
                    'usuario_id' => (int)$_SESSION['usuario']['id'],
                    'origem' => 'Inventario mensal ' . $competencia,
                    'destino' => $diferenca > 0 ? 'Ajuste de entrada' : 'Ajuste de saida',
                    'observacoes' => trim(($item['motivo_ajuste'] ?? '') . ' | Fechamento ' . $competencia)
                ]);
                $totalAjustesAplicados++;
            }

            $inventarioModel->marcarCicloComoAjustado((int)$ciclo['id']);
            $fechado = $inventarioModel->fecharCiclo((int)$ciclo['id'], (int)$_SESSION['usuario']['id'], $observacoesFechamento);
            if (!$fechado) {
                throw new RuntimeException('Nao foi possivel fechar o ciclo informado.');
            }

            $db->commit();

            LogService::registrar(
                $_SESSION['usuario']['id'],
                'inventario_fechamento',
                'Fechamento do ciclo mensal de inventario',
                'inventarios_mensais',
                (int)$ciclo['id'],
                ['status' => 'aberto'],
                [
                    'status' => 'fechado',
                    'competencia' => $competencia,
                    'total_ajustes' => $totalAjustesAplicados,
                    'observacoes_fechamento' => $observacoesFechamento
                ]
            );

            $mensagem = 'Ciclo mensal fechado com sucesso. Ajustes aplicados: ' . $totalAjustesAplicados . '.';
            flash_set('inventarios', $mensagem, 'success');
        } catch (Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            flash_set('inventarios', 'Falha ao fechar ciclo mensal. Tente novamente.', 'danger');
        }

        $this->redirecionarParaCompetencia($competencia);
    }

    private function renderFormularioContagem($competencia, $erro = null, $dadosForm = [])
    {
        $inventarioModel = new Inventario();
        $ciclo = $inventarioModel->buscarCicloPorCompetencia($competencia);
        if (!$ciclo) {
            flash_set('inventarios', 'Abra o ciclo mensal antes de registrar contagens.', 'warning');
            $this->redirecionarParaCompetencia($competencia);
        }

        if ($ciclo['status'] !== 'aberto') {
            flash_set('inventarios', 'O ciclo selecionado esta fechado e nao permite novas contagens.', 'warning');
            $this->redirecionarParaCompetencia($competencia);
        }

        $produtoModel = new Produto();
        $produtos = $produtoModel->listar(500, 0, '');
        $this->render('inventarios/criar', [
            'competencia' => $competencia,
            'ciclo' => $ciclo,
            'produtos' => $produtos,
            'erro' => $erro,
            'dadosForm' => $dadosForm
        ]);
    }

    private function normalizarCompetencia($valor)
    {
        $valor = trim((string)$valor);
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $valor)) {
            return date('Y-m');
        }
        return $valor;
    }

    private function redirecionarParaCompetencia($competencia)
    {
        redirect(url('inventarios?competencia=' . urlencode($competencia)));
    }
}
