<?php $titulo = 'Saidas'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Saidas de Estoque</h2>
        <p class="page-header__subtitulo">Controle dos materiais destinados as ordens de servico.</p>
    </div>
    <div class="page-header__acoes">
        <a class="btn btn-outline-success" href="<?php echo url('saidas/excel'); ?>">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar Excel
        </a>
        <a class="btn btn-outline-secondary" href="<?php echo url('saidas/pdf'); ?>" target="_blank">
            <i class="bi bi-filetype-pdf me-1"></i>Exportar PDF
        </a>
        <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
            <a class="btn btn-primary" href="<?php echo url('saidas/criar'); ?>"><i class="bi bi-plus-circle me-1"></i>Nova Saida</a>
        <?php endif; ?>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Local</th>
                        <th>Solicitante</th>
                        <th>Usuario Emissor</th>
                        <th class="text-end">Comprovante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($saidas)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                    Nenhuma saida registrada ate o momento.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($saidas as $s): ?>
                            <tr>
                                <td><?php echo e($s['data_saida']); ?></td>
                                <td><?php echo e($s['produto_nome']); ?></td>
                                <td><?php echo e($s['quantidade']); ?></td>
                                <td><?php echo e($s['local_utilizacao']); ?></td>
                                <td><?php echo e($s['tecnico_responsavel']); ?></td>
                                <td><?php echo e($s['usuario_nome']); ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="<?php echo url('saidas/comprovante/' . $s['id']); ?>" target="_blank">Imprimir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<nav class="data-paginacao">
    <ul class="pagination flex-wrap">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo url('saidas') . '?pagina=' . $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
