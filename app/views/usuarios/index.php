<?php $titulo = 'Usuarios'; require __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Usuarios</h3>
    <a class="btn btn-primary" href="<?php echo url('usuarios/criar'); ?>">Novo Usuario</a>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Status</th>
                <th>Criado em</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo e($u['nome']); ?></td>
                    <td><?php echo e($u['email']); ?></td>
                    <td><?php echo e($u['tipo_usuario']); ?></td>
                    <td><?php echo $u['ativo'] ? 'Ativo' : 'Inativo'; ?></td>
                    <td><?php echo e($u['created_at']); ?></td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo url('usuarios/editar/' . $u['id']); ?>">Editar</a>
                        <?php if ($u['ativo']): ?>
                            <form class="d-inline" method="POST" action="<?php echo url('usuarios/desativar/' . $u['id']); ?>" onsubmit="return confirm('Desativar usuario?');">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-sm btn-outline-danger">Desativar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
