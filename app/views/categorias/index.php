<?php $titulo = 'Categorias'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Categorias</h2>
        <p class="page-header__subtitulo">Classificação dos materiais para relatórios e controle operacional.</p>
    </div>
    <?php if ($tipoUsuario === 'Administrador'): ?>
        <div class="page-header__acoes">
            <a class="btn btn-primary" href="<?php echo url('categorias/criar'); ?>"><i class="bi bi-plus-circle me-1"></i>Nova Categoria</a>
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
                        <th>Descrição</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categorias)): ?>
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <i class="bi bi-tags"></i>
                                    Nenhuma categoria cadastrada.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categorias as $c): ?>
                            <tr>
                                <td><strong><?php echo e($c['nome']); ?></strong></td>
                                <td><?php echo e($c['descricao']); ?></td>
                                <td class="text-end">
                                    <?php if ($tipoUsuario === 'Administrador'): ?>
                                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo url('categorias/editar/' . $c['id']); ?>">Editar</a>
                                        <form class="d-inline" method="POST" action="<?php echo url('categorias/excluir/' . $c['id']); ?>" onsubmit="return confirm('Excluir categoria?');">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>
