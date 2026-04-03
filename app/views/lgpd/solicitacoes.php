<?php $titulo = 'Solicitacoes LGPD'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Solicitacoes de titulares (LGPD)</h2>
        <p class="page-header__subtitulo">Registro, acompanhamento e resposta das demandas de titulares de dados.</p>
    </div>
</section>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?php echo e($flash['tipo']); ?>"><?php echo e($flash['mensagem']); ?></div>
<?php endif; ?>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel mb-3">
    <div class="panel__body">
        <h3 class="panel__titulo">Nova solicitacao</h3>
        <form method="POST" action="<?php echo url('lgpd/solicitacoes'); ?>" class="row g-3">
            <?php echo csrf_field(); ?>
            <div class="col-md-4">
                <label class="form-label" for="titular_nome">Nome do titular</label>
                <input class="form-control" id="titular_nome" name="titular_nome" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="titular_email">E-mail do titular</label>
                <input class="form-control" id="titular_email" name="titular_email" type="email" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="tipo_solicitacao">Tipo de solicitacao</label>
                <select class="form-select" id="tipo_solicitacao" name="tipo_solicitacao" required>
                    <option value="">Selecione</option>
                    <option value="acesso">Acesso</option>
                    <option value="correcao">Correcao</option>
                    <option value="anonimizacao">Anonimizacao</option>
                    <option value="eliminacao">Eliminacao</option>
                    <option value="portabilidade">Portabilidade</option>
                    <option value="oposicao">Oposicao</option>
                    <option value="revogacao">Revogacao de consentimento</option>
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label" for="descricao">Descricao da solicitacao</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-journal-check me-1"></i>Registrar solicitacao
                </button>
            </div>
        </form>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <h3 class="panel__titulo">Historico de solicitacoes</h3>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Protocolo</th>
                        <th>Titular</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Abertura</th>
                        <th>Resposta</th>
                        <th class="text-end">Acao</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($itens)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-shield-lock"></i>
                                    Nenhuma solicitacao LGPD registrada.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($itens as $item): ?>
                            <tr>
                                <td><strong><?php echo e($item['protocolo']); ?></strong></td>
                                <td>
                                    <?php echo e($item['titular_nome']); ?><br>
                                    <small><?php echo e($item['titular_email']); ?></small>
                                </td>
                                <td><?php echo e(ucfirst($item['tipo_solicitacao'])); ?></td>
                                <td>
                                    <?php if ($item['status'] === 'concluida'): ?>
                                        <span class="status-pill status-pill--ok">Concluida</span>
                                    <?php elseif ($item['status'] === 'indeferida'): ?>
                                        <span class="status-pill status-pill--erro">Indeferida</span>
                                    <?php elseif ($item['status'] === 'em_analise'): ?>
                                        <span class="status-pill status-pill--alerta">Em analise</span>
                                    <?php else: ?>
                                        <span class="status-pill status-pill--alerta">Aberta</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item['data_abertura']); ?></td>
                                <td>
                                    <?php echo e($item['resposta'] ?? '-'); ?><br>
                                    <?php if (!empty($item['responsavel_nome'])): ?>
                                        <small>Resp.: <?php echo e($item['responsavel_nome']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="<?php echo url('lgpd/solicitacoes/atualizar/' . $item['id']); ?>">
                                        <?php echo csrf_field(); ?>
                                        <div class="d-flex gap-2 justify-content-end">
                                            <select class="form-select form-select-sm" name="status" style="max-width: 140px;" required>
                                                <option value="aberta" <?php echo $item['status'] === 'aberta' ? 'selected' : ''; ?>>Aberta</option>
                                                <option value="em_analise" <?php echo $item['status'] === 'em_analise' ? 'selected' : ''; ?>>Em analise</option>
                                                <option value="concluida" <?php echo $item['status'] === 'concluida' ? 'selected' : ''; ?>>Concluida</option>
                                                <option value="indeferida" <?php echo $item['status'] === 'indeferida' ? 'selected' : ''; ?>>Indeferida</option>
                                            </select>
                                            <input type="text" class="form-control form-control-sm" name="resposta" placeholder="Resposta" value="<?php echo e($item['resposta'] ?? ''); ?>" style="max-width: 220px;">
                                            <button class="btn btn-sm btn-outline-primary" type="submit">Salvar</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
