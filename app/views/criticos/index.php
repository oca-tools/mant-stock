<?php $titulo = 'Itens Criticos'; require __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3>Itens Criticos</h3>
        <div class="text-muted">Produtos com estoque igual ou abaixo do Mínimo</div>
    </div>
</div>
<form class="row g-2 mb-3" method="GET" action="<?php echo url('criticos'); ?>">
    <div class="col-md-4">
        <select name="categoria_id" class="form-select">
            <option value="">Todas as categorias</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?php echo e($c['id']); ?>" <?php echo ($categoria_id == $c['id']) ? 'selected' : ''; ?>><?php echo e($c['nome']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary">Filtrar</button>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Estoque Atual</th>
                <th>Estoque Mínimo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
                <tr class="table-warning">
                    <td><?php echo e($p['nome']); ?></td>
                    <td><?php echo e($p['categoria_nome']); ?></td>
                    <td><?php echo e($p['estoque_atual']); ?></td>
                    <td><?php echo e($p['estoque_minimo']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







