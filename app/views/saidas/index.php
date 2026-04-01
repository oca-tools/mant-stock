<?php $titulo = 'Saídas'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Saídas de Estoque</h3>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <a class="btn btn-primary" href="<?php echo url('saidas/criar'); ?>">Nova Saída</a>
    <?php endif; ?>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Local</th>
                <th>Solicitante</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($saidas as $s): ?>
                <tr>
                    <td><?php echo e($s['data_saida']); ?></td>
                    <td><?php echo e($s['produto_nome']); ?></td>
                    <td><?php echo e($s['quantidade']); ?></td>
                    <td><?php echo e($s['local_utilizacao']); ?></td>
                    <td><?php echo e($s['tecnico_responsavel']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







