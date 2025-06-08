// js/script.js
$(document).ready(function () {
    // Toggle Theme
    $('#themeToggle').click(function () {
        const currentTheme = $('html').attr('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        $('html').attr('data-bs-theme', newTheme);

        if (newTheme === 'dark') {
            $(this).html('<i class="fas fa-sun"></i>');
        } else {
            $(this).html('<i class="fas fa-moon"></i>');
        }

        localStorage.setItem('theme', newTheme);
    });

    const savedTheme = localStorage.getItem('theme') || 'light';
    $('html').attr('data-bs-theme', savedTheme);

    if (savedTheme === 'dark') {
        $('#themeToggle').html('<i class="fas fa-sun"></i>');
    }

    // View Sales Modal AJAX Loader
$('.view-sales-btn').on('click', function () {
    const supplierId = $(this).data('id');

    $('#salesDetailsContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);

    $.ajax({
        url: '../../admin/Pages/viewSales.php',
        type: 'GET',
        data: { id: supplierId },
        success: function (response) {
            $('#salesDetailsContent').html(response);
        },
        error: function () {
            $('#salesDetailsContent').html('<div class="alert alert-danger">Failed to load sales data.</div>');
        }
    });
});

});

$(document).ready(function() {
    // View Sales button click handler
    $('.view-sales-btn').on('click', function() {
        var supplierId = $(this).data('id');
        
        $.ajax({
            url: 'getSupplierSales.php',
            type: 'GET',
            data: { id: supplierId },
            beforeSend: function() {
                $('#salesDetailsContent').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Loading sales information...</p>
                    </div>
                `);
            },
            success: function(response) {
                $('#salesDetailsContent').html(response);
            },
            error: function() {
                $('#salesDetailsContent').html(`
                    <div class="alert alert-danger">
                        Failed to load sales information. Please try again.
                    </div>
                `);
            }
        });
    });
    
    

    const salesByProductData = {
    labels: ["iPhone 15", "Galaxy S24", "Xiaomi 13", "Realme C55", "Infinix Zero"],
    datasets: [{
        label: "Units Sold",
        data: [120, 95, 60, 80, 45],
        backgroundColor: [
            '#4e73df',
            '#1cc88a',
            '#36b9cc',
            '#f6c23e',
            '#e74a3b'
        ]
    }]
};

const orderStatusData = {
    labels: ["Completed", "Pending", "Cancelled"],
    datasets: [{
        data: [250, 75, 30],
        backgroundColor: [
            '#1cc88a', // green
            '#f6c23e', // yellow
            '#e74a3b'  // red
        ],
        hoverOffset: 8
    }]
};

new Chart(document.getElementById("salesByProductChart"), {
    type: 'bar',
    data: salesByProductData,
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Top 5 Products by Sales' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

new Chart(document.getElementById("orderStatusChart"), {
    type: 'doughnut',
    data: orderStatusData,
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            title: { display: true, text: 'Order Status Breakdown' }
        }
    }
});

});