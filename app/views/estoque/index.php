<?php $titulo = 'Estoque Atual'; require __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3>Estoque Atual</h3>
        <div class="text-muted">Visão geral dos materiais e saldos disponíveis</div>
    </div>
</div>
<form class="row g-2 mb-3" method="GET" action="<?php echo url('estoque'); ?>">
    <div class="col-md-4">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou código" value="<?php echo e($busca); ?>">
    </div>
    <div class="col-md-3">
        <select name="ordem" class="form-select">
            <option value="az" <?php echo ($ordem === 'az') ? 'selected' : ''; ?>>A-Z</option>
            <option value="za" <?php echo ($ordem === 'za') ? 'selected' : ''; ?>>Z-A</option>
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary">Buscar</button>
    </div>
</form>
<div class="row g-3">
    <?php foreach ($produtos as $p): ?>
        <div class="col-md-4">
            <div class="card estoque-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="mb-1"><?php echo e($p['nome']); ?></h5>
                        <?php if ($p['estoque_atual'] <= $p['estoque_minimo']): ?>
                            <span class="badge badge-estoque"><i class="bi bi-exclamation-triangle-fill me-1"></i>Baixo</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted mb-2"><?php echo e($p['categoria_nome']); ?></div>
                    <div class="mb-2">Estoque: <strong><?php echo e($p['estoque_atual']); ?></strong> <?php echo e($p['unidade_medida']); ?></div>
                    <div class="text-muted">Mínimo: <?php echo e($p['estoque_minimo']); ?></div>
                    <div class="text-muted">Local: <?php echo e($p['localizacao']); ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<nav class="mt-4">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo url('estoque') . '?pagina=' . $i . '&busca=' . urlencode($busca) . '&ordem=' . $ordem; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php require __DIR__ . '/../layouts/footer.php'; ?>





