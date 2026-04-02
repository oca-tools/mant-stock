<?php $usuario = $_SESSION['usuario'] ?? null; ?>
<?php if ($usuario): ?>
        </main>
        <footer class="app-footer">
            <div class="app-footer__titulo">Grand Oca Maragogi Resort - Sistema de Estoque da Manutenção</div>
            <div class="app-footer__autor">Desenvolvido por Gilson Matias</div>
        </footer>
    </div>
</div>
<?php else: ?>
</main>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo url('js/app.js'); ?>"></script>
</body>
</html>
