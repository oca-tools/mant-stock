<?php $titulo = 'Ferramentas'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Ferramentas</h3>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <a class="btn btn-primary" href="<?php echo url('ferramentas/criar'); ?>">Nova Ferramenta</a>
    <?php endif; ?>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ferramentas as $f): ?>
                <tr>
                    <td><?php echo e($f['nome']); ?></td>
                    <td><?php echo e($f['descricao']); ?></td>
                    <td><?php echo e($f['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







