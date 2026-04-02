<?php $titulo = 'Editar Usuário'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Editar Usuário</h2>
        <p class="page-header__subtitulo">Ajuste permissões, status e dados de acesso do colaborador.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<div class="alert alert-primary">
    Se houver mais de uma conta com este e-mail, cada conta deve manter senha diferente.
</div>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('usuarios/editar/' . $usuario['id']); ?>">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" value="<?php echo e($usuario['nome']); ?>" required>
                </div>
                <div class="col-lg-6 mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e($usuario['email']); ?>" required>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Nova senha (opcional)</label>
                    <input type="password" name="senha" class="form-control">
                    <small class="text-muted">Obrigatoria se o e-mail informado ja existir em outra conta.</small>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Perfil</label>
                    <select name="tipo_usuario" class="form-select" required>
                        <option value="Administrador" <?php echo $usuario['tipo_usuario'] === 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                        <option value="Almoxarifado" <?php echo $usuario['tipo_usuario'] === 'Almoxarifado' ? 'selected' : ''; ?>>Almoxarifado</option>
                        <option value="Consulta" <?php echo $usuario['tipo_usuario'] === 'Consulta' ? 'selected' : ''; ?>>Consulta</option>
                    </select>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="ativo" id="status-ativo-editar" <?php echo $usuario['ativo'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status-ativo-editar">Ativo</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Atualizar usuário</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('usuarios'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
