<?php $titulo = 'Política de Privacidade (LGPD)'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Política de Privacidade e Tratamento de Dados</h2>
        <p class="page-header__subtitulo">Diretrizes internas de uso de dados pessoais no sistema de estoque da manutenção.</p>
    </div>
    <div class="page-header__acoes">
        <?php if (!empty($_SESSION['usuario'])): ?>
            <a class="btn btn-outline-secondary" href="<?php echo url('dashboard'); ?>">Voltar ao painel</a>
        <?php else: ?>
            <a class="btn btn-outline-secondary" href="<?php echo url('login'); ?>">Voltar ao login</a>
        <?php endif; ?>
    </div>
</section>

<section class="panel">
    <div class="panel__body">
        <h3 class="panel__titulo">Resumo de conformidade LGPD</h3>
        <p><strong>Versão da política:</strong> <?php echo e($versaoPolitica); ?></p>
        <p><strong>Canal de privacidade:</strong> <?php echo e($emailEncarregado); ?></p>
        <p>Este sistema trata dados pessoais exclusivamente para operação interna, auditoria, segurança e rastreabilidade dos processos da manutenção.</p>

        <hr>

        <h4>Base legal e finalidade</h4>
        <ul>
            <li>Controle de acesso e autenticação de usuários.</li>
            <li>Registro de auditoria para prevenção de fraude e rastreabilidade.</li>
            <li>Gestão operacional do estoque, entradas, saídas e inventários.</li>
            <li>Cumprimento de obrigações legais e regulatórias aplicáveis.</li>
        </ul>

        <h4>Dados tratados</h4>
        <ul>
            <li>Identificação do colaborador: nome, e-mail, perfil e status.</li>
            <li>Dados de autenticação: hash de senha (senha em texto nunca é armazenada).</li>
            <li>Registros de segurança: data/hora, IP, user-agent e trilha de ações.</li>
            <li>Dados operacionais vinculados a movimentações de estoque.</li>
        </ul>

        <h4>Direitos do titular</h4>
        <p>Solicitações de acesso, correção, anonimização, eliminação, portabilidade, oposição e revogação podem ser registradas no canal de privacidade.</p>

        <h4>Retenção e minimização</h4>
        <p>Os registros são mantidos pelo período necessário para auditoria e obrigações internas, com anonimização e exclusão conforme configuração de retenção.</p>

        <h4>Segurança</h4>
        <ul>
            <li>Senha protegida com hash criptográfico.</li>
            <li>Sessão segura com expiração automática.</li>
            <li>Proteções contra CSRF, XSS e SQL Injection.</li>
            <li>Controle de acesso por perfil e trilha de auditoria.</li>
        </ul>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
