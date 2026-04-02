<?php $titulo = 'Novo Produto'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Novo Produto</h2>
        <p class="page-header__subtitulo">Cadastre materiais com dados completos para rastreabilidade e consumo.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('produtos'); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Selecione</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?php echo e($c['id']); ?>"><?php echo e($c['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Código interno</label>
                    <input type="text" name="codigo_interno" class="form-control">
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 mb-3">
                    <label class="form-label">Unidade</label>
                    <select name="unidade_medida" class="form-select">
                        <option value="">Selecione</option>
                        <?php foreach ($unidades as $u): ?>
                            <option value="<?php echo e($u); ?>"><?php echo e($u); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">Estoque atual</label>
                    <input type="number" step="0.01" name="estoque_atual" class="form-control" value="0">
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">Estoque mínimo</label>
                    <input type="number" step="0.01" name="estoque_minimo" class="form-control" value="0">
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Localização</label>
                    <input type="text" name="localizacao" class="form-control">
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Imagem</label>
                    <input type="file" name="imagem" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control" rows="3"></textarea>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Salvar produto</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('produtos'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
