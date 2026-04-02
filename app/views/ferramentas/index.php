<?php $titulo = 'Ferramentas'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Ferramentas</h2>
        <p class="page-header__subtitulo">Cadastro e monitoramento de disponibilidade das ferramentas do setor.</p>
    </div>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <div class="page-header__acoes">
            <a class="btn btn-primary" href="<?php echo url('ferramentas/criar'); ?>"><i class="bi bi-plus-circle me-1"></i>Nova Ferramenta</a>
        </div>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel__body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descricao</th>
                        <th>Status</th>
                        <th>Cadastrada por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ferramentas)): ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-wrench-adjustable-circle"></i>
                                    Nenhuma ferramenta cadastrada.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ferramentas as $f): ?>
                            <tr>
                                <td><strong><?php echo e($f['nome']); ?></strong></td>
                                <td><?php echo e($f['descricao']); ?></td>
                                <td>
                                    <?php if ($f['status'] === 'Disponivel' || $f['status'] === 'Disponível'): ?>
                                        <span class="status-pill status-pill--ok">Disponivel</span>
                                    <?php elseif ($f['status'] === 'Emprestada'): ?>
                                        <span class="status-pill status-pill--alerta">Emprestada</span>
                                    <?php else: ?>
                                        <span class="status-pill status-pill--erro"><?php echo e($f['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($f['usuario_cadastro_nome']); ?></td>
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
                <a class="page-link" href="<?php echo url('ferramentas') . '?pagina=' . $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
