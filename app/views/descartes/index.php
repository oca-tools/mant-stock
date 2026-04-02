<?php $titulo = 'Descartes'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Descartes</h2>
        <p class="page-header__subtitulo">Rastreio de materiais descartados para apoio à reposição de estoque.</p>
    </div>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <div class="page-header__acoes">
            <a class="btn btn-primary" href="<?php echo url('descartes/criar'); ?>"><i class="bi bi-plus-circle me-1"></i>Novo Descarte</a>
        </div>
    <?php endif; ?>
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
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($descartes)): ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-recycle"></i>
                                    Nenhum descarte registrado até o momento.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($descartes as $d): ?>
                            <tr>
                                <td><?php echo e($d['data_descarte']); ?></td>
                                <td><?php echo e($d['produto_nome']); ?></td>
                                <td><?php echo e($d['quantidade']); ?></td>
                                <td><?php echo e($d['motivo_descarte']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
