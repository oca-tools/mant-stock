<?php $titulo = 'Estoque Atual'; require __DIR__ . '/../layouts/header.php'; ?>
<?php
$totalPagina = count($produtos);
$totalBaixo = 0;
$totalOk = 0;
$somaEstoque = 0.0;

foreach ($produtos as $itemResumo) {
    $atualResumo = (float)($itemResumo['estoque_atual'] ?? 0);
    $minimoResumo = (float)($itemResumo['estoque_minimo'] ?? 0);
    $somaEstoque += $atualResumo;
    if ($atualResumo <= $minimoResumo) {
        $totalBaixo++;
    } else {
        $totalOk++;
    }
}
?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Estoque Atual</h2>
        <p class="page-header__subtitulo">Visualização operacional com foco em leitura rápida de disponibilidade.</p>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <form class="row g-2" method="GET" action="<?php echo url('estoque'); ?>">
            <div class="col-lg-5">
                <label class="form-label">Busca</label>
                <input type="text" name="busca" class="form-control" placeholder="Buscar por nome, código ou local" value="<?php echo e($busca); ?>">
            </div>
            <div class="col-lg-3">
                <label class="form-label">Ordenação</label>
                <select name="ordem" class="form-select">
                    <option value="az" <?php echo ($ordem === 'az') ? 'selected' : ''; ?>>A-Z</option>
                    <option value="za" <?php echo ($ordem === 'za') ? 'selected' : ''; ?>>Z-A</option>
                </select>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">Aplicar</button>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <a class="btn btn-outline-primary w-100" href="<?php echo url('estoque'); ?>">Limpar</a>
            </div>
        </form>
    </div>
</section>

<section class="estoque-quick mt-3">
    <article class="estoque-quick__card">
        <span class="estoque-quick__label">Itens na página</span>
        <strong class="estoque-quick__valor"><?php echo e($totalPagina); ?></strong>
    </article>
    <article class="estoque-quick__card">
        <span class="estoque-quick__label">Em equilíbrio</span>
        <strong class="estoque-quick__valor"><?php echo e($totalOk); ?></strong>
    </article>
    <article class="estoque-quick__card">
        <span class="estoque-quick__label">Abaixo do mínimo</span>
        <strong class="estoque-quick__valor"><?php echo e($totalBaixo); ?></strong>
    </article>
    <article class="estoque-quick__card">
        <span class="estoque-quick__label">Saldo somado</span>
        <strong class="estoque-quick__valor"><?php echo e(number_format($somaEstoque, 2, ',', '.')); ?></strong>
    </article>
</section>

<section class="panel mt-3 estoque-visualizacao" data-estoque-bloco>
    <div class="panel__body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h3 class="panel__titulo m-0">Itens em Estoque</h3>
            <div class="btn-group" role="group" aria-label="Visualização do estoque">
                <button type="button" class="btn btn-sm btn-outline-primary" data-estoque-view="lista">Lista Compacta</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-estoque-view="tabela">Tabela</button>
            </div>
        </div>

        <?php if (empty($produtos)): ?>
            <div class="empty-state">
                <i class="bi bi-inboxes"></i>
                Nenhum produto encontrado para os filtros aplicados.
            </div>
        <?php else: ?>
            <div data-estoque-content="lista">
                <div class="estoque-lista">
                    <?php foreach ($produtos as $p): ?>
                        <?php
                        $atual = (float)($p['estoque_atual'] ?? 0);
                        $minimo = (float)($p['estoque_minimo'] ?? 0);
                        $ratio = $minimo > 0 ? ($atual / $minimo) : 2;
                        $barra = (int)max(8, min(100, round($ratio * 50)));
                        $status = 'ok';
                        $textoStatus = 'Estável';
                        if ($ratio <= 1) {
                            $status = 'baixo';
                            $textoStatus = 'Baixo';
                        } elseif ($ratio <= 1.5) {
                            $status = 'atencao';
                            $textoStatus = 'Atenção';
                        }
                        ?>
                        <article class="estoque-linha estoque-linha--<?php echo e($status); ?>">
                            <div class="estoque-linha__principal">
                                <h4><?php echo e($p['nome']); ?></h4>
                                <p>
                                    <?php echo e($p['categoria_nome']); ?>
                                    <?php if (!empty($p['codigo_interno'])): ?>
                                        <span class="mx-1">•</span><?php echo e($p['codigo_interno']); ?>
                                    <?php endif; ?>
                                </p>
                                <small>Local: <?php echo e($p['localizacao'] ?: 'Não informado'); ?></small>
                            </div>

                            <div class="estoque-linha__indicador">
                                <div class="estoque-linha__indicador-topo">
                                    <span>Atual: <strong><?php echo e(number_format($atual, 2, ',', '.')); ?> <?php echo e($p['unidade_medida']); ?></strong></span>
                                    <span>Mínimo: <?php echo e(number_format($minimo, 2, ',', '.')); ?></span>
                                </div>
                                <div class="estoque-barra">
                                    <span style="width: <?php echo e($barra); ?>%"></span>
                                </div>
                            </div>

                            <div class="estoque-linha__status">
                                <span class="status-pill <?php echo $status === 'baixo' ? 'status-pill--erro' : ($status === 'atencao' ? 'status-pill--alerta' : 'status-pill--ok'); ?>">
                                    <?php echo e($textoStatus); ?>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div data-estoque-content="tabela" class="d-none">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Atual</th>
                                <th>Mínimo</th>
                                <th>Unidade</th>
                                <th>Localização</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos as $p): ?>
                                <?php
                                $atual = (float)($p['estoque_atual'] ?? 0);
                                $minimo = (float)($p['estoque_minimo'] ?? 0);
                                $statusTabela = $atual <= $minimo ? 'status-pill--erro' : 'status-pill--ok';
                                $textoTabela = $atual <= $minimo ? 'Baixo' : 'OK';
                                ?>
                                <tr class="<?php echo $atual <= $minimo ? 'table-warning' : ''; ?>">
                                    <td><strong><?php echo e($p['nome']); ?></strong></td>
                                    <td><?php echo e($p['categoria_nome']); ?></td>
                                    <td><?php echo e(number_format($atual, 2, ',', '.')); ?></td>
                                    <td><?php echo e(number_format($minimo, 2, ',', '.')); ?></td>
                                    <td><?php echo e($p['unidade_medida']); ?></td>
                                    <td><?php echo e($p['localizacao'] ?: 'Não informado'); ?></td>
                                    <td><span class="status-pill <?php echo e($statusTabela); ?>"><?php echo e($textoTabela); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<nav class="data-paginacao">
    <ul class="pagination flex-wrap">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo url('estoque') . '?pagina=' . $i . '&busca=' . urlencode($busca) . '&ordem=' . $ordem; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
