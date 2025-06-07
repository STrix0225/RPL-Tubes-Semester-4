// js/script.js
$(document).ready(function () {
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
});
