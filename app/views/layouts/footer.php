<?php $usuario = $_SESSION['usuario'] ?? null; ?>
<?php if ($usuario): ?>
        </main>
        <footer class="app-footer">
            <div class="app-footer__titulo">Grand Oca Maragogi Resort - Sistema de Estoque da Manutenção</div>
            <div class="app-footer__autor">Desenvolvido por Gilson Matias</div>
            <div class="app-footer__autor"><a href="<?php echo url('lgpd/politica'); ?>">Politica de Privacidade (LGPD)</a></div>
        </footer>
    </div>
</div>
<?php else: ?>
</main>
<footer class="app-footer">
    <div class="app-footer__titulo">Grand Oca Maragogi Resort - Sistema de Estoque da Manutencao</div>
    <div class="app-footer__autor"><a href="<?php echo url('lgpd/politica'); ?>">Politica de Privacidade (LGPD)</a></div>
</footer>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo url('js/app.js'); ?>"></script>
</body>
</html>
