<?php $titulo = 'Cadastro por Convite'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="login-shell">
    <aside class="login-hero">
        <div>
            <img src="<?php echo url('img/logo-grand-oca.png'); ?>" alt="Grand Oca Maragogi Resort" class="login-logo">
        </div>
        <div>
            <h1>Crie sua conta de acesso</h1>
            <p>Este cadastro foi liberado por convite. Depois de concluir, voce podera acessar o sistema normalmente.</p>
        </div>
        <div class="small">OCA MantStock • Cadastro seguro por link temporario</div>
    </aside>

    <div class="login-form-wrap">
        <h2>Aceitar convite</h2>
        <p class="sub">Preencha os dados para finalizar seu usuario.</p>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger"><?php echo e($erro); ?></div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="alert alert-success"><?php echo e($sucesso); ?></div>
            <a class="btn btn-primary" href="<?php echo url('login'); ?>">Ir para login</a>
        <?php elseif ($convite): ?>
            <div class="mb-3 p-3 rounded border" style="border-color: var(--borda-suave); background: var(--bg-superficie-secundaria);">
                <div><strong>E-mail:</strong> <?php echo e($convite['email']); ?></div>
                <div><strong>Perfil:</strong> <?php echo e($convite['tipo_usuario']); ?></div>
                <div><strong>Expira em:</strong> <?php echo e($convite['expira_em']); ?></div>
            </div>

            <form method="POST" action="<?php echo url('cadastro/aceitar'); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="token" value="<?php echo e($token); ?>">
                <div class="mb-3">
                    <label class="form-label" for="cadastro-nome">Nome completo</label>
                    <input type="text" id="cadastro-nome" name="nome" class="form-control" value="<?php echo e($dadosForm['nome'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="cadastro-senha">Senha</label>
                    <input type="password" id="cadastro-senha" name="senha" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="cadastro-confirmar">Confirmar senha</label>
                    <input type="password" id="cadastro-confirmar" name="confirmar_senha" class="form-control" required>
                </div>
                <button class="btn btn-primary" type="submit">Criar minha conta</button>
            </form>
        <?php else: ?>
            <div class="empty-state">
                Convite invalido, expirado ou inexistente.
            </div>
            <a class="btn btn-outline-secondary mt-3" href="<?php echo url('login'); ?>">Voltar para login</a>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
