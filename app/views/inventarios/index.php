<?php $titulo = 'Inventários'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Inventários</h3>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <a class="btn btn-primary" href="<?php echo url('inventarios/criar'); ?>">Novo Inventário</a>
    <?php endif; ?>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Qtd Sistema</th>
                <th>Qtd Real</th>
                <th>Diferenca</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventarios as $i): ?>
                <tr>
                    <td><?php echo e($i['data_inventario']); ?></td>
                    <td><?php echo e($i['produto_nome']); ?></td>
                    <td><?php echo e($i['quantidade_sistema']); ?></td>
                    <td><?php echo e($i['quantidade_real']); ?></td>
                    <td><?php echo e($i['diferenca']); ?></td>
                    <td><?php echo e($i['motivo_ajuste']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







