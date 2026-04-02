<?php $titulo = 'Estoque Crítico'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Estoque Crítico</h2>
        <p class="page-header__subtitulo">Produtos com saldo igual ou abaixo do mínimo definido.</p>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <form class="row g-2" method="GET" action="<?php echo url('criticos'); ?>">
            <div class="col-lg-4">
                <label class="form-label">Categoria</label>
                <select name="categoria_id" class="form-select">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?php echo e($c['id']); ?>" <?php echo ($categoria_id == $c['id']) ? 'selected' : ''; ?>><?php echo e($c['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">Filtrar</button>
            </div>
        </form>
    </div>
</section>

<section class="panel mt-3">
    <div class="panel__body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Estoque atual</th>
                        <th>Estoque mínimo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produtos)): ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-check2-circle"></i>
                                    Nenhum item crítico para o filtro selecionado.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produtos as $p): ?>
                            <tr class="table-warning">
                                <td><?php echo e($p['nome']); ?></td>
                                <td><?php echo e($p['categoria_nome']); ?></td>
                                <td><?php echo e($p['estoque_atual']); ?></td>
                                <td><?php echo e($p['estoque_minimo']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
