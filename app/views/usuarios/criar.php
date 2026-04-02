<?php $titulo = 'Novo Usuário'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Novo Usuário</h2>
        <p class="page-header__subtitulo">Cadastre colaboradores para auditoria e rastreio de responsabilidades.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<div class="alert alert-primary">
    O sistema permite mais de uma conta no mesmo e-mail, desde que a senha seja diferente entre elas.
</div>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('usuarios'); ?>">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="col-lg-6 mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Perfil</label>
                    <select name="tipo_usuario" class="form-select" required>
                        <option value="">Selecione</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Almoxarifado">Almoxarifado</option>
                        <option value="Consulta">Consulta</option>
                    </select>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="ativo" id="status-ativo" checked>
                        <label class="form-check-label" for="status-ativo">Ativo</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Salvar usuário</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('usuarios'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
