<?php $titulo = 'Novo Descarte'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Novo Descarte</h2>
        <p class="page-header__subtitulo">Registre materiais devolvidos para descarte e posterior reporte à controladoria.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('descartes'); ?>">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-xl-7">
                    <div class="form-section">
                        <h3 class="form-section__title">Material descartado</h3>
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <label class="form-label">Produto</label>
                                <input type="text" class="form-control mb-2 js-filtrar-select" data-alvo="produto_id" placeholder="Digite para filtrar produtos">
                                <select name="produto_id" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($produtos as $p): ?>
                                        <option value="<?php echo e($p['id']); ?>"><?php echo e($p['nome']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">Quantidade</label>
                                <input type="number" step="0.01" name="quantidade" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="form-section">
                        <h3 class="form-section__title">Evidência do descarte</h3>
                        <div class="mb-3">
                            <label class="form-label">Motivo / condição do material</label>
                            <input type="text" name="motivo_descarte" class="form-control" placeholder="Rolo vazio, lâmpada queimada, peça quebrada..." required>
                        </div>
                        <div>
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-primary mt-3 mb-0">
                O descarte registra a devolução física do material usado ou inutilizado. O saldo de estoque não é debitado por este lançamento.
            </div>

            <div class="d-flex gap-2 flex-wrap mt-3">
                <?php $modalId = 'modal-confirmar-descarte'; $acaoConfirmacao = 'Registrar descarte'; require __DIR__ . '/../partials/confirmacao_operacional_modal.php'; ?>
                <a class="btn btn-outline-secondary" href="<?php echo url('descartes'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
