<?php $titulo = 'Inventario Mensal'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<?php $podeOperar = in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true); ?>
<?php $flash = flash_get('inventarios'); ?>
<?php $statusCiclo = $ciclo['status'] ?? 'nao_iniciado'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Inventario Mensal</h2>
        <p class="page-header__subtitulo">Contagem fisica manual por competencia mensal, com fechamento e ajuste controlado.</p>
    </div>
    <?php if ($podeOperar && $ciclo && $statusCiclo === 'aberto'): ?>
        <div class="page-header__acoes">
            <a class="btn btn-primary" href="<?php echo url('inventarios/criar?competencia=' . urlencode($competencia)); ?>">
                <i class="bi bi-plus-circle me-1"></i>Registrar Contagem
            </a>
        </div>
    <?php endif; ?>
</section>

<?php if ($flash): ?>
    <div class="alert alert-<?php echo e($flash['tipo']); ?>"><?php echo e($flash['mensagem']); ?></div>
<?php endif; ?>

<section class="panel mb-3">
    <div class="panel__body">
        <form class="row g-2 align-items-end" method="GET" action="<?php echo url('inventarios'); ?>">
            <div class="col-lg-3">
                <label class="form-label">Competencia</label>
                <input type="month" name="competencia" class="form-control" value="<?php echo e($competencia); ?>">
            </div>
            <div class="col-lg-4">
                <label class="form-label">Atalhos recentes</label>
                <select class="form-select" onchange="if (this.value) { window.location.href = this.value; }">
                    <option value="">Selecione</option>
                    <?php foreach ($competenciasRecentes as $competenciaItem): ?>
                        <option value="<?php echo url('inventarios?competencia=' . urlencode($competenciaItem)); ?>" <?php echo $competenciaItem === $competencia ? 'selected' : ''; ?>>
                            <?php echo e($competenciaItem); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button class="btn btn-outline-primary w-100" type="submit">Carregar</button>
            </div>
            <div class="col-lg-3 d-flex align-items-end">
                <?php if ($statusCiclo === 'aberto'): ?>
                    <span class="status-pill status-pill--ok">Ciclo aberto</span>
                <?php elseif ($statusCiclo === 'fechado'): ?>
                    <span class="status-pill status-pill--alerta">Ciclo fechado</span>
                <?php else: ?>
                    <span class="status-pill status-pill--erro">Ciclo nao iniciado</span>
                <?php endif; ?>
            </div>
        </form>
    </div>
</section>

<div class="row g-3 mb-3">
    <div class="col-xl-3 col-sm-6">
        <article class="metric-card metric-card--azul">
            <div class="metric-card__rotulo">Produtos cadastrados</div>
            <div class="metric-card__valor"><?php echo e($totalProdutos); ?></div>
            <div class="metric-card__detalhe">Base total para conferencia mensal</div>
        </article>
    </div>
    <div class="col-xl-3 col-sm-6">
        <article class="metric-card metric-card--verde">
            <div class="metric-card__rotulo">Produtos contados</div>
            <div class="metric-card__valor"><?php echo e($resumo['total_contados']); ?></div>
            <div class="metric-card__detalhe">Itens ja conferidos na competencia</div>
        </article>
    </div>
    <div class="col-xl-3 col-sm-6">
        <article class="metric-card metric-card--laranja">
            <div class="metric-card__rotulo">Pendentes</div>
            <div class="metric-card__valor"><?php echo e($resumo['total_pendentes']); ?></div>
            <div class="metric-card__detalhe">Produtos ainda sem contagem no mes</div>
        </article>
    </div>
    <div class="col-xl-3 col-sm-6">
        <article class="metric-card metric-card--vinho">
            <div class="metric-card__rotulo">Divergencias</div>
            <div class="metric-card__valor"><?php echo e($resumo['total_divergencias']); ?></div>
            <div class="metric-card__detalhe">Contagens com diferenca entre fisico e sistema</div>
        </article>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <section class="panel">
            <div class="panel__body">
                <h3 class="panel__titulo">Contagens da competencia <?php echo e($competencia); ?></h3>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Qtd. Sistema</th>
                                <th>Qtd. Real</th>
                                <th>Diferenca</th>
                                <th>Motivo</th>
                                <th>Contado por</th>
                                <th>Data</th>
                                <th>Ajuste</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($inventarios)): ?>
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <i class="bi bi-clipboard2-check"></i>
                                            Nenhuma contagem registrada para esta competencia.
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($inventarios as $item): ?>
                                    <?php $diferenca = (float)$item['diferenca']; ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($item['produto_nome']); ?></strong>
                                            <div class="text-muted small"><?php echo e($item['unidade_medida']); ?></div>
                                        </td>
                                        <td><?php echo e(number_format((float)$item['quantidade_sistema'], 2, ',', '.')); ?></td>
                                        <td><?php echo e(number_format((float)$item['quantidade_real'], 2, ',', '.')); ?></td>
                                        <td>
                                            <?php if (abs($diferenca) <= 0.00001): ?>
                                                <span class="status-pill status-pill--ok">Sem diferenca</span>
                                            <?php elseif ($diferenca > 0): ?>
                                                <span class="status-pill status-pill--ok">+<?php echo e(number_format($diferenca, 2, ',', '.')); ?></span>
                                            <?php else: ?>
                                                <span class="status-pill status-pill--erro"><?php echo e(number_format($diferenca, 2, ',', '.')); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($item['motivo_ajuste']); ?></td>
                                        <td><?php echo e($item['usuario_nome']); ?></td>
                                        <td><?php echo e($item['data_inventario']); ?></td>
                                        <td>
                                            <?php if ((int)$item['ajuste_aplicado'] === 1): ?>
                                                <span class="status-pill status-pill--ok">Aplicado</span>
                                            <?php else: ?>
                                                <span class="status-pill status-pill--alerta">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="panel mb-3">
            <div class="panel__body">
                <h3 class="panel__titulo">Gestao do ciclo mensal</h3>
                <?php if (!$ciclo): ?>
                    <p class="text-muted mb-3">Nenhum ciclo iniciado para <strong><?php echo e($competencia); ?></strong>.</p>
                    <?php if ($podeOperar): ?>
                        <form method="POST" action="<?php echo url('inventarios/abrir'); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="competencia" value="<?php echo e($competencia); ?>">
                            <div class="mb-2">
                                <label class="form-label">Observacoes de abertura (opcional)</label>
                                <input type="text" name="observacoes_abertura" class="form-control" placeholder="Ex.: Inventario mensal regular">
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Abrir ciclo mensal</button>
                        </form>
                    <?php else: ?>
                        <div class="empty-state">Apenas perfis operacionais podem abrir o ciclo.</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="mb-2"><strong>Status:</strong> <?php echo e($ciclo['status']); ?></div>
                    <div class="mb-2"><strong>Abertura:</strong> <?php echo e($ciclo['data_abertura']); ?></div>
                    <div class="mb-2"><strong>Responsavel:</strong> <?php echo e($ciclo['usuario_abertura_nome']); ?></div>
                    <?php if (!empty($ciclo['data_fechamento'])): ?>
                        <div class="mb-2"><strong>Fechamento:</strong> <?php echo e($ciclo['data_fechamento']); ?></div>
                        <div class="mb-2"><strong>Fechado por:</strong> <?php echo e($ciclo['usuario_fechamento_nome']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($ciclo['observacoes_abertura'])): ?>
                        <div class="mb-2"><strong>Obs. abertura:</strong> <?php echo e($ciclo['observacoes_abertura']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($ciclo['observacoes_fechamento'])): ?>
                        <div class="mb-2"><strong>Obs. fechamento:</strong> <?php echo e($ciclo['observacoes_fechamento']); ?></div>
                    <?php endif; ?>

                    <?php if ($podeOperar && $statusCiclo === 'aberto'): ?>
                        <hr>
                        <form method="POST" action="<?php echo url('inventarios/fechar'); ?>" onsubmit="return confirm('Fechar ciclo e aplicar ajustes de estoque agora?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="competencia" value="<?php echo e($competencia); ?>">
                            <div class="mb-2">
                                <label class="form-label">Observacoes de fechamento (opcional)</label>
                                <input type="text" name="observacoes_fechamento" class="form-control" placeholder="Ex.: Conferencia concluida pela equipe.">
                            </div>
                            <button class="btn btn-outline-danger w-100" type="submit">Fechar ciclo e aplicar ajustes</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="panel">
            <div class="panel__body">
                <h3 class="panel__titulo">Pendentes (top 10)</h3>
                <?php if (empty($pendentes)): ?>
                    <div class="empty-state">
                        <i class="bi bi-check2-all"></i>
                        Todos os produtos ja foram contados.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Saldo atual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendentes as $pendente): ?>
                                    <tr>
                                        <td><?php echo e($pendente['nome']); ?></td>
                                        <td><?php echo e(number_format((float)$pendente['estoque_atual'], 2, ',', '.')); ?> <?php echo e($pendente['unidade_medida']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
