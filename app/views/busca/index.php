<?php $titulo = 'Busca Global'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Busca Global</h2>
        <p class="page-header__subtitulo">Localize rapidamente produtos e movimentações relacionadas.</p>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <form class="row g-2" method="GET" action="<?php echo url('busca'); ?>">
            <div class="col-lg-10">
                <label class="form-label">Termo de busca</label>
                <input type="text" name="q" class="form-control" placeholder="Digite nome do produto, código ou movimentação" value="<?php echo e($termo); ?>">
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">Buscar</button>
            </div>
        </form>
    </div>
</section>

<div class="row g-3 mt-1">
    <div class="col-xl-6">
        <section class="panel h-100">
            <div class="panel__body">
                <h3 class="panel__titulo">Produtos</h3>
                <ul class="list-group list-group-flush">
                    <?php if (empty($produtos)): ?>
                        <li class="list-group-item text-muted">Nenhum produto encontrado.</li>
                    <?php else: ?>
                        <?php foreach ($produtos as $p): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><?php echo e($p['nome']); ?></span>
                                <a class="btn btn-sm btn-outline-primary" href="<?php echo url('produtos/ver/' . $p['id']); ?>">Ver</a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </section>
    </div>
    <div class="col-xl-6">
        <section class="panel h-100">
            <div class="panel__body">
                <h3 class="panel__titulo">Movimentações</h3>
                <ul class="list-group list-group-flush">
                    <?php if (empty($movimentacoes)): ?>
                        <li class="list-group-item text-muted">Nenhuma movimentação encontrada.</li>
                    <?php else: ?>
                        <?php foreach ($movimentacoes as $m): ?>
                            <li class="list-group-item">
                                <?php echo e($m['data_movimentacao']); ?> - <?php echo e($m['produto_nome']); ?> (<?php echo e($m['tipo_movimentacao']); ?>)
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </section>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
