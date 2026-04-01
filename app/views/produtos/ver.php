<?php $titulo = 'Detalhes do Produto'; require __DIR__ . '/../layouts/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3><?php echo e($produto['nome']); ?></h3>
        <div class="text-muted">Código: <?php echo e($produto['codigo_interno']); ?></div>
    </div>
    <a class="btn btn-outline-secondary" href="<?php echo url('produtos'); ?>">Voltar</a>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($produto['imagem'])): ?>
                    <img src="<?php echo url('uploads/' . $produto['imagem']); ?>" class="img-fluid rounded mb-3" alt="Produto">
                <?php endif; ?>
                <div><strong>Categoria:</strong> <?php echo e($produto['categoria_nome']); ?></div>
                <div><strong>Unidade:</strong> <?php echo e($produto['unidade_medida']); ?></div>
                <div><strong>Estoque Atual:</strong> <?php echo e($produto['estoque_atual']); ?></div>
                <div><strong>Estoque Mínimo:</strong> <?php echo e($produto['estoque_minimo']); ?></div>
                <div><strong>Localização:</strong> <?php echo e($produto['localizacao']); ?></div>
                <div><strong>Observações:</strong> <?php echo e($produto['observacoes']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5>Últimas Movimentações</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Usuario</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movimentacoes as $m): ?>
                                <tr>
                                    <td><?php echo e($m['data_movimentacao']); ?></td>
                                    <td><?php echo e($m['tipo_movimentacao']); ?></td>
                                    <td><?php echo e($m['quantidade']); ?></td>
                                    <td><?php echo e($m['usuario_nome']); ?></td>
                                    <td><?php echo e($m['observacoes']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







