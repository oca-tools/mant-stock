<?php $titulo = 'Entradas'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Entradas de Estoque</h3>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <a class="btn btn-primary" href="<?php echo url('entradas/criar'); ?>">Nova Entrada</a>
    <?php endif; ?>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Fornecedor</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entradas as $e): ?>
                <tr>
                    <td><?php echo e($e['data_entrada']); ?></td>
                    <td><?php echo e($e['produto_nome']); ?></td>
                    <td><?php echo e($e['quantidade']); ?></td>
                    <td><?php echo e($e['fornecedor']); ?></td>
                    <td><?php echo e($e['usuario_nome']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







