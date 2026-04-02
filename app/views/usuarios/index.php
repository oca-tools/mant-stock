<?php $titulo = 'Usuários'; require __DIR__ . '/../layouts/header.php'; ?>
<?php $flash = flash_get('usuarios'); ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Usuários</h2>
        <p class="page-header__subtitulo">Controle de acesso e cadastro por convite com rastreabilidade de quem convidou.</p>
    </div>
    <div class="page-header__acoes">
        <a class="btn btn-outline-primary" href="<?php echo url('usuarios/convites/novo'); ?>">
            <i class="bi bi-envelope-plus me-1"></i>Enviar Convite
        </a>
        <a class="btn btn-primary" href="<?php echo url('usuarios/criar'); ?>">
            <i class="bi bi-person-plus me-1"></i>Novo Usuário
        </a>
    </div>
</section>

<?php if ($flash): ?>
    <div class="alert alert-<?php echo e($flash['tipo']); ?>"><?php echo e($flash['mensagem']); ?></div>
<?php endif; ?>

<section class="panel mb-3">
    <div class="panel__body">
        <h3 class="panel__titulo">Contas cadastradas</h3>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    Nenhum usuário cadastrado.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><strong><?php echo e($usuario['nome']); ?></strong></td>
                                <td><?php echo e($usuario['email']); ?></td>
                                <td><?php echo e($usuario['tipo_usuario']); ?></td>
                                <td>
                                    <?php if ($usuario['ativo']): ?>
                                        <span class="status-pill status-pill--ok">Ativo</span>
                                    <?php else: ?>
                                        <span class="status-pill status-pill--erro">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($usuario['created_at']); ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="<?php echo url('usuarios/editar/' . $usuario['id']); ?>">Editar</a>
                                    <?php if ($usuario['ativo']): ?>
                                        <form class="d-inline" method="POST" action="<?php echo url('usuarios/desativar/' . $usuario['id']); ?>" onsubmit="return confirm('Desativar usuário?');">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Desativar</button>
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

<section class="panel">
    <div class="panel__body">
        <h3 class="panel__titulo">Últimos convites enviados</h3>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>E-mail</th>
                        <th>Nome sugerido</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th>Convidado por</th>
                        <th>Criação</th>
                        <th>Expira em</th>
                        <th>Usado em</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($convites)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-envelope"></i>
                                    Nenhum convite enviado até o momento.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($convites as $convite): ?>
                            <?php
                                $statusConvite = $convite['status'];
                                if ($statusConvite === 'pendente' && !empty($convite['expira_em']) && strtotime($convite['expira_em']) < time()) {
                                    $statusConvite = 'expirado';
                                }
                            ?>
                            <tr>
                                <td><?php echo e($convite['email']); ?></td>
                                <td><?php echo e($convite['nome_sugerido']); ?></td>
                                <td><?php echo e($convite['tipo_usuario']); ?></td>
                                <td>
                                    <?php if ($statusConvite === 'aceito'): ?>
                                        <span class="status-pill status-pill--ok">Aceito</span>
                                    <?php elseif ($statusConvite === 'expirado'): ?>
                                        <span class="status-pill status-pill--erro">Expirado</span>
                                    <?php else: ?>
                                        <span class="status-pill status-pill--alerta">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($convite['convidado_por_nome']); ?></td>
                                <td><?php echo e($convite['criado_em']); ?></td>
                                <td><?php echo e($convite['expira_em']); ?></td>
                                <td><?php echo e($convite['usado_em']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
