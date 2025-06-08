$(document).ready(function() {
            // Initialize DataTable
            $('#customersTable').DataTable({
                responsive: true,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 3 },
                    { responsivePriority: 4, targets: 4 },
                    { responsivePriority: 5, targets: -1 }
                ]
            });

            // Delete button click handler
            $('.delete-btn').click(function() {
                var customerId = $(this).data('id');
                $('#confirmDelete').attr('href', 'listCustomers.php?delete=' + customerId);
                $('#deleteModal').modal('show');
            });
        });