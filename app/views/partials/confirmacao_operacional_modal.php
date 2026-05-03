<?php
$modalId = $modalId ?? 'modal-confirmacao-operacional';
$acaoConfirmacao = $acaoConfirmacao ?? 'Confirmar operacao';
$usuarioConfirmacao = $_SESSION['usuario']['nome'] ?? 'Usuario logado';
$senhaJaConfirmada = !empty($_SESSION['senha_operacional_confirmada']);
?>

<?php if ($senhaJaConfirmada): ?>
    <button class="btn btn-primary" type="submit"><?php echo e($acaoConfirmacao); ?></button>
<?php else: ?>
    <button class="btn btn-primary" type="button" data-operational-confirm="#<?php echo e($modalId); ?>">
        <?php echo e($acaoConfirmacao); ?>
    </button>

    <div class="modal fade" id="<?php echo e($modalId); ?>" tabindex="-1" aria-labelledby="<?php echo e($modalId); ?>-titulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="<?php echo e($modalId); ?>-titulo">Confirmar transacao</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Usuario</label>
                        <div class="form-control bg-body-secondary"><?php echo e($usuarioConfirmacao); ?></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="<?php echo e($modalId); ?>-senha">Senha</label>
                        <input type="password" id="<?php echo e($modalId); ?>-senha" name="senha_confirmacao" class="form-control" autocomplete="current-password" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="<?php echo e($modalId); ?>-lembrar" name="lembrar_senha_operacional" value="1" class="form-check-input">
                        <label class="form-check-label" for="<?php echo e($modalId); ?>-lembrar">Nao exigir novamente ate eu sair do sistema</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><?php echo e($acaoConfirmacao); ?></button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
