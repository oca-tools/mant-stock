<?php $titulo = 'Nova Saida'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Nova Saida</h2>
        <p class="page-header__subtitulo">Registre consumo de materiais por setor, local e solicitante.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('saidas'); ?>">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-xl-7">
                    <div class="form-section">
                        <h3 class="form-section__title">Item consumido</h3>
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
                        <h3 class="form-section__title">Destino</h3>
                        <div class="row g-3">
                            <div class="col-md-6 col-xl-12">
                                <label class="form-label">Setor</label>
                                <input type="text" name="setor" class="form-control">
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <label class="form-label">Local de utilizacao</label>
                                <input type="text" name="local_utilizacao" class="form-control" placeholder="Apartamento, bloco, piscina...">
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <label class="form-label">Solicitante</label>
                                <input type="text" name="solicitante" class="form-control" required>
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <label class="form-label">Observações</label>
                                <textarea name="observacoes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap mt-3">
                <?php $modalId = 'modal-confirmar-saida'; $acaoConfirmacao = 'Registrar saida e gerar comprovante'; require __DIR__ . '/../partials/confirmacao_operacional_modal.php'; ?>
                <a class="btn btn-outline-secondary" href="<?php echo url('saidas'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
