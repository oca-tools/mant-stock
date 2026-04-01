<?php $titulo = 'Busca Global'; require __DIR__ . '/../layouts/header.php'; ?>
<h3>Busca Global</h3>
<form class="row g-2 mb-3" method="GET" action="<?php echo url('busca'); ?>">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Buscar produtos e Movimentações" value="<?php echo e($termo); ?>">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary">Buscar</button>
    </div>
</form>
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Produtos</h5>
                <ul class="list-group list-group-flush">
                    <?php foreach ($produtos as $p): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?php echo e($p['nome']); ?></span>
                            <a class="btn btn-sm btn-outline-primary" href="<?php echo url('produtos/ver/' . $p['id']); ?>">Ver</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Movimentações</h5>
                <ul class="list-group list-group-flush">
                    <?php foreach ($movimentacoes as $m): ?>
                        <li class="list-group-item">
                            <?php echo e($m['data_movimentacao']); ?> - <?php echo e($m['produto_nome']); ?> (<?php echo e($m['tipo_movimentacao']); ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







