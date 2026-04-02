<?php $usuario = $_SESSION['usuario'] ?? null; ?>
<?php $rotaAtual = rota_atual(); ?>
<?php
// Versao para evitar cache antigo do favicon no navegador
$caminhoLogoFavicon = __DIR__ . '/../../../public/img/logo-grand-oca.png';
$versaoFavicon = file_exists($caminhoLogoFavicon) ? (string)filemtime($caminhoLogoFavicon) : (string)time();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(($titulo ?? 'Sistema de Estoque')); ?></title>
    <link rel="icon" type="image/svg+xml" href="<?php echo url('favicon.svg?v=' . $versaoFavicon); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo url('favicon.png?v=' . $versaoFavicon); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo url('favicon.png?v=' . $versaoFavicon); ?>">
    <link rel="apple-touch-icon" href="<?php echo url('favicon.png?v=' . $versaoFavicon); ?>">
    <meta name="theme-color" content="#101826">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo url('css/app.css'); ?>" rel="stylesheet">
</head>
<body data-rota="<?php echo e($rotaAtual); ?>">
<?php if ($usuario): ?>
<?php $tipoUsuario = $usuario['tipo_usuario'] ?? ''; ?>
<?php $podeOperar = in_array($tipoUsuario, ['Administrador', 'Almoxarifado'], true); ?>
<?php $isAdmin = ($tipoUsuario === 'Administrador'); ?>
<?php $cadastrosAberto = rota_comeca_com(['/produtos', '/categorias', '/ferramentas', '/usuarios']); ?>
<?php $movimentacoesAberto = rota_comeca_com(['/entradas', '/saidas', '/descartes', '/movimentacoes']); ?>
<?php $operacoesAberto = rota_comeca_com(['/emprestimos', '/inventarios']); ?>
<div class="app-shell" id="app-shell">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <aside class="app-sidebar" id="app-sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo-wrap">
                <img src="<?php echo url('img/logo-grand-oca.png'); ?>" alt="Grand Oca Maragogi Resort" class="brand-logo" loading="lazy">
            </div>
            <div class="brand-meta">
                <div class="brand-title">OCA MantStock</div>
                <div class="brand-subtitle">Grand Oca Resort</div>
                <div class="brand-version">Versão 1.0</div>
            </div>
            <button class="btn-close-sidebar d-lg-none" type="button" id="close-sidebar" aria-label="Fechar menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a class="sidebar-link <?php echo rota_ativa('/dashboard') ? 'is-active' : ''; ?>" href="<?php echo url('dashboard'); ?>">
                <i class="bi bi-grid-1x2"></i>
                <span>Dashboard</span>
            </a>

            <a class="sidebar-link <?php echo rota_ativa('/estoque') ? 'is-active' : ''; ?>" href="<?php echo url('estoque'); ?>">
                <i class="bi bi-box-seam"></i>
                <span>Estoque Atual</span>
                <?php $qtdBaixo = estoque_baixo_count(); ?>
                <?php if ($qtdBaixo > 0): ?>
                    <span class="badge rounded-pill badge-estoque ms-auto"><?php echo e($qtdBaixo); ?></span>
                <?php endif; ?>
            </a>

            <button class="sidebar-group<?php echo $cadastrosAberto ? '' : ' collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-cadastros" aria-expanded="<?php echo $cadastrosAberto ? 'true' : 'false'; ?>">
                <span><i class="bi bi-folder2-open"></i> Cadastros</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </button>
            <div id="menu-cadastros" class="collapse <?php echo $cadastrosAberto ? 'show' : ''; ?>" data-menu-chave="cadastros">
                <a class="sidebar-sublink <?php echo rota_comeca_com('/produtos') ? 'is-active' : ''; ?>" href="<?php echo url('produtos'); ?>">Produtos</a>
                <a class="sidebar-sublink <?php echo rota_comeca_com('/categorias') ? 'is-active' : ''; ?>" href="<?php echo url('categorias'); ?>">Categorias</a>
                <a class="sidebar-sublink <?php echo rota_comeca_com('/ferramentas') ? 'is-active' : ''; ?>" href="<?php echo url('ferramentas'); ?>">Ferramentas</a>
                <?php if ($isAdmin): ?>
                    <a class="sidebar-sublink <?php echo rota_comeca_com('/usuarios') ? 'is-active' : ''; ?>" href="<?php echo url('usuarios'); ?>">Usuários</a>
                <?php endif; ?>
            </div>

            <button class="sidebar-group<?php echo $movimentacoesAberto ? '' : ' collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-movimentacoes" aria-expanded="<?php echo $movimentacoesAberto ? 'true' : 'false'; ?>">
                <span><i class="bi bi-arrow-left-right"></i> Movimentações</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </button>
            <div id="menu-movimentacoes" class="collapse <?php echo $movimentacoesAberto ? 'show' : ''; ?>" data-menu-chave="movimentacoes">
                <a class="sidebar-sublink <?php echo rota_comeca_com('/entradas') ? 'is-active' : ''; ?>" href="<?php echo url('entradas'); ?>">Entradas</a>
                <a class="sidebar-sublink <?php echo rota_comeca_com('/saidas') ? 'is-active' : ''; ?>" href="<?php echo url('saidas'); ?>">Saídas</a>
                <a class="sidebar-sublink <?php echo rota_comeca_com('/descartes') ? 'is-active' : ''; ?>" href="<?php echo url('descartes'); ?>">Descartes</a>
                <a class="sidebar-sublink <?php echo rota_comeca_com('/movimentacoes') ? 'is-active' : ''; ?>" href="<?php echo url('movimentacoes'); ?>">Histórico</a>
                <?php if (!$podeOperar): ?>
                    <span class="sidebar-note">Perfil em modo consulta</span>
                <?php endif; ?>
            </div>

            <button class="sidebar-group<?php echo $operacoesAberto ? '' : ' collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#menu-operacoes" aria-expanded="<?php echo $operacoesAberto ? 'true' : 'false'; ?>">
                <span><i class="bi bi-tools"></i> Operações</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </button>
            <div id="menu-operacoes" class="collapse <?php echo $operacoesAberto ? 'show' : ''; ?>" data-menu-chave="operacoes">
                <a class="sidebar-sublink <?php echo rota_comeca_com('/emprestimos') ? 'is-active' : ''; ?>" href="<?php echo url('emprestimos'); ?>">Empréstimos</a>
                <a class="sidebar-sublink <?php echo rota_comeca_com('/inventarios') ? 'is-active' : ''; ?>" href="<?php echo url('inventarios'); ?>">Inventários</a>
            </div>

            <a class="sidebar-link <?php echo rota_comeca_com('/relatorios') ? 'is-active' : ''; ?>" href="<?php echo url('relatorios'); ?>">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Relatórios</span>
            </a>
        </nav>
    </aside>

    <div class="app-main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="btn btn-icon sidebar-toggle" type="button" id="toggle-sidebar" aria-label="Abrir menu">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-title">
                    <p class="topbar-kicker">Operação da Manutenção</p>
                    <h1><?php echo e(($titulo ?? 'Painel')); ?></h1>
                </div>
            </div>

            <div class="topbar-right">
                <form class="topbar-search" method="GET" action="<?php echo url('busca'); ?>">
                    <i class="bi bi-search"></i>
                    <input type="search" name="q" placeholder="Buscar produtos, códigos e movimentações..." aria-label="Busca global">
                    <button type="submit" class="btn btn-link">Buscar</button>
                </form>

                <button class="btn btn-icon" type="button" id="toggle-theme" aria-label="Alternar tema">
                    <i class="bi bi-moon-stars"></i>
                </button>

                <div class="user-chip">
                    <div class="user-chip__name"><?php echo e($usuario['nome']); ?></div>
                    <div class="user-chip__meta">
                        <span><?php echo e($usuario['tipo_usuario']); ?></span>
                        <span id="relogio-topo"></span>
                    </div>
                </div>

                <form method="POST" action="<?php echo url('logout'); ?>" class="logout-form">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn-outline-danger btn-sm" type="submit">
                        <i class="bi bi-box-arrow-right me-1"></i>Sair
                    </button>
                </form>
            </div>
        </header>
        <main class="content">
<?php endif; ?>

<?php if (!$usuario): ?>
<main class="guest-main">
<?php endif; ?>
