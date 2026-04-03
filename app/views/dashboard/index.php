<?php $titulo = 'Dashboard'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<?php $totalBaixo = count($estoqueBaixo); ?>
<?php $totalMovimentacoes = count($movimentacoes); ?>
<?php $totalDescartes = array_sum(array_map(static function ($item) { return (float)($item['total_descarte'] ?? 0); }, $maisDescartados)); ?>
<?php $flashLgpd = flash_get('lgpd'); ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Visão Geral da Operação</h2>
        <p class="page-header__subtitulo">Indicadores do estoque da manutenção com foco em decisão rápida.</p>
    </div>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <div class="page-header__acoes">
            <a class="btn btn-primary" href="<?php echo url('entradas/criar'); ?>"><i class="bi bi-plus-circle me-1"></i>Nova Entrada</a>
            <a class="btn btn-outline-primary" href="<?php echo url('saidas/criar'); ?>"><i class="bi bi-dash-circle me-1"></i>Nova Saída</a>
            <a class="btn btn-outline-primary" href="<?php echo url('inventarios'); ?>"><i class="bi bi-clipboard-check me-1"></i>Inventário Mensal</a>
        </div>
    <?php endif; ?>
</section>

<?php if ($flashLgpd): ?>
    <div class="alert alert-<?php echo e($flashLgpd['tipo']); ?>"><?php echo e($flashLgpd['mensagem']); ?></div>
<?php endif; ?>

<div class="row g-3 mb-1">
    <div class="col-xl-3 col-sm-6">
        <article class="metric-card metric-card--azul">
            <div class="metric-card__rotulo">Total de Produtos</div>
            <div class="metric-card__valor"><?php echo e($totalProdutos); ?></div>
            <div class="metric-card__detalhe">Base cadastrada no almoxarifado</div>
        </article>
    </div>
    <div class="col-xl-3 col-sm-6">
        <article class="metric-card metric-card--laranja">
            <div class="metric-card__rotulo">Estoque Baixo</div>
            <div class="metric-card__valor"><?php echo e($totalBaixo); ?></div>
            <div class="metric-card__detalhe">Itens abaixo do mínimo definido</div>
        </article>
    </div>
    <div class="col-xl-3 col-sm-6">
        <article class="metric-card metric-card--verde">
            <div class="metric-card__rotulo">Últimas Movimentações</div>
            <div class="metric-card__valor"><?php echo e($totalMovimentacoes); ?></div>
            <div class="metric-card__detalhe">Transações recentes registradas</div>
        </article>
    </div>
    <div class="col-xl-3 col-sm-6">
        <article class="metric-card metric-card--vinho">
            <div class="metric-card__rotulo">Descartes (Período)</div>
            <div class="metric-card__valor"><?php echo e(number_format($totalDescartes, 0, ',', '.')); ?></div>
            <div class="metric-card__detalhe">Total usado para reposição</div>
        </article>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-xl-7">
        <section class="panel">
            <div class="panel__body">
                <h3 class="panel__titulo">Produtos com Estoque Baixo</h3>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Atual</th>
                                <th>Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($estoqueBaixo)): ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="bi bi-check2-circle"></i>
                                        Nenhum item crítico no momento.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($estoqueBaixo as $item): ?>
                                <tr class="table-warning">
                                    <td><?php echo e($item['nome']); ?></td>
                                    <td><?php echo e($item['categoria_nome']); ?></td>
                                    <td><?php echo e($item['estoque_atual']); ?></td>
                                    <td><?php echo e($item['estoque_minimo']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-5">
        <section class="panel h-100">
            <div class="panel__body">
                <h3 class="panel__titulo">Últimas Movimentações</h3>
                <ul class="list-group list-group-flush">
                    <?php if (empty($movimentacoes)): ?>
                        <li class="list-group-item text-muted">Sem movimentações recentes registradas.</li>
                    <?php else: ?>
                        <?php foreach ($movimentacoes as $mov): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <strong><?php echo e($mov['produto_nome']); ?></strong>
                                    <div class="text-muted small"><?php echo e($mov['data_movimentacao']); ?></div>
                                </div>
                                <span class="status-pill <?php echo $mov['tipo_movimentacao'] === 'saida' ? 'status-pill--alerta' : 'status-pill--ok'; ?>">
                                    <?php echo e(ucfirst($mov['tipo_movimentacao'])); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </section>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-xl-6">
        <section class="panel">
            <div class="panel__body">
                <h3 class="panel__titulo">Materiais Mais Utilizados</h3>
                <div class="grafico-box">
                    <canvas id="graficoUso"></canvas>
                </div>
                <div class="grafico-resumo grafico-resumo--primario">
                    <h4 class="grafico-resumo__titulo">Resumo do consumo</h4>
                    <?php if (empty($maisUtilizados)): ?>
                        <div class="empty-state py-3">Sem dados de saida para o periodo.</div>
                    <?php else: ?>
                        <ul class="grafico-resumo__lista">
                            <?php foreach ($maisUtilizados as $item): ?>
                                <li class="grafico-resumo__item">
                                    <span class="grafico-resumo__produto"><?php echo e($item['nome']); ?></span>
                                    <span class="grafico-resumo__quantidade">
                                        <?php echo e(number_format((float)$item['total_saida'], 2, ',', '.')); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
    <div class="col-xl-6">
        <section class="panel">
            <div class="panel__body">
                <h3 class="panel__titulo">Materiais Descartados</h3>
                <div class="grafico-box">
                    <canvas id="graficoDescarte"></canvas>
                </div>
                <div class="grafico-resumo grafico-resumo--perigo">
                    <h4 class="grafico-resumo__titulo">Resumo dos descartes</h4>
                    <?php if (empty($maisDescartados)): ?>
                        <div class="empty-state py-3">Sem descartes no periodo.</div>
                    <?php else: ?>
                        <ul class="grafico-resumo__lista">
                            <?php foreach ($maisDescartados as $item): ?>
                                <li class="grafico-resumo__item">
                                    <span class="grafico-resumo__produto"><?php echo e($item['nome']); ?></span>
                                    <span class="grafico-resumo__quantidade">
                                        <?php echo e(number_format((float)$item['total_descarte'], 2, ',', '.')); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
function corCss(nome, fallback) {
    var valor = getComputedStyle(document.documentElement).getPropertyValue(nome).trim();
    return valor || fallback;
}

var corPrimaria = corCss('--cor-primaria-500', '#2c6cb8');
var corPerigo = corCss('--cor-perigo', '#d64545');
var corTexto = corCss('--texto-principal', '#111c2d');
var corGrade = corCss('--borda-suave', '#dde5ef');

var opcoesPadrao = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: corGrade },
            ticks: { color: corTexto }
        },
        x: {
            grid: { display: false },
            ticks: { color: corTexto }
        }
    }
};

new Chart(document.getElementById('graficoUso'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($maisUtilizados, 'nome')); ?>,
        datasets: [{
            label: 'Materiais mais utilizados',
            data: <?php echo json_encode(array_column($maisUtilizados, 'total_saida')); ?>,
            backgroundColor: corPrimaria,
            borderRadius: 8
        }]
    },
    options: opcoesPadrao
});

new Chart(document.getElementById('graficoDescarte'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($maisDescartados, 'nome')); ?>,
        datasets: [{
            label: 'Materiais descartados',
            data: <?php echo json_encode(array_column($maisDescartados, 'total_descarte')); ?>,
            backgroundColor: corPerigo,
            borderRadius: 8
        }]
    },
    options: opcoesPadrao
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
