<?php
include __DIR__ . '/../Database/connection.php';

$query = "SELECT 
            orders.order_id,
            orders.order_cost,
            orders.order_status,
            orders.order_date,
            customers.customer_name,
            customers.customer_city,
            customers.customer_phone,
            customers.customer_photo
          FROM orders
          INNER JOIN customers ON orders.customer_id = customers.customer_id";

$result = mysqli_query($conn, $query);

// Error handling
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>
