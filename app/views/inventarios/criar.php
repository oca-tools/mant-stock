<?php $titulo = 'Registrar Contagem de Inventario'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $produtoSelecionado = (string)($dadosForm['produto_id'] ?? ''); ?>
<?php $quantidadeReal = (string)($dadosForm['quantidade_real'] ?? ''); ?>
<?php $motivoAjuste = (string)($dadosForm['motivo_ajuste'] ?? ''); ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Registrar Contagem</h2>
        <p class="page-header__subtitulo">Competencia <?php echo e($competencia); ?>. Os ajustes serao aplicados somente no fechamento do ciclo mensal.</p>
    </div>
    <div class="page-header__acoes">
        <a class="btn btn-outline-secondary" href="<?php echo url('inventarios?competencia=' . urlencode($competencia)); ?>">Voltar</a>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel mb-3">
    <div class="panel__body">
        <div class="row g-3">
            <div class="col-lg-4">
                <label class="form-label">Competencia</label>
                <input type="text" class="form-control" value="<?php echo e($competencia); ?>" readonly>
            </div>
            <div class="col-lg-4">
                <label class="form-label">Status do ciclo</label>
                <input type="text" class="form-control" value="<?php echo e($ciclo['status']); ?>" readonly>
            </div>
            <div class="col-lg-4">
                <label class="form-label">Aberto em</label>
                <input type="text" class="form-control" value="<?php echo e($ciclo['data_abertura']); ?>" readonly>
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('inventarios'); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="competencia" value="<?php echo e($competencia); ?>">

            <div class="row">
                <div class="col-lg-7 mb-3">
                    <label class="form-label">Produto</label>
                    <input type="text" class="form-control mb-2 js-filtrar-select" data-alvo="produto_id" placeholder="Digite para filtrar produtos">
                    <select name="produto_id" class="form-select" required>
                        <option value="">Selecione</option>
                        <?php foreach ($produtos as $produto): ?>
                            <option value="<?php echo e($produto['id']); ?>" <?php echo $produtoSelecionado === (string)$produto['id'] ? 'selected' : ''; ?>>
                                <?php echo e($produto['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">Qtd. real</label>
                    <input type="number" step="0.01" min="0" name="quantidade_real" class="form-control" value="<?php echo e($quantidadeReal); ?>" required>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Motivo da divergencia</label>
                    <input type="text" name="motivo_ajuste" class="form-control" value="<?php echo e($motivoAjuste); ?>" placeholder="Obrigatorio se houver diferenca">
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Salvar contagem</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('inventarios?competencia=' . urlencode($competencia)); ?>">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
