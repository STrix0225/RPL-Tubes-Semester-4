<?php
session_start();
include('layouts/header.php');

// Query untuk ambil data customers
$query_customers = "SELECT * FROM customers ORDER BY customer_id DESC";
$stmt_customers = $conn->prepare($query_customers);
$stmt_customers->execute();
$customers = $stmt_customers->get_result();
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Customers</h1>
    <nav class="mt-4 rounded" aria-label="breadcrumb">
        <ol class="breadcrumb px-3 py-2 rounded mb-4">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Customers</li>
        </ol>
    </nav>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Customers</h6>
        </div>
        <div class="card-body">
            <?php if (isset($_GET['success_status'])) { ?>
                <div class="alert alert-info" role="alert">
                    <?php echo $_GET['success_status']; ?>
                </div>
            <?php } ?>
            <?php if (isset($_GET['fail_status'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_GET['fail_status']; ?>
                </div>
            <?php } ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Photo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer) { ?>
                            <tr>
                                <td><?php echo $customer['customer_id']; ?></td>
                                <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($customer['customer_email']); ?></td>
                                <td><?php echo htmlspecialchars($customer['customer_phone']); ?></td>
                                <td><?php echo htmlspecialchars($customer['customer_city']); ?></td>
                                <td><?php echo htmlspecialchars($customer['customer_address']); ?></td>
                                <td class="text-center">
                                    <?php if (!empty($customer['customer_photo'])) { ?>
                                        <img src="../img/Customers/<?php echo $customer['customer_photo']; ?>" width="50" class="img-thumbnail rounded-circle">
                                    <?php } else { echo "-"; } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<?php include('layouts/footer.php'); ?>
