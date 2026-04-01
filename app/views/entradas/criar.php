<?php $titulo = 'Nova Entrada'; require __DIR__ . '/../layouts/header.php'; ?>
<h3>Nova Entrada</h3>
<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>
<form method="POST" action="<?php echo url('entradas'); ?>">
    <?php echo csrf_field(); ?>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Produto</label>
            <input type="text" class="form-control mb-2 js-filtrar-select" data-alvo="produto_id" placeholder="Digite para filtrar produtos">
            <select name="produto_id" class="form-select" required>
                <option value="">Selecione</option>
                <?php foreach ($produtos as $p): ?>
                    <option value="<?php echo e($p['id']); ?>"><?php echo e($p['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 mb-3">
            <label class="form-label">Quantidade</label>
            <input type="number" step="0.01" name="quantidade" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Fornecedor</label>
            <input type="text" name="fornecedor" class="form-control">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Nota Fiscal</label>
            <input type="text" name="nota_fiscal" class="form-control">
        </div>
        <div class="col-md-8 mb-3">
            <label class="form-label">Observações</label>
            <input type="text" name="observacoes" class="form-control">
        </div>
    </div>
    <button class="btn btn-primary">Registrar</button>
    <a class="btn btn-secondary" href="<?php echo url('entradas'); ?>">Voltar</a>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







