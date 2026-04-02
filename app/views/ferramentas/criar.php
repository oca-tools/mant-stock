<?php $titulo = 'Nova Ferramenta'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Nova Ferramenta</h2>
        <p class="page-header__subtitulo">Registre novas ferramentas para controle de disponibilidade e emprestimo.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('ferramentas'); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descricao</label>
                <textarea name="descricao" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha de confirmacao</label>
                <input type="password" name="senha_confirmacao" class="form-control" autocomplete="current-password" required>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Salvar ferramenta</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('ferramentas'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
