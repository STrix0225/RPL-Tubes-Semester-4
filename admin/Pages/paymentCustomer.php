<?php
require_once '../../Database/connection.php';

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login-customer.php");
    exit();
}


$customer_id = $_SESSION['customer_id'];

// Get payment data for the logged-in customer
$payments = [];
$query = "SELECT p.*, o.order_status, o.order_date, o.order_cost 
          FROM payments p
          JOIN orders o ON p.order_id = o.order_id
          WHERE o.customer_id = ?
          ORDER BY p.payment_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $payments = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle payment deletion
if (isset($_GET['delete_payment'])) {
    $payment_id = (int)$_GET['delete_payment'];
    
    // Verify the payment belongs to the customer before deleting
    $check_query = "SELECT p.* FROM payments p
                   JOIN orders o ON p.order_id = o.order_id
                   WHERE p.payment_id = ? AND o.customer_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('ii', $payment_id, $customer_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $delete_stmt = $conn->prepare("DELETE FROM payments WHERE payment_id = ?");
        $delete_stmt->bind_param('i', $payment_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success_message'] = "Payment deleted successfully";
            header("Location: paymentCustomer.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to delete payment";
        }
    } else {
        $_SESSION['error_message'] = "Payment not found or you don't have permission to delete it";
    }
    
    header("Location: paymentCustomer.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Payments - Gadget MS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="../css/style.css" rel="stylesheet" />
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <style>
        .payment-container {
            background-color: var(--bg-card);
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 var(--shadow-color);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .payment-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .payment-status-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.65rem;
            border-radius: 0.25rem;
        }
        
        .status-completed {
            background-color: var(--color-success);
        }
        
        .status-pending {
            background-color: var(--color-warning);
        }
        
        .status-cancelled {
            background-color: var(--color-danger);
        }
        
        .action-buttons .btn {
            min-width: 80px;
            margin-right: 0.5rem;
        }
        
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        #paymentsTable {
            background-color: var(--bg-card);
        }
        
        #paymentsTable thead th {
            background-color: var(--color-primary);
            color: white;
            border-bottom: none;
        }
        
        #paymentsTable tbody tr {
            transition: all 0.2s ease;
        }
        
        #paymentsTable tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            background-color: var(--bg-card);
            border-radius: 0.5rem;
        }
        
        .empty-state-icon {
            font-size: 3rem;
            color: var(--color-secondary);
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .action-buttons .btn {
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../Layout/sidebar.php'; ?>

        <div id="content">
            <?php include '../Layout/header.php'; ?>
            
            <div class="container-fluid py-4">
                <div class="payment-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0 text-primary">
                            <i class="fas fa-credit-card me-2"></i>Payment History
                        </h1>
                        <div>
                            <button id="exportCSV" class="btn btn-success me-2">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </button>
                            <button id="exportPDF" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </button>
                        </div>
                    </div>
                    
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($payments)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <h4>No Payment History</h4>
                            <p class="text-muted">You haven't made any payments yet.</p>
                            <a href="shop.php" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-1"></i> Start Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table id="paymentsTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Order ID</th>
                                        <th>Transaction ID</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                                        <td><?php echo htmlspecialchars($payment['order_id']); ?></td>
                                        <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($payment['payment_date'])); ?></td>
                                        <td>$<?php echo number_format($payment['order_cost'], 2); ?></td>
                                        <td>
                                            <span class="payment-status-badge 
                                                <?php 
                                                    switch($payment['order_status']) {
                                                        case 'completed': echo 'status-completed'; break;
                                                        case 'processing': echo 'bg-primary'; break;
                                                        case 'pending': echo 'status-pending'; break;
                                                        case 'cancelled': echo 'status-cancelled'; break;
                                                        default: echo 'bg-secondary';
                                                    }
                                                ?>">
                                                <?php echo ucfirst(htmlspecialchars($payment['order_status'])); ?>
                                            </span>
                                        </td>
                                        <td class="action-buttons">
                                            <button class="btn btn-sm btn-primary view-payment-details" 
                                                data-payment-id="<?php echo $payment['payment_id']; ?>"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#paymentDetailsModal">
                                                <i class="fas fa-eye me-1"></i> Details
                                            </button>
                                            <a href="paymentCustomer.php?delete_payment=<?php echo $payment['payment_id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this payment record?');">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Payment Details Modal -->
            <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="paymentDetailsModalLabel">
                                <i class="fas fa-receipt me-2"></i>Payment Details
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="paymentDetailsContent">
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-3">Loading payment details...</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php include '../Layout/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/script.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable with responsive settings
        var table = $('#paymentsTable').DataTable({
            responsive: true,
            order: [[3, 'desc']],
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search payments...",
                lengthMenu: "Show _MENU_ payments per page",
                info: "Showing _START_ to _END_ of _TOTAL_ payments",
                infoEmpty: "No payments found",
                infoFiltered: "(filtered from _MAX_ total payments)"
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control');
                $('.dataTables_length select').addClass('form-select');
            }
        });

        // Payment details modal handler
        $('.view-payment-details').on('click', function() {
            const paymentId = $(this).data('payment-id');
            $('#paymentDetailsContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3">Loading payment details...</p>
                </div>
            `);

            $.ajax({
                url: 'getPaymentDetails.php',
                method: 'GET',
                data: { payment_id: paymentId },
                success: function(response) {
                    $('#paymentDetailsContent').html(response);
                },
                error: function() {
                    $('#paymentDetailsContent').html(`
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Failed to load payment details. Please try again.
                        </div>
                    `);
                }
            });
        });

        // Export to CSV - Fixed version
        $('#exportCSV').click(function() {
            // Show loading state
            const btn = $(this);
            const originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Exporting...').prop('disabled', true);
            
            try {
                // Get filtered data from DataTables
                const data = table.rows({ search: 'applied' }).data();
                let csvContent = "data:text/csv;charset=utf-8,";
                
                // Headers
                csvContent += "Payment ID,Order ID,Transaction ID,Date,Amount,Status\r\n";
                
                // Rows
                data.each(function(row) {
                    // Access data directly from DataTables row object
                    const rowData = [
                        row[0], // Payment ID
                        row[1], // Order ID
                        row[2], // Transaction ID
                        row[3], // Date
                        row[4], // Amount
                        $(row[5]).text().trim() // Status (needs jQuery for the badge)
                    ];
                    
                    // Properly escape CSV values
                    const escapedRow = rowData.map(field => {
                        if (typeof field === 'string') {
                            return `"${field.replace(/"/g, '""')}"`;
                        }
                        return field;
                    }).join(',');
                    
                    csvContent += escapedRow + "\r\n";
                });
                
                // Create download link
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", `payment_history_${new Date().toISOString().slice(0,10)}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                console.error('CSV export error:', error);
                alert('Error during CSV export: ' + error.message);
            } finally {
                // Restore button state
                btn.html(originalHtml).prop('disabled', false);
            }
        });

        // Export to PDF - Fixed version
        $('#exportPDF').click(function() {
            // Show loading state
            const btn = $(this);
            const originalHtml = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Exporting...').prop('disabled', true);
            
            try {
                // Check if jsPDF is loaded
                if (typeof window.jspdf === 'undefined') {
                    throw new Error('PDF library not loaded. Please try again.');
                }
                
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('p', 'pt');
                const title = "Payment History - Gadget MS";
                const dateString = new Date().toLocaleString();
                
                // Title
                doc.setFontSize(18);
                doc.setTextColor(40);
                doc.text(title, 40, 30);
                
                // Date
                doc.setFontSize(10);
                doc.text(`Generated on: ${dateString}`, 40, 45);
                
                // Get table data
                const data = table.rows({ search: 'applied' }).data();
                const pdfData = [];
                
                // Prepare data for PDF
                data.each(function(row) {
                    pdfData.push([
                        row[0], // Payment ID
                        row[1], // Order ID
                        row[2], // Transaction ID
                        row[3], // Date
                        row[4], // Amount
                        $(row[5]).text().trim() // Status
                    ]);
                });
                
                // Add table
                doc.autoTable({
                    head: [['Payment ID', 'Order ID', 'Transaction ID', 'Date', 'Amount', 'Status']],
                    body: pdfData,
                    startY: 60,
                    margin: { left: 40 },
                    headStyles: {
                        fillColor: [78, 115, 223],
                        textColor: 255,
                        fontStyle: 'bold'
                    },
                    styles: {
                        fontSize: 8,
                        cellPadding: 3,
                        overflow: 'linebreak'
                    },
                    columnStyles: {
                        0: { cellWidth: 40 },
                        1: { cellWidth: 40 },
                        2: { cellWidth: 60 },
                        3: { cellWidth: 60 },
                        4: { cellWidth: 40 },
                        5: { cellWidth: 40 }
                    },
                    didDrawPage: function(data) {
                        // Footer
                        const pageCount = doc.internal.getNumberOfPages();
                        doc.setFontSize(10);
                        doc.setTextColor(150);
                        for (let i = 1; i <= pageCount; i++) {
                            doc.setPage(i);
                            doc.text(
                                `Page ${i} of ${pageCount}`,
                                doc.internal.pageSize.width - 60,
                                doc.internal.pageSize.height - 20
                            );
                        }
                    }
                });
                
                // Save PDF
                doc.save(`payment_history_${new Date().toISOString().slice(0,10)}.pdf`);
            } catch (error) {
                console.error('PDF export error:', error);
                alert('Error during PDF export: ' + error.message);
            } finally {
                // Restore button state
                btn.html(originalHtml).prop('disabled', false);
            }
        });
    });
</script>
</body>
</html>