<?php $titulo = 'Comprovante de Saida'; require __DIR__ . '/../layouts/header.php'; ?>

<section class="page-header">
    <div>
        <h2 class="page-header__titulo">Comprovante de Saida de Material</h2>
        <p class="page-header__subtitulo">Documento para auditoria interna da manutencao.</p>
    </div>
    <div class="page-header__acoes">
        <button class="btn btn-primary" type="button" onclick="window.print();">
            <i class="bi bi-printer me-1"></i>Imprimir
        </button>
        <a class="btn btn-outline-secondary" href="<?php echo url('saidas'); ?>">Voltar</a>
    </div>
</section>

<section class="panel comprovante-saida">
    <div class="panel__body">
        <div class="comprovante-cabecalho">
            <div>
                <strong>Grand Oca Maragogi Resort</strong><br>
                Sistema de Estoque da Manutencao
            </div>
            <div class="text-end">
                <strong>Comprovante #<?php echo e($saida['id']); ?></strong><br>
                Emitido em: <?php echo e(date('Y-m-d H:i:s')); ?>
            </div>
        </div>

        <hr>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Data da saida</label>
                <div class="form-control"><?php echo e($saida['data_saida']); ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Usuario emissor</label>
                <div class="form-control"><?php echo e($saida['usuario_nome']); ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Produto</label>
                <div class="form-control"><?php echo e($saida['produto_nome']); ?></div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Codigo interno</label>
                <div class="form-control"><?php echo e($saida['codigo_interno']); ?></div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantidade</label>
                <div class="form-control"><?php echo e($saida['quantidade']); ?> <?php echo e($saida['unidade_medida']); ?></div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Setor</label>
                <div class="form-control"><?php echo e($saida['setor']); ?></div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Local de utilizacao</label>
                <div class="form-control"><?php echo e($saida['local_utilizacao']); ?></div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Solicitante</label>
                <div class="form-control"><?php echo e($saida['tecnico_responsavel']); ?></div>
            </div>
            <div class="col-md-12">
                <label class="form-label">Observacoes</label>
                <div class="form-control comprovante-observacoes"><?php echo e($saida['observacoes']); ?></div>
            </div>
        </div>

        <div class="alert alert-primary mt-3 mb-0">
            Validacao de senha do usuario emissor: <strong>CONFIRMADA</strong>
        </div>

        <div class="comprovante-assinaturas">
            <div class="assinatura-box">
                <div class="assinatura-linha"></div>
                <small class="assinatura-titulo">Assinatura do solicitante</small>
                <div class="assinatura-campo"><strong>Nome:</strong> <?php echo e($saida['tecnico_responsavel']); ?></div>
                <div class="assinatura-campo"><strong>Assinatura:</strong> ____________________________________</div>
            </div>
            <div class="assinatura-box">
                <div class="assinatura-linha"></div>
                <small class="assinatura-titulo">Assinatura do usuario emissor</small>
                <div class="assinatura-campo"><strong>Nome:</strong> <?php echo e($saida['usuario_nome']); ?></div>
                <div class="assinatura-campo"><strong>Assinatura:</strong> ____________________________________</div>
            </div>
        </div>
    </div>
</section>

<?php if ($auto): ?>
<script>
window.addEventListener('load', function () {
    window.print();
});
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
