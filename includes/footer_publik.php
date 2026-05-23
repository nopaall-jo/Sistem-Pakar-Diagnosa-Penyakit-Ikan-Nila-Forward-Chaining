</main> <!-- Penutup Konten Utama -->

<!-- Footer Publik -->
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Script Notifikasi Toastr -->
<script>
$(document).ready(function() {
    // Setting posisi Toastr biar muncul di tengah atas untuk peternak
    toastr.options = {
        "positionClass": "toast-top-center",
        "timeOut": "3000"
    };

    <?php if (isset($_SESSION['success'])): ?>
        toastr.success('<?= $_SESSION['success'] ?>');
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        toastr.error('<?= $_SESSION['error'] ?>');
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
});
</script>

</body>
</html>