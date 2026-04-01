<?php $titulo = 'Descartes'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Descartes</h3>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <a class="btn btn-primary" href="<?php echo url('descartes/criar'); ?>">Novo Descarte</a>
    <?php endif; ?>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($descartes as $d): ?>
                <tr>
                    <td><?php echo e($d['data_descarte']); ?></td>
                    <td><?php echo e($d['produto_nome']); ?></td>
                    <td><?php echo e($d['quantidade']); ?></td>
                    <td><?php echo e($d['motivo_descarte']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







