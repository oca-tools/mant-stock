<?php $titulo = 'Relatórios'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Relatórios</h2>
        <p class="page-header__subtitulo">Análises operacionais para consumo, descarte, movimentações e saldo atual.</p>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <form class="row g-2" method="GET" action="<?php echo url('relatorios'); ?>">
            <div class="col-lg-2">
                <label class="form-label">Início</label>
                <input type="date" name="inicio" class="form-control" value="<?php echo e($inicio); ?>">
            </div>
            <div class="col-lg-2">
                <label class="form-label">Fim</label>
                <input type="date" name="fim" class="form-control" value="<?php echo e($fim); ?>">
            </div>
            <div class="col-lg-4">
                <label class="form-label">Tipo de relatório</label>
                <select name="tipo" class="form-select">
                    <option value="movimentacoes" <?php echo $tipo === 'movimentacoes' ? 'selected' : ''; ?>>Movimentações por período</option>
                    <option value="consumo_produto" <?php echo $tipo === 'consumo_produto' ? 'selected' : ''; ?>>Consumo por produto</option>
                    <option value="consumo_categoria" <?php echo $tipo === 'consumo_categoria' ? 'selected' : ''; ?>>Consumo por categoria</option>
                    <option value="consumo_setor_mensal" <?php echo $tipo === 'consumo_setor_mensal' ? 'selected' : ''; ?>>Consumo mensal por setor</option>
                    <option value="descartes" <?php echo $tipo === 'descartes' ? 'selected' : ''; ?>>Materiais descartados</option>
                    <option value="descartes_vs_saidas" <?php echo $tipo === 'descartes_vs_saidas' ? 'selected' : ''; ?>>Descartes vs saídas</option>
                    <option value="ajustes" <?php echo $tipo === 'ajustes' ? 'selected' : ''; ?>>Ajustes de estoque</option>
                    <option value="estoque_minimo" <?php echo $tipo === 'estoque_minimo' ? 'selected' : ''; ?>>Produtos com estoque mínimo</option>
                    <option value="estoque_atual" <?php echo $tipo === 'estoque_atual' ? 'selected' : ''; ?>>Estoque atual</option>
                </select>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">Gerar</button>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <a class="btn btn-outline-primary w-100" href="<?php echo url('relatorios'); ?>">Limpar</a>
            </div>
        </form>
    </div>
</section>

<section class="page-header mt-3 mb-2">
    <div class="page-header__acoes">
        <a class="btn btn-outline-success" href="<?php echo url('relatorios/excel?inicio=' . $inicio . '&fim=' . $fim . '&tipo=' . $tipo); ?>">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar Excel (CSV)
        </a>
        <a class="btn btn-outline-secondary" href="<?php echo url('relatorios/pdf?inicio=' . $inicio . '&fim=' . $fim . '&tipo=' . $tipo); ?>" target="_blank">
            <i class="bi bi-filetype-pdf me-1"></i>Exportar PDF (impressão)
        </a>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <?php foreach ($dados['cabecalho'] as $col): ?>
                            <th><?php echo e($col); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dados['linhas'])): ?>
                        <tr>
                            <td colspan="<?php echo count($dados['cabecalho']); ?>">
                                <div class="empty-state">
                                    <i class="bi bi-bar-chart-line"></i>
                                    Sem dados no período selecionado.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dados['linhas'] as $linha): ?>
                            <tr>
                                <?php foreach ($linha as $valor): ?>
                                    <td><?php echo e($valor); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!empty($dados['totais'])): ?>
                            <tr class="table-warning fw-bold">
                                <?php foreach ($dados['totais'] as $valor): ?>
                                    <td><?php echo e($valor); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
