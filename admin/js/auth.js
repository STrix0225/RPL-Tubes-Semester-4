$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const password = $('#password');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    // Handle login form submission
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        const username = $('#username').val();
        const password = $('#password').val();
        const rememberMe = $('#rememberMe').is(':checked');

        // Simulate AJAX login (replace with actual API call)
        $.ajax({
            url: '/api/login',
            method: 'POST',
            data: {
                username: username,
                password: password,
                remember: rememberMe
            },
            success: function(response) {
                // On successful login, redirect to dashboard
                window.location.href = 'index.html';
            },
            error: function(xhr) {
                // Show error message
                alert('Login failed: ' + (xhr.responseJSON?.message || 'Invalid credentials'));
            }
        });
    });

    // Handle logout
    $('#logoutBtn').click(function(e) {
        e.preventDefault();
        
        // Simulate AJAX logout (replace with actual API call)
        $.ajax({
            url: '/api/logout',
            method: 'POST',
            success: function() {
                // On successful logout, redirect to login page
                window.location.href = 'login.html';
            },
            error: function() {
                // Still redirect even if logout fails
                window.location.href = 'login.html';
            }
        });
    });
});