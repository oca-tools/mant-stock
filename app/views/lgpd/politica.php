<?php $titulo = 'Politica de Privacidade (LGPD)'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Politica de Privacidade e Tratamento de Dados</h2>
        <p class="page-header__subtitulo">Diretrizes internas de uso de dados pessoais no sistema de estoque da manutencao.</p>
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
        <p><strong>Versao da politica:</strong> <?php echo e($versaoPolitica); ?></p>
        <p><strong>Canal de privacidade:</strong> <?php echo e($emailEncarregado); ?></p>
        <p>Este sistema trata dados pessoais exclusivamente para operacao interna, auditoria, seguranca e rastreabilidade dos processos da manutencao.</p>

        <hr>

        <h4>Base legal e finalidade</h4>
        <ul>
            <li>Controle de acesso e autenticacao de usuarios.</li>
            <li>Registro de auditoria para prevencao de fraude e rastreabilidade.</li>
            <li>Gestao operacional do estoque, entradas, saidas e inventarios.</li>
            <li>Cumprimento de obrigacoes legais e reguladoras aplicaveis.</li>
        </ul>

        <h4>Dados tratados</h4>
        <ul>
            <li>Identificacao do colaborador: nome, e-mail, perfil e status.</li>
            <li>Dados de autenticacao: hash de senha (senha em texto nunca e armazenada).</li>
            <li>Registros de seguranca: data/hora, IP, user-agent e trilha de acoes.</li>
            <li>Dados operacionais vinculados a movimentacoes de estoque.</li>
        </ul>

        <h4>Direitos do titular</h4>
        <p>Solicitacoes de acesso, correcao, anonimizacao, eliminacao, portabilidade, oposicao e revogacao podem ser registradas no canal de privacidade.</p>

        <h4>Retencao e minimizacao</h4>
        <p>Os registros sao mantidos pelo periodo necessario para auditoria e obrigacoes internas, com anonimizacao e exclusao conforme configuracao de retencao.</p>

        <h4>Seguranca</h4>
        <ul>
            <li>Senha protegida com hash criptografico.</li>
            <li>Sessao segura com expiraçao automatica.</li>
            <li>Protecoes contra CSRF, XSS e SQL Injection.</li>
            <li>Controle de acesso por perfil e trilha de auditoria.</li>
        </ul>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
