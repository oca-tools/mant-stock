<?php $titulo = 'Relatórios'; require __DIR__ . '/../layouts/header.php'; ?>
<h3>Relatórios</h3>
<form class="row g-2 mb-3" method="GET" action="<?php echo url('relatorios'); ?>">
    <div class="col-md-3">
        <label class="form-label">Inicio</label>
        <input type="date" name="inicio" class="form-control" value="<?php echo e($inicio); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Fim</label>
        <input type="date" name="fim" class="form-control" value="<?php echo e($fim); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tipo</label>
        <select name="tipo" class="form-select">
            <option value="movimentacoes" <?php echo $tipo === 'movimentacoes' ? 'selected' : ''; ?>>Movimentações por Período</option>
            <option value="consumo_produto" <?php echo $tipo === 'consumo_produto' ? 'selected' : ''; ?>>Consumo por Produto</option>
            <option value="consumo_categoria" <?php echo $tipo === 'consumo_categoria' ? 'selected' : ''; ?>>Consumo por Categoria</option>
            <option value="consumo_setor_mensal" <?php echo $tipo === 'consumo_setor_mensal' ? 'selected' : ''; ?>>Consumo Mensal por Setor</option>
            <option value="descartes" <?php echo $tipo === 'descartes' ? 'selected' : ''; ?>>Materiais Descartados</option>
            <option value="descartes_vs_saidas" <?php echo $tipo === 'descartes_vs_saidas' ? 'selected' : ''; ?>>Descartes vs Saidas</option>
            <option value="ajustes" <?php echo $tipo === 'ajustes' ? 'selected' : ''; ?>>Ajustes de Estoque</option>
            <option value="estoque_minimo" <?php echo $tipo === 'estoque_minimo' ? 'selected' : ''; ?>>Produtos com Estoque Mínimo</option>
            <option value="estoque_atual" <?php echo $tipo === 'estoque_atual' ? 'selected' : ''; ?>>Estoque Atual</option>
        </select>
    </div>
    <div class="col-md-2 align-self-end">
        <button class="btn btn-primary">Gerar</button>
    </div>
</form>
<div class="mb-3">
    <a class="btn btn-outline-success" href="<?php echo url('relatorios/excel?inicio=' . $inicio . '&fim=' . $fim . '&tipo=' . $tipo); ?>">Exportar Excel (CSV)</a>
    <a class="btn btn-outline-secondary" href="<?php echo url('relatorios/pdf?inicio=' . $inicio . '&fim=' . $fim . '&tipo=' . $tipo); ?>" target="_blank">Exportar PDF (via impressão)</a>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <?php foreach ($dados['cabecalho'] as $col): ?>
                    <th><?php echo e($col); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados['linhas'] as $linha): ?>
                <tr>
                    <?php foreach ($linha as $valor): ?>
                        <td><?php echo e($valor); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            <?php if (!empty($dados['totais'])): ?>
                <tr class="table-secondary fw-bold">
                    <?php foreach ($dados['totais'] as $valor): ?>
                        <td><?php echo e($valor); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="mt-4 text-muted">
    Assinatura do Responsavel: _______________________________
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







