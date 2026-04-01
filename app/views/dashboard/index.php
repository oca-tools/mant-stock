<?php $titulo = 'Dashboard'; require __DIR__ . '/../layouts/header.php'; ?>
<div class="row g-3">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <div class="fs-6">Total de Produtos</div>
                <div class="fs-3 fw-bold"><?php echo e($totalProdutos); ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <h5>Produtos com Estoque Baixo</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Estoque Atual</th>
                                <th>Estoque Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($estoqueBaixo as $item): ?>
                            <tr class="table-warning">
                                <td><?php echo e($item['nome']); ?></td>
                                <td><?php echo e($item['categoria_nome']); ?></td>
                                <td><?php echo e($item['estoque_atual']); ?></td>
                                <td><?php echo e($item['estoque_minimo']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5>Ações Rápidas</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-primary" href="<?php echo url('entradas/criar'); ?>">Nova Entrada</a>
                        <a class="btn btn-primary" href="<?php echo url('saidas/criar'); ?>">Nova Saída</a>
                        <a class="btn btn-primary" href="<?php echo url('descartes/criar'); ?>">Novo Descarte</a>
                        <a class="btn btn-outline-primary" href="<?php echo url('inventarios/criar'); ?>">Novo Inventário</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Últimas Movimentações</h5>
                <ul class="list-group list-group-flush">
                    <?php foreach ($movimentacoes as $mov): ?>
                        <li class="list-group-item">
                            <?php echo e($mov['data_movimentacao']); ?> - <?php echo e($mov['produto_nome']); ?> (<?php echo e($mov['tipo_movimentacao']); ?>)
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5>Gráficos</h5>
                <canvas id="graficoUso" height="180"></canvas>
                <canvas id="graficoDescarte" height="180" class="mt-4"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const dadosUso = {
    labels: <?php echo json_encode(array_column($maisUtilizados, 'nome')); ?>,
    datasets: [{
        label: 'Materiais mais utilizados',
        data: <?php echo json_encode(array_column($maisUtilizados, 'total_saida')); ?>,
        backgroundColor: '#0d6efd'
    }]
};

const dadosDescarte = {
    labels: <?php echo json_encode(array_column($maisDescartados, 'nome')); ?>,
    datasets: [{
        label: 'Materiais descartados',
        data: <?php echo json_encode(array_column($maisDescartados, 'total_descarte')); ?>,
        backgroundColor: '#dc3545'
    }]
};

new Chart(document.getElementById('graficoUso'), { type: 'bar', data: dadosUso });
new Chart(document.getElementById('graficoDescarte'), { type: 'bar', data: dadosDescarte });
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







