<?php $titulo = 'Novo Empréstimo'; require __DIR__ . '/../layouts/header.php'; ?>
<h3>Novo Empréstimo</h3>
<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>
<form method="POST" action="<?php echo url('emprestimos'); ?>">
    <?php echo csrf_field(); ?>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Ferramenta</label>
            <input type="text" class="form-control mb-2 js-filtrar-select" data-alvo="ferramenta_id" placeholder="Digite para filtrar ferramentas">
            <select name="ferramenta_id" class="form-select" required>
                <option value="">Selecione</option>
                <?php foreach ($ferramentas as $f): ?>
                    <option value="<?php echo e($f['id']); ?>"><?php echo e($f['nome']); ?> (<?php echo e($f['status']); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Responsavel</label>
            <input type="text" name="usuario_responsavel" class="form-control" required>
        </div>
    </div>
    <button class="btn btn-primary">Registrar</button>
    <a class="btn btn-secondary" href="<?php echo url('emprestimos'); ?>">Voltar</a>
</form>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







