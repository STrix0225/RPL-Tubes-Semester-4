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

    // View Sales Modal AJAX Loader (gunakan URL yang sesuai)
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
            url: '../../admin/Pages/viewSales.php', // pastikan ini benar, atau ganti sesuai kebutuhan
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

    // Inisialisasi preview image dengan tulisan default
    for (let i = 1; i <= 4; i++) {
        $(`#preview${i}`).html('<div class="preview-text">No image selected</div>');
    }

    // Chart.js - kamu bisa letakkan disini (tidak perlu diubah)
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

}); // akhir document ready

// Fungsi previewImage harus global supaya bisa dipanggil dari HTML onchange=""
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;

    const file = input.files[0];
    if (file) {
        const reader = new FileReader();

        reader.onload = function (e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 100%; height: auto;">`;
        };

        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<div class="preview-text">No image selected</div>';
    }
}

// Fungsi clearImage juga harus global supaya bisa dipanggil dari HTML onclick=""
function clearImage(inputId, previewId) {
    const inputElem = document.getElementById(inputId);
    const previewElem = document.getElementById(previewId);

    if (inputElem) inputElem.value = '';
    if (previewElem) previewElem.innerHTML = '<div class="preview-text">No image selected</div>';
}

$(document).ready(function () {
    $('#product_name').on('input', function () {
        const name = $(this).val().trim();
        if (name.length === 0) {
            $('#product_name').removeClass('is-invalid');
            $('#name-feedback').addClass('d-none');
            return;
        }

        $.get('check_product_name.php', { name: name }, function (data) {
            const res = JSON.parse(data);
            if (res.exists) {
                $('#product_name').addClass('is-invalid');
                $('#name-feedback').removeClass('d-none');
            } else {
                $('#product_name').removeClass('is-invalid');
                $('#name-feedback').addClass('d-none');
            }
        });
    });
});