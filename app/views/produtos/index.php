<?php $titulo = 'Produtos'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Produtos</h2>
        <p class="page-header__subtitulo">Cadastro e gestão dos materiais usados pela manutenção.</p>
    </div>
    <div class="page-header__acoes">
        <a class="btn btn-outline-success" href="<?php echo url('produtos/excel?busca=' . urlencode($busca)); ?>">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar Excel
        </a>
        <a class="btn btn-outline-secondary" href="<?php echo url('produtos/pdf?busca=' . urlencode($busca)); ?>" target="_blank">
            <i class="bi bi-filetype-pdf me-1"></i>Exportar PDF
        </a>
        <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
            <a class="btn btn-primary" href="<?php echo url('produtos/criar'); ?>">
                <i class="bi bi-plus-circle me-1"></i>Novo Produto
            </a>
        <?php endif; ?>
    </div>
</section>

<?php $flash = flash_get('produtos'); ?>
<?php if ($flash): ?>
    <div class="alert alert-<?php echo e($flash['tipo']); ?>"><?php echo e($flash['mensagem']); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form class="row g-2" method="GET" action="<?php echo url('produtos'); ?>">
            <div class="col-lg-8">
                <label class="form-label">Buscar produto</label>
                <input type="text" name="busca" class="form-control" placeholder="Digite nome, código interno ou localização" value="<?php echo e($busca); ?>">
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" type="submit">Buscar</button>
            </div>
            <div class="col-lg-2 d-flex align-items-end">
                <a class="btn btn-outline-primary w-100" href="<?php echo url('produtos'); ?>">Limpar</a>
            </div>
        </form>
    </div>
</section>

<section class="panel mt-3">
    <div class="panel__body">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Estoque</th>
                        <th>Mínimo</th>
                        <th>Unidade</th>
                        <th>Localização</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produtos)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-search"></i>
                                    Nenhum produto encontrado para o termo informado.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produtos as $p): ?>
                            <tr class="<?php echo ($p['estoque_atual'] <= $p['estoque_minimo']) ? 'table-warning' : ''; ?>">
                                <td>
                                    <strong><?php echo e($p['nome']); ?></strong>
                                </td>
                                <td><?php echo e($p['categoria_nome']); ?></td>
                                <td><?php echo e($p['estoque_atual']); ?></td>
                                <td><?php echo e($p['estoque_minimo']); ?></td>
                                <td><?php echo e($p['unidade_medida']); ?></td>
                                <td><?php echo e($p['localizacao']); ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="<?php echo url('produtos/ver/' . $p['id']); ?>">Detalhes</a>
                                    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
                                        <a class="btn btn-sm btn-outline-secondary" href="<?php echo url('produtos/editar/' . $p['id']); ?>">Editar</a>
                                    <?php endif; ?>
                                    <?php if ($tipoUsuario === 'Administrador'): ?>
                                        <form class="d-inline" method="POST" action="<?php echo url('produtos/excluir/' . $p['id']); ?>" onsubmit="return confirm('Excluir produto?');">
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

<nav class="data-paginacao">
    <ul class="pagination flex-wrap">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo url('produtos') . '?pagina=' . $i . '&busca=' . urlencode($busca); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
