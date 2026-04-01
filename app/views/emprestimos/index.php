<?php $titulo = 'Empréstimos'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Empréstimos de Ferramentas</h3>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <a class="btn btn-primary" href="<?php echo url('emprestimos/criar'); ?>">Novo Empréstimo</a>
    <?php endif; ?>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Ferramenta</th>
                <th>Responsavel</th>
                <th>Data Retirada</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emprestimos as $e): ?>
                <tr>
                    <td><?php echo e($e['ferramenta_nome']); ?></td>
                    <td><?php echo e($e['usuario_responsavel']); ?></td>
                    <td><?php echo e($e['data_retirada']); ?></td>
                    <td><?php echo e($e['status']); ?></td>
                    <td class="text-end">
                        <?php if ($e['status'] === 'Emprestada'): ?>
                            <form method="POST" action="<?php echo url('emprestimos/devolver/' . $e['id']); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-sm btn-outline-success">Registrar Devolucao</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







