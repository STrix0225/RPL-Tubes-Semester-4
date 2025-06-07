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
