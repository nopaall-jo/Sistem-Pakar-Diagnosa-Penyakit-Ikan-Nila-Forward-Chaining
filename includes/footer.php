</div> 
<footer class="bg-white py-3 mt-4 border-top">
    <div class="container-fluid text-center">
    
    </div>
</footer>
            
        </div> 
    </div> 

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- TAMBAHAN BARU: Library Chart.js untuk Grafik Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="<?= $base_url ?>assets/js/script.js"></script>
    
    <script>
    // 1. SweetAlert Konfirmasi Hapus (Sudah Di-Upgrade jadi Dinamis)
    function confirmDelete(id, url = 'delete_riwayat.php') {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0dbb94', /* Warna Emerald */
            cancelButtonColor: '#e74a3b',  /* Warna Merah Danger */
            confirmButtonText: '<i class="bi bi-check-lg"></i> Ya, hapus!',
            cancelButtonText: '<i class="bi bi-x-lg"></i> Batal',
            reverseButtons: true /* Posisi tombol batal di kiri */
        }).then((result) => {
            if (result.isConfirmed) {
                // Sekarang endpoint-nya dinamis sesuai parameter yang dikirim!
                window.location.href = url + '?id=' + id; 
            }
        });
    }

    // 2. Toastr Notifikasi Alert (Membaca Session PHP)
    $(document).ready(function() {
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