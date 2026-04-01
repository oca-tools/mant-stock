<?php $titulo = 'Produtos'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $tipoUsuario = $_SESSION['usuario']['tipo_usuario'] ?? ''; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Produtos</h3>
    <?php if (in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true)): ?>
        <a class="btn btn-primary" href="<?php echo url('produtos/criar'); ?>">Novo Produto</a>
    <?php endif; ?>
</div>
<?php $flash = flash_get('produtos'); ?>
<?php if ($flash): ?>
    <div class="alert alert-<?php echo e($flash['tipo']); ?>"><?php echo e($flash['mensagem']); ?></div>
<?php endif; ?>
<form class="row g-2 mb-3" method="GET" action="<?php echo url('produtos'); ?>">
    <div class="col-md-4">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou Código" value="<?php echo e($busca); ?>">
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary">Buscar</button>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Estoque</th>
                <th>Mínimo</th>
                <th>Unidade</th>
                <th>Localização</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $p): ?>
                <tr class="<?php echo ($p['estoque_atual'] <= $p['estoque_minimo']) ? 'table-warning' : ''; ?>">
                    <td><?php echo e($p['nome']); ?></td>
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
                                <button class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i === $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo url('produtos') . '?pagina=' . $i . '&busca=' . urlencode($busca); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php require __DIR__ . '/../layouts/footer.php'; ?>







