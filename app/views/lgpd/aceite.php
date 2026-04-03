<?php $titulo = 'Aceite de Privacidade'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Aceite obrigatorio de privacidade</h2>
        <p class="page-header__subtitulo">Antes de continuar, confirme a ciencia da politica de tratamento de dados.</p>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <h3 class="panel__titulo">Termo de ciencia LGPD</h3>
        <p>Ao acessar este sistema, voce confirma que leu e compreendeu as regras de privacidade aplicadas ao ambiente interno da manutencao.</p>
        <p><strong>Versao em vigor:</strong> <?php echo e($versaoPolitica); ?></p>
        <p><strong>Canal de contato:</strong> <?php echo e($emailEncarregado); ?></p>

        <div class="alert alert-primary">
            Leia a politica completa em
            <a href="<?php echo url('lgpd/politica'); ?>" target="_blank" rel="noopener">Politica de Privacidade (LGPD)</a>.
        </div>

        <form method="POST" action="<?php echo url('lgpd/aceite'); ?>" class="mt-3">
            <?php echo csrf_field(); ?>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="aceite_lgpd" name="aceite_lgpd" required>
                <label class="form-check-label" for="aceite_lgpd">
                    Declaro ciencia e aceite da Politica de Privacidade e Tratamento de Dados.
                </label>
            </div>
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-shield-check me-1"></i>Registrar aceite e continuar
            </button>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
