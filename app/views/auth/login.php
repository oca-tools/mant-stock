<?php $titulo = 'Login'; require __DIR__ . '/../layouts/header.php'; ?>
<section class="login-shell">
    <aside class="login-hero">
        <div>
            <img src="<?php echo asset_url('img/logo-grand-oca.png'); ?>" alt="Grand Oca Maragogi Resort" class="login-logo">
        </div>
        <div>
            <h1>Gestão Inteligente de Estoque da Manutenção</h1>
            <p>Controle materiais, saídas, inventários e auditoria em um fluxo profissional para a operação diária do resort.</p>
        </div>
        <div class="small">Ambiente interno seguro • Grand Oca Maragogi Resort</div>
    </aside>

    <div class="login-form-wrap">
        <h2>Acessar sistema</h2>
        <p class="sub">Use suas credenciais para continuar.</p>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?php echo e($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo url('login'); ?>" novalidate>
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label" for="campo-email">E-mail</label>
                <input type="email" id="campo-email" name="email" class="form-control" autocomplete="username" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="campo-senha">Senha</label>
                <input type="password" id="campo-senha" name="senha" class="form-control" autocomplete="current-password" required>
            </div>
            <button class="btn btn-primary" type="submit">Entrar</button>
        </form>
        <p class="sub mt-3 mb-0">Nao possui acesso? Solicite um convite ao administrador.</p>
    </div>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
