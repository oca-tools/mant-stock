<?php $titulo = 'Novo Inventário'; require __DIR__ . '/../layouts/header.php'; ?>
<h3>Novo Inventário</h3>
<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>
<form method="POST" action="<?php echo url('inventarios'); ?>">
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
        <div class="col-md-3 mb-3">
            <label class="form-label">Quantidade Real</label>
            <input type="number" step="0.01" name="quantidade_real" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Motivo do Ajuste (se houver)</label>
            <input type="text" name="motivo_ajuste" class="form-control" placeholder="Ex: Inventário fisico, perda, avaria">
        </div>
    </div>
    <button class="btn btn-primary">Registrar</button>
    <a class="btn btn-secondary" href="<?php echo url('inventarios'); ?>">Voltar</a>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







