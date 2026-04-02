<?php $titulo = 'Detalhes do Produto'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo"><?php echo e($produto['nome']); ?></h2>
        <p class="page-header__subtitulo">Código interno: <?php echo e($produto['codigo_interno']); ?></p>
    </div>
    <div class="page-header__acoes">
        <a class="btn btn-outline-secondary" href="<?php echo url('produtos'); ?>">Voltar</a>
    </div>
</section>

<div class="row g-3">
    <div class="col-xl-4">
        <section class="panel">
            <div class="panel__body">
                <?php if (!empty($produto['imagem'])): ?>
                    <img src="<?php echo url('uploads/' . $produto['imagem']); ?>" class="img-fluid rounded mb-3" alt="Produto">
                <?php endif; ?>
                <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Categoria</span><strong><?php echo e($produto['categoria_nome']); ?></strong></div>
                <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Unidade</span><strong><?php echo e($produto['unidade_medida']); ?></strong></div>
                <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Estoque atual</span><strong><?php echo e($produto['estoque_atual']); ?></strong></div>
                <div class="d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Estoque mínimo</span><strong><?php echo e($produto['estoque_minimo']); ?></strong></div>
                <div class="d-flex justify-content-between py-1"><span class="text-muted">Localização</span><strong><?php echo e($produto['localizacao']); ?></strong></div>
                <hr>
                <div><span class="text-muted d-block mb-1">Observações</span><?php echo e($produto['observacoes']); ?></div>
            </div>
        </section>
    </div>

    <div class="col-xl-8">
        <section class="panel">
            <div class="panel__body">
                <h3 class="panel__titulo">Últimas Movimentações</h3>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Usuário</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($movimentacoes)): ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="bi bi-clock-history"></i>
                                            Sem movimentações recentes para este produto.
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($movimentacoes as $m): ?>
                                    <tr>
                                        <td><?php echo e($m['data_movimentacao']); ?></td>
                                        <td><?php echo e($m['tipo_movimentacao']); ?></td>
                                        <td><?php echo e($m['quantidade']); ?></td>
                                        <td><?php echo e($m['usuario_nome']); ?></td>
                                        <td><?php echo e($m['observacoes']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
