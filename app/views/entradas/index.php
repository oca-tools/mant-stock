<?php $titulo = 'Entradas'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Entradas de Estoque</h2>
        <p class="page-header__subtitulo">Registro de reposicoes recebidas no almoxarifado.</p>
    </div>
    <div class="page-header__acoes">
        <a class="btn btn-outline-success" href="<?php echo url('entradas/excel'); ?>">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar Excel
        </a>
        <a class="btn btn-outline-secondary" href="<?php echo url('entradas/pdf'); ?>" target="_blank">
            <i class="bi bi-filetype-pdf me-1"></i>Exportar PDF
        </a>
        <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
            <a class="btn btn-primary" href="<?php echo url('entradas/criar'); ?>"><i class="bi bi-plus-circle me-1"></i>Nova Entrada</a>
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
                        <th>Fornecedor</th>
                        <th>Usuario Emissor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entradas)): ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-box-arrow-in-down"></i>
                                    Nenhuma entrada registrada ate o momento.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($entradas as $e): ?>
                            <tr>
                                <td><?php echo e($e['data_entrada']); ?></td>
                                <td><?php echo e($e['produto_nome']); ?></td>
                                <td><?php echo e($e['quantidade']); ?></td>
                                <td><?php echo e($e['fornecedor']); ?></td>
                                <td><?php echo e($e['usuario_nome']); ?></td>
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
                <a class="page-link" href="<?php echo url('entradas') . '?pagina=' . $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
