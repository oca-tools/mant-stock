<?php $titulo = 'Novo Emprestimo'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Novo Emprestimo</h2>
        <p class="page-header__subtitulo">Controle retirada de ferramentas com responsabilidade nominal.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('emprestimos'); ?>">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Ferramenta</label>
                    <input type="text" class="form-control mb-2 js-filtrar-select" data-alvo="ferramenta_id" placeholder="Digite para filtrar ferramentas">
                    <select name="ferramenta_id" class="form-select" required>
                        <option value="">Selecione</option>
                        <?php foreach ($ferramentas as $f): ?>
                            <option value="<?php echo e($f['id']); ?>"><?php echo e($f['nome']); ?> (<?php echo e($f['status']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Responsavel retirada</label>
                    <input type="text" name="usuario_responsavel" class="form-control" required>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Senha de confirmacao</label>
                    <input type="password" name="senha_confirmacao" class="form-control" autocomplete="current-password" required>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Registrar emprestimo</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('emprestimos'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
