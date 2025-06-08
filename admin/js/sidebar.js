$(document).ready(function () {
    // Fungsi untuk sidebar toggle (seperti sebelumnya)
    function setSidebarState(isClosed) {
        if (isClosed) {
            $('#sidebar').addClass('active');
            $('#content').addClass('active');
        } else {
            $('#sidebar').removeClass('active');
            $('#content').removeClass('active');
        }
    }

    function handleResize() {
        if ($(window).width() < 768) {
            setSidebarState(true);
        } else {
            const isSidebarClosed = localStorage.getItem('sidebarClosed') === 'true';
            setSidebarState(isSidebarClosed);
        }
    }

    // Fungsi untuk menangani state collapse dropdown
    function handleCollapseState() {
        // Inisialisasi semua collapse element
        $('.collapse').each(function() {
            const collapseId = $(this).attr('id');
            const collapseState = localStorage.getItem(`collapse_${collapseId}`);
            const bsCollapse = new bootstrap.Collapse(this, {
                toggle: false
            });
            
            if (collapseState === 'show') {
                bsCollapse.show();
            } else {
                bsCollapse.hide();
            }
        });

        // Simpan state saat collapse di-toggle
        $('.dropdown-toggle').on('click', function() {
            const target = $(this).attr('data-bs-target') || $(this).attr('href');
            const targetId = target.replace('#', '');
            const targetElement = $(target);
            
            // Tunggu sedikit untuk mendapatkan state yang baru
            setTimeout(() => {
                const isShown = targetElement.hasClass('show');
                localStorage.setItem(`collapse_${targetId}`, isShown ? 'show' : 'hide');
            }, 100);
        });
    }

    // Load initial state
    handleResize();
    handleCollapseState();

    // Toggle sidebar on button click
    $('#sidebarToggle, #sidebarCollapse').click(function () {
        $('#sidebar').toggleClass('active');
        $('#content').toggleClass('active');

        if ($(window).width() >= 768) {
            localStorage.setItem('sidebarClosed', $('#sidebar').hasClass('active'));
        }
    });

    // Handle window resize
    $(window).resize(function () {
        handleResize();
    });
});