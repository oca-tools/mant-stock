<?php $titulo = 'Nova Entrada'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Nova Entrada</h2>
        <p class="page-header__subtitulo">Registre reposicoes e atualize automaticamente o saldo em estoque.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('entradas'); ?>">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Produto</label>
                    <input type="text" class="form-control mb-2 js-filtrar-select" data-alvo="produto_id" placeholder="Digite para filtrar produtos">
                    <select name="produto_id" class="form-select" required>
                        <option value="">Selecione</option>
                        <?php foreach ($produtos as $p): ?>
                            <option value="<?php echo e($p['id']); ?>"><?php echo e($p['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">Quantidade</label>
                    <input type="number" step="0.01" name="quantidade" class="form-control" required>
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Fornecedor</label>
                    <input type="text" name="fornecedor" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Nota fiscal</label>
                    <input type="text" name="nota_fiscal" class="form-control">
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Observacoes</label>
                    <input type="text" name="observacoes" class="form-control">
                </div>
                <div class="col-lg-4 mb-3">
                    <label class="form-label">Senha de confirmacao</label>
                    <input type="password" name="senha_confirmacao" class="form-control" autocomplete="current-password" required>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Registrar entrada</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('entradas'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
