<?php $titulo = 'Editar Categoria'; require __DIR__ . '/../layouts/header.php'; ?>
<h3>Editar Categoria</h3>
<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>
<form method="POST" action="<?php echo url('categorias/editar/' . $categoria['id']); ?>">
    <?php echo csrf_field(); ?>
    <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" value="<?php echo e($categoria['nome']); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control"><?php echo e($categoria['descricao']); ?></textarea>
    </div>
    <button class="btn btn-primary">Atualizar</button>
    <a class="btn btn-secondary" href="<?php echo url('categorias'); ?>">Voltar</a>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







