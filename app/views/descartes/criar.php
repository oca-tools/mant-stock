<?php $titulo = 'Novo Descarte'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Novo Descarte</h2>
        <p class="page-header__subtitulo">Registre materiais descartados para suporte ao planejamento de reposição.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('descartes'); ?>">
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
                    <label class="form-label">Motivo do descarte</label>
                    <input type="text" name="motivo_descarte" class="form-control" placeholder="Lata vazia, peça quebrada..." required>
                </div>
            </div>
            <div class="alert alert-primary">
                O descarte não debita o estoque principal. Este registro apoia análise de consumo e reposição.
            </div>
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Item recebido em troca</label>
                    <input type="text" name="item_recebido_troca" class="form-control">
                </div>
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Observações</label>
                    <input type="text" name="observacoes" class="form-control">
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Registrar descarte</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('descartes'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
