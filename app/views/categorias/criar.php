<?php $titulo = 'Nova Categoria'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Nova Categoria</h2>
        <p class="page-header__subtitulo">Defina agrupamentos para organizar os materiais da manutenção.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('categorias'); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descrição</label>
                <textarea name="descricao" class="form-control" rows="3"></textarea>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Salvar categoria</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('categorias'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
