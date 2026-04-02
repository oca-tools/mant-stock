<?php $titulo = 'Editar Produto'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Editar Produto</h2>
        <p class="page-header__subtitulo">Atualize informações cadastrais e parâmetros de estoque.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('produtos/editar/' . $produto['id']); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" value="<?php echo e($produto['nome']); ?>" required>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Selecione</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?php echo e($c['id']); ?>" <?php echo ($produto['categoria_id'] == $c['id']) ? 'selected' : ''; ?>><?php echo e($c['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Código interno</label>
                    <input type="text" name="codigo_interno" class="form-control" value="<?php echo e($produto['codigo_interno']); ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-lg-2 mb-3">
                    <label class="form-label">Unidade</label>
                    <select name="unidade_medida" class="form-select">
                        <option value="">Selecione</option>
                        <?php foreach ($unidades as $u): ?>
                            <option value="<?php echo e($u); ?>" <?php echo ($produto['unidade_medida'] === $u) ? 'selected' : ''; ?>><?php echo e($u); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">Estoque atual</label>
                    <input type="number" step="0.01" name="estoque_atual" class="form-control" value="<?php echo e($produto['estoque_atual']); ?>">
                </div>
                <div class="col-lg-2 mb-3">
                    <label class="form-label">Estoque mínimo</label>
                    <input type="number" step="0.01" name="estoque_minimo" class="form-control" value="<?php echo e($produto['estoque_minimo']); ?>">
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Localização</label>
                    <input type="text" name="localizacao" class="form-control" value="<?php echo e($produto['localizacao']); ?>">
                </div>
                <div class="col-lg-3 mb-3">
                    <label class="form-label">Imagem</label>
                    <input type="file" name="imagem" class="form-control" accept="image/*">
                    <?php if (!empty($produto['imagem'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo url('uploads/' . $produto['imagem']); ?>" class="img-thumbnail" style="max-height:90px;" alt="Imagem atual do produto">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações</label>
                <textarea name="observacoes" class="form-control" rows="3"><?php echo e($produto['observacoes']); ?></textarea>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Atualizar produto</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('produtos'); ?>">Voltar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
