$(document).ready(function() {
    $('#dataTable').DataTable({
        "pageLength": 10,
        "language": {
            "paginate": {
                "previous": "&laquo;",
                "next": "&raquo;"
            }
        }
    });
});