<?php
// Service para garantir consistencia transacional nas operacoes de estoque
class OperacaoEstoqueService
{
    public function registrarEntrada(array $dadosEntrada, array $dadosMovimentacao)
    {
        $db = Conexao::obter();
        $entradaModel = new Entrada();
        $produtoModel = new Produto();
        $movimentacaoModel = new Movimentacao();

        try {
            $db->beginTransaction();

            $produto = $produtoModel->buscarPorIdParaAtualizacao((int)$dadosEntrada['produto_id']);
            if (!$produto) {
                throw new RuntimeException('Produto nao encontrado para registrar entrada.');
            }

            $entradaId = (int)$entradaModel->criar($dadosEntrada);
            $produtoModel->incrementarEstoque((int)$dadosEntrada['produto_id'], (float)$dadosEntrada['quantidade']);
            $movimentacaoId = (int)$movimentacaoModel->criar($dadosMovimentacao);

            $db->commit();
            return [
                'entrada_id' => $entradaId,
                'movimentacao_id' => $movimentacaoId
            ];
        } catch (Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $erro;
        }
    }

    public function registrarSaida(array $dadosSaida, array $dadosMovimentacao)
    {
        $db = Conexao::obter();
        $saidaModel = new Saida();
        $produtoModel = new Produto();
        $movimentacaoModel = new Movimentacao();

        try {
            $db->beginTransaction();

            $produto = $produtoModel->buscarPorIdParaAtualizacao((int)$dadosSaida['produto_id']);
            if (!$produto) {
                throw new RuntimeException('Produto nao encontrado para registrar saida.');
            }

            $estoqueAtual = (float)($produto['estoque_atual'] ?? 0);
            $quantidadeSaida = (float)$dadosSaida['quantidade'];
            if ($estoqueAtual < $quantidadeSaida) {
                throw new DomainException('Estoque insuficiente para a saida.');
            }

            $saidaId = (int)$saidaModel->criar($dadosSaida);
            $debitoAplicado = $produtoModel->debitarEstoqueSemNegativo((int)$dadosSaida['produto_id'], $quantidadeSaida);
            if (!$debitoAplicado) {
                throw new DomainException('Nao foi possivel debitar o estoque. Tente novamente.');
            }

            $movimentacaoId = (int)$movimentacaoModel->criar($dadosMovimentacao);

            $db->commit();
            return [
                'saida_id' => $saidaId,
                'movimentacao_id' => $movimentacaoId
            ];
        } catch (Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $erro;
        }
    }

    public function registrarDescarte(array $dadosDescarte, array $dadosMovimentacao)
    {
        $db = Conexao::obter();
        $descarteModel = new Descarte();
        $movimentacaoModel = new Movimentacao();

        try {
            $db->beginTransaction();
            $descarteId = (int)$descarteModel->criar($dadosDescarte);
            $movimentacaoId = (int)$movimentacaoModel->criar($dadosMovimentacao);
            $db->commit();
            return [
                'descarte_id' => $descarteId,
                'movimentacao_id' => $movimentacaoId
            ];
        } catch (Throwable $erro) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $erro;
        }
    }
}
