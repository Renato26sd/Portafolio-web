<?php if (Session::isLoggedIn()): ?>
</div>
<?php endif; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="/public/js/main.js"></script>

<?php if (Session::hasFlash('success')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showAlert('success', '<?= Session::getFlash('success') ?>');
    });
</script>
<?php endif; ?>

<?php if (Session::hasFlash('error')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showAlert('error', '<?= Session::getFlash('error') ?>');
    });
</script>
<?php endif; ?>

<?php if (Session::hasFlash('warning')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showAlert('warning', '<?= Session::getFlash('warning') ?>');
    });
</script>
<?php endif; ?>

<?php if (Session::hasFlash('info')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showAlert('info', '<?= Session::getFlash('info') ?>');
    });
</script>
<?php endif; ?>

</body>
</html>
