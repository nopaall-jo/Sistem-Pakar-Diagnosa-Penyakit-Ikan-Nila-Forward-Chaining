// Toggle sidebar
$('#sidebarToggleTop').click(function(e) {
    $('body').toggleClass('sidebar-toggled');
    $('.sidebar').toggleClass('toggled');
    if ($('.sidebar').hasClass('toggled')) {
        $('.sidebar .collapse').collapse('hide');
    }
});

// Prevent closing from click inside dropdown
$(document).on('click', '.dropdown-menu', function(e) {
    e.stopPropagation();
});

// Make dropdown work on hover
$('.dropdown').hover(function() {
    $(this).addClass('show');
    $(this).find('.dropdown-menu').addClass('show');
}, function() {
    $(this).removeClass('show');
    $(this).find('.dropdown-menu').removeClass('show');
});

// DataTable initialization (Sudah digabung jadi satu biar tidak error)
$(document).ready(function() {
    $('#dataTable').DataTable({
        responsive: true,
        pageLength: 10, // Jumlah baris per halaman
        language: {
            paginate: {
                previous: "&laquo;",
                next: "&raquo;"
            },
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            emptyTable: "Tidak ada data tersedia",
            zeroRecords: "Tidak ditemukan data yang sesuai"
        }
    });
});

// Form validation
$(document).ready(function() {
    $('form.needs-validation').each(function() {
        $(this).on('submit', function(e) {
            if (this.checkValidity() === false) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(this).addClass('was-validated');
        });
    });
});