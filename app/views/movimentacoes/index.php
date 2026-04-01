<?php $titulo = 'Movimentações'; require __DIR__ . '/../layouts/header.php'; ?>
<h3>Movimentações de Estoque</h3>
<form class="row g-2 mb-3" method="GET" action="<?php echo url('movimentacoes'); ?>">
    <div class="col-md-3">
        <label class="form-label">Inicio</label>
        <input type="date" name="inicio" class="form-control" value="<?php echo e($inicio); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Fim</label>
        <input type="date" name="fim" class="form-control" value="<?php echo e($fim); ?>">
    </div>
    <div class="col-md-2 align-self-end">
        <button class="btn btn-primary">Filtrar</button>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movimentacoes as $m): ?>
                <tr>
                    <td><?php echo e($m['data_movimentacao']); ?></td>
                    <td><?php echo e($m['produto_nome']); ?></td>
                    <td><?php echo e($m['tipo_movimentacao']); ?></td>
                    <td><?php echo e($m['quantidade']); ?></td>
                    <td><?php echo e($m['usuario_nome']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







