<?php $titulo = 'Categorias'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Categorias</h3>
    <?php if ($tipoUsuario === 'Administrador'): ?>
        <a class="btn btn-primary" href="<?php echo url('categorias/criar'); ?>">Nova Categoria</a>
    <?php endif; ?>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $c): ?>
                <tr>
                    <td><?php echo e($c['nome']); ?></td>
                    <td><?php echo e($c['descricao']); ?></td>
                    <td class="text-end">
                        <?php if ($tipoUsuario === 'Administrador'): ?>
                            <a class="btn btn-sm btn-outline-secondary" href="<?php echo url('categorias/editar/' . $c['id']); ?>">Editar</a>
                            <form class="d-inline" method="POST" action="<?php echo url('categorias/excluir/' . $c['id']); ?>" onsubmit="return confirm('Excluir categoria?');">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







