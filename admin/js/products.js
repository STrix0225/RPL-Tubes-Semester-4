function isDarkMode() {
    return $('html').attr('data-bs-theme') === 'dark';
}

// Theme adaptation for brand table
function updateBrandTableTheme() {
    const isDarkMode = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const card = document.getElementById('brandDetailCard');
    
    if (isDarkMode) {
        card.classList.add('bg-dark', 'text-white');
        card.classList.remove('bg-white', 'text-dark');
        $('#brandTable').addClass('table-dark').removeClass('table-light');
    } else {
        card.classList.add('bg-white', 'text-dark');
        card.classList.remove('bg-dark', 'text-white');
        $('#brandTable').addClass('table-light').removeClass('table-dark');
    }
}

// Call this function when theme changes
document.addEventListener('DOMContentLoaded', function() {
    // Initial theme setup
    updateBrandTableTheme();
    
    // Watch for theme changes (assuming your sidebar.js triggers this)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'data-bs-theme') {
                updateBrandTableTheme();
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true
    });
});

$(document).ready(function () {
    // Inisialisasi DataTable
    const table = $('#productsTable').DataTable({
        responsive: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 6, 9] }
        ]
    });

    // Handler tombol hapus
    $('#productsTable').on('click', '.delete-btn', function () {
        const productId = $(this).data('id');
        $('#confirmDelete').attr('href', 'listProducts.php?delete=' + productId);
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });

    // Handler tombol edit
    $('#productsTable').on('click', '.edit-btn', function () {
        const productId = $(this).data('id');

        $.post('editProduct.php', { product_id: productId }, function (res) {
            if (res.success) {
                const p = res.data;
                $('#editProductId').val(p.product_id);
                $('#editProductName').val(p.product_name);
                $('#editProductBrand').val(p.product_brand);
                $('#editProductCategory').val(p.product_category);
                $('#editProductColor').val(p.product_color);
                $('#editProductDescription').val(p.product_description);
                $('#editProductPrice').val(p.product_price);
                $('#editProductDiscount').val(p.product_discount || 0);

                if (p.product_criteria === 'Favorite') {
                    $('#editCriteriaFavorite').prop('checked', true);
                } else {
                    $('#editCriteriaNonFavorite').prop('checked', true);
                }

                const basePath = '../../Customer/gems-customer-pages/images/';
                for (let i = 1; i <= 4; i++) {
                    const img = p[`product_image${i}`];
                    if (img) {
                        $(`#preview_image${i}`).attr('src', basePath + img).show();
                    } else {
                        $(`#preview_image${i}`).hide();
                    }
                }

                $('#editProductModal').modal('show');
            } else {
                alert('Failed to fetch product data.');
            }
        }, 'json');
    });

    // Submit form edit produk
    $('#editProductForm').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: 'editProduct.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $('#editProductModal').modal('hide');
                    alert(res.message);
                    location.reload();
                } else {
                    alert(res.message || 'Failed to update product');
                }
            },
            error: function (xhr) {
                alert('Error while updating product. ' + xhr.responseText);
            }
        });
    });

    // Preview gambar saat input file berubah
    $('input[type="file"]').on('change', function () {
        const previewId = 'preview_' + $(this).attr('id');
        const preview = $('#' + previewId);
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        } else {
            preview.hide();
        }
    });

    // Plugin teks tengah untuk donut chart
    const centerTextPlugin = {
        id: 'centerText',
        afterDraw(chart) {
            const { ctx, chartArea, innerRadius, outerRadius } = chart;
            ctx.save();

            const total = chart.data.datasets[0].data.reduce((a, b) => Number(a) + Number(b), 0);
            const centerX = (chartArea.left + chartArea.right) / 2;
            const centerY = (chartArea.top + chartArea.bottom) / 2;

            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = isDarkMode() ? '#fff' : '#000';

            ctx.font = 'bold 18px Arial';
            ctx.fillText(total.toLocaleString(), centerX, centerY - 5);

            ctx.font = 'normal 11px Arial';
            ctx.fillText('Total Qty', centerX, centerY + 12);
            ctx.restore();
        }
    };

    // Load chart donut berdasarkan kategori
    let chart;
    function loadChart(kategori = '') {
        $.get('listProducts.php?kategori=' + encodeURIComponent(kategori) + '&ajax=1', function (data) {
            const labels = data.map(item => item.product_brand);
            const values = data.map(item => item.total_qty);
            const colors = labels.map(() => '#' + Math.floor(Math.random() * 16777215).toString(16));

            const ctx = document.getElementById('qtyChart').getContext('2d');
            if (chart) chart.destroy();
            chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: isDarkMode() ? '#fff' : '#000' }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });

            // Update tabel brand di bawah chart
            const tbody = $('#brandTable tbody');
            tbody.empty();
            data.forEach(item => {
                tbody.append(`
                    <tr>
                        <td>${item.product_brand}</td>
                        <td>${item.product_category}</td>
                        <td>${item.total_qty}</td>
                    </tr>
                `);
            });
        });
    }

    // Ganti chart saat filter kategori berubah
    $('#filterKategori').change(function () {
        loadChart($(this).val());
    });

    // Load chart pertama kali
    loadChart();
});