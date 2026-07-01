</main> 
<footer class="bg-white py-4 border-top mt-auto">
    <div class="container text-center">
        <span class="text-muted small">
            &copy; <?= date('Y') ?> Sistem Pakar Diagnosis Penyakit Ikan Nila (Forward Chaining)<br>
            Studi Kasus: Bojong Gede, Jawa Barat<br>
            Skripsi Teknik Informatika | Naufal Rafif (202243501684)
        </span>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            title: 'Berhasil!',
            html: '<?= $_SESSION['success'] ?>',
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            title: 'Gagal!',
            html: '<?= $_SESSION['error'] ?>',
            icon: 'error',
            confirmButtonColor: '#002d27'
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
});
</script>

</body>
</html>