<?php $titulo = 'Enviar Convite de Cadastro'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Enviar Convite de Cadastro</h2>
        <p class="page-header__subtitulo">Dispare um e-mail para o colaborador criar a propria conta com perfil definido.</p>
    </div>
    <div class="page-header__acoes">
        <a class="btn btn-outline-secondary" href="<?php echo url('usuarios'); ?>">Voltar</a>
    </div>
</section>

<?php if (!empty($erro)): ?>
    <div class="alert alert-danger"><?php echo e($erro); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel__body">
        <form method="POST" action="<?php echo url('usuarios/convites'); ?>">
            <?php echo csrf_field(); ?>
            <div class="row g-3">
                <div class="col-lg-5">
                    <label class="form-label">E-mail do colaborador</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">Nome sugerido (opcional)</label>
                    <input type="text" name="nome_sugerido" class="form-control" placeholder="Ex.: João da Silva">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Perfil de acesso</label>
                    <select name="tipo_usuario" class="form-select" required>
                        <option value="">Selecione</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Almoxarifado">Almoxarifado</option>
                        <option value="Consulta">Consulta</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Validade (horas)</label>
                    <input type="number" name="validade_horas" class="form-control" min="1" max="720" value="72" required>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" type="submit">Enviar convite</button>
                <a class="btn btn-outline-secondary" href="<?php echo url('usuarios'); ?>">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
