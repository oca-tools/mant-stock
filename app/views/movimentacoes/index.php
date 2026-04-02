<?php $titulo = 'Movimentacoes'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Movimentacoes de Estoque</h2>
        <p class="page-header__subtitulo">Rastreabilidade completa de entradas, saidas, descartes e ajustes.</p>
    </div>
    <div class="page-header__acoes">
        <a class="btn btn-outline-success" href="<?php echo url('movimentacoes/excel?inicio=' . urlencode($inicio) . '&fim=' . urlencode($fim)); ?>">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar Excel
        </a>
        <a class="btn btn-outline-secondary" href="<?php echo url('movimentacoes/pdf?inicio=' . urlencode($inicio) . '&fim=' . urlencode($fim)); ?>" target="_blank">
            <i class="bi bi-filetype-pdf me-1"></i>Exportar PDF
        </a>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <form class="row g-2" method="GET" action="<?php echo url('movimentacoes'); ?>">
            <div class="col-lg-3">
                <label class="form-label">Inicio</label>
                <input type="date" name="inicio" class="form-control" value="<?php echo e($inicio); ?>">
            </div>
            <div class="col-lg-3">
                <label class="form-label">Fim</label>
                <input type="date" name="fim" class="form-control" value="<?php echo e($fim); ?>">
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">Filtrar</button>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <a class="btn btn-outline-primary w-100" href="<?php echo url('movimentacoes'); ?>">Limpar</a>
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
                        <th>Data</th>
                        <th>Produto</th>
                        <th>Tipo</th>
                        <th>Quantidade</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($movimentacoes)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-journal-text"></i>
                                    Nenhuma movimentacao encontrada no periodo.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($movimentacoes as $m): ?>
                            <tr>
                                <td><?php echo e($m['data_movimentacao']); ?></td>
                                <td><?php echo e($m['produto_nome']); ?></td>
                                <td><?php echo e($m['tipo_movimentacao']); ?></td>
                                <td><?php echo e($m['quantidade']); ?></td>
                                <td><?php echo e($m['usuario_nome']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<nav class="data-paginacao">
    <ul class="pagination flex-wrap">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo url('movimentacoes') . '?inicio=' . urlencode($inicio) . '&fim=' . urlencode($fim) . '&pagina=' . $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
