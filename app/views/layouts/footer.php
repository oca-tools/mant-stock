<?php $usuario = $_SESSION['usuario'] ?? null; ?>
<?php if ($usuario): ?>
        </main>
        <footer class="app-footer">
            <div class="container">
                <div>Grand Oca Maragogi Resort - Sistema de Estoque da Manutenção</div>
                <div class="text-muted">Desenvolvido por Gilson Matias</div>
            </div>
        </footer>
    </div>
</div>
<?php else: ?>
</div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo url('js/app.js'); ?>"></script>
</body>
</html>







