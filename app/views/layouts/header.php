<?php $usuario = $_SESSION['usuario'] ?? null; ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(($titulo ?? 'Sistema de Estoque')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo url('css/app.css'); ?>" rel="stylesheet">
</head>
<body>
<?php if ($usuario): ?>
<?php $tipoUsuario = $usuario['tipo_usuario'] ?? ''; ?>
<?php $podeOperar = in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true); ?>
<?php $isAdmin = ($tipoUsuario === 'Administrador'); ?>
<div class="app-shell">
    <aside class="app-sidebar">
        <div class="brand-mini">
            <img src="<?php echo url('img/logo-grand-oca.png'); ?>" alt="Grand Oca" class="brand-logo">
            <div class="brand-mini-text">OCA MantStock</div>
            <div class="brand-version">Versão 1.0</div>
        </div>
        <nav class="sidebar-nav">
            <a class="sidebar-link" href="<?php echo url('dashboard'); ?>"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a class="sidebar-link" href="<?php echo url('estoque'); ?>">
                <i class="bi bi-box-seam"></i> Estoque Atual
                <?php $qtdBaixo = estoque_baixo_count(); ?>
                <?php if ($qtdBaixo > 0): ?>
                    <span class="badge badge-estoque ms-auto"><?php echo e($qtdBaixo); ?></span>
                <?php endif; ?>
            </a>
            <button class="sidebar-group" data-bs-toggle="collapse" data-bs-target="#menu-cadastros">
                <span><i class="bi bi-folder"></i> Cadastros</span>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div id="menu-cadastros" class="collapse show">
                <a class="sidebar-sublink" href="<?php echo url('produtos'); ?>">Produtos</a>
                <a class="sidebar-sublink" href="<?php echo url('categorias'); ?>">Categorias</a>
                <a class="sidebar-sublink" href="<?php echo url('ferramentas'); ?>">Ferramentas</a>
                <?php if ($isAdmin): ?>
                    <a class="sidebar-sublink" href="<?php echo url('usuarios'); ?>">Usuários</a>
                <?php endif; ?>
                <?php if (!$isAdmin): ?>
                    <span class="sidebar-note">Edição restrita</span>
                <?php endif; ?>
            </div>

            <button class="sidebar-group" data-bs-toggle="collapse" data-bs-target="#menu-movimentacoes">
                <span><i class="bi bi-arrow-left-right"></i> Movimentações</span>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div id="menu-movimentacoes" class="collapse show">
                <a class="sidebar-sublink" href="<?php echo url('entradas'); ?>">Entradas</a>
                <a class="sidebar-sublink" href="<?php echo url('saidas'); ?>">Saídas</a>
                <a class="sidebar-sublink" href="<?php echo url('descartes'); ?>">Descartes</a>
                <a class="sidebar-sublink" href="<?php echo url('movimentacoes'); ?>">Histórico</a>
                <?php if (!$podeOperar): ?>
                    <span class="sidebar-note">Somente leitura</span>
                <?php endif; ?>
            </div>

            <button class="sidebar-group" data-bs-toggle="collapse" data-bs-target="#menu-operacoes">
                <span><i class="bi bi-tools"></i> Operações</span>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div id="menu-operacoes" class="collapse show">
                <a class="sidebar-sublink" href="<?php echo url('emprestimos'); ?>">Empréstimos</a>
                <a class="sidebar-sublink" href="<?php echo url('inventarios'); ?>">Inventários</a>
                <?php if (!$podeOperar): ?>
                    <span class="sidebar-note">Somente leitura</span>
                <?php endif; ?>
            </div>

            <a class="sidebar-link" href="<?php echo url('relatorios'); ?>"><i class="bi bi-graph-up"></i> Relatórios</a>
        </nav>
    </aside>
    <div class="app-main">
        <header class="topbar">
            <button class="btn btn-outline-primary btn-sm sidebar-toggle" type="button" id="toggle-sidebar">
                <i class="bi bi-list"></i>
            </button>
            <form class="d-flex" method="GET" action="<?php echo url('busca'); ?>">
                <input class="form-control form-control-sm me-2" type="search" name="q" placeholder="Busca global" aria-label="Busca">
                <button class="btn btn-outline-primary btn-sm" type="submit">Buscar</button>
            </form>
            <div class="theme-toggle">
                <button class="btn btn-outline-primary btn-sm" type="button" id="toggle-theme">
                    <i class="bi bi-moon-stars"></i>
                    Tema
                </button>
            </div>
            <div class="user-block">
                <div>Olá, <?php echo e($usuario['nome']); ?></div>
                <div class="user-clock" id="relogio-topo"></div>
            </div>
            <form method="POST" action="<?php echo url('logout'); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-outline-primary btn-sm" type="submit">Sair</button>
            </form>
        </header>
        <main class="content">
<?php endif; ?>
<?php if (!$usuario): ?>
<div class="container my-4">
<?php endif; ?>







