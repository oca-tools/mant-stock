<?php $titulo = 'Emprestimos'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<?php $flash = flash_get('emprestimos'); ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Emprestimos de Ferramentas</h2>
        <p class="page-header__subtitulo">Controle de retirada, devolucao e autoria de cada operacao.</p>
    </div>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <div class="page-header__acoes">
            <a class="btn btn-primary" href="<?php echo url('emprestimos/criar'); ?>"><i class="bi bi-plus-circle me-1"></i>Novo Emprestimo</a>
        </div>
    <?php endif; ?>
</section>

<?php if ($flash): ?>
    <div class="alert alert-<?php echo e($flash['tipo']); ?>"><?php echo e($flash['mensagem']); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Ferramenta</th>
                        <th>Responsavel retirada</th>
                        <th>Data retirada</th>
                        <th>Status</th>
                        <th>Emitido por</th>
                        <th>Devolvido por</th>
                        <th class="text-end">Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($emprestimos)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-tools"></i>
                                    Nenhum emprestimo registrado ate o momento.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($emprestimos as $e): ?>
                            <tr>
                                <td><?php echo e($e['ferramenta_nome']); ?></td>
                                <td><?php echo e($e['usuario_responsavel']); ?></td>
                                <td><?php echo e($e['data_retirada']); ?></td>
                                <td>
                                    <?php if ($e['status'] === 'Emprestada'): ?>
                                        <span class="status-pill status-pill--alerta">Emprestada</span>
                                    <?php elseif ($e['status'] === 'Devolvida'): ?>
                                        <span class="status-pill status-pill--ok">Devolvida</span>
                                    <?php else: ?>
                                        <span class="status-pill status-pill--erro"><?php echo e($e['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($e['usuario_executor_nome']); ?></td>
                                <td><?php echo e($e['usuario_devolucao_nome']); ?></td>
                                <td class="text-end">
                                    <?php if ($e['status'] === 'Emprestada'): ?>
                                        <form method="POST" action="<?php echo url('emprestimos/devolver/' . $e['id']); ?>" class="d-inline-flex gap-2 align-items-center">
                                            <?php echo csrf_field(); ?>
                                            <input type="password" name="senha_confirmacao" class="form-control form-control-sm" style="width: 180px;" placeholder="Senha emissor" required>
                                            <button class="btn btn-sm btn-outline-success" type="submit">Registrar devolucao</button>
                                        </form>
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

<nav class="data-paginacao">
    <ul class="pagination flex-wrap">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo url('emprestimos') . '?pagina=' . $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
