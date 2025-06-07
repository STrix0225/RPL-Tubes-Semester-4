// Initialize DataTable with responsive settings
$(document).ready(function() {
    const table = $('#productsTable').DataTable({
        responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 2 },
            { responsivePriority: 3, targets: -1 },
            { orderable: false, targets: -1 } // Disable sorting for tools column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search products...",
            lengthMenu: "Show _MENU_ products per page",
            zeroRecords: "No products found",
            info: "Showing _START_ to _END_ of _TOTAL_ products",
            infoEmpty: "No products available",
            infoFiltered: "(filtered from _MAX_ total products)"
        }
    });

    // Delete confirmation modal
    $(document).on('click', '.delete-btn', function() {
        const productId = $(this).data('id');
        $('#confirmDelete').attr('href', 'listProducts.php?delete=' + productId);
        $('#deleteModal').modal('show');
    });

    // Adjust table on window resize
    $(window).on('resize', function() {
        table.columns.adjust().responsive.recalc();
    });
});