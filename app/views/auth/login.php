<?php $titulo = 'Login'; require __DIR__ . '/../layouts/header.php'; ?>
<div class="row justify-content-center login-page">
    <div class="col-md-6">
        <div class="card shadow-sm login-card">
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="<?php echo url('img/logo-grand-oca.png'); ?>" alt="Grand Oca" class="login-logo mb-2">
                    <h4 class="mb-1">Acesso ao Sistema</h4>
                    <div class="text-muted">Controle de Estoque da Manutenção</div>
                </div>
                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger"><?php echo e($erro); ?></div>
                <?php endif; ?>
                <form method="POST" action="<?php echo url('login'); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







