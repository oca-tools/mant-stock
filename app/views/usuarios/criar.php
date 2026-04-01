<?php $titulo = 'Novo Usuario'; require __DIR__ . '/../layouts/header.php'; ?>
<h3>Novo Usuario</h3>
<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>
<form method="POST" action="<?php echo url('usuarios'); ?>">
    <?php echo csrf_field(); ?>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Perfil</label>
            <select name="tipo_usuario" class="form-select" required>
                <option value="">Selecione</option>
                <option value="Administrador">Administrador</option>
                <option value="Almoxarifado">Almoxarifado</option>
                <option value="Consulta">Consulta</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Status</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="ativo" checked>
                <label class="form-check-label">Ativo</label>
            </div>
        </div>
    </div>
    <button class="btn btn-primary">Salvar</button>
    <a class="btn btn-secondary" href="<?php echo url('usuarios'); ?>">Voltar</a>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
