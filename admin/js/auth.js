// auth.js (versi bersih)
$(document).ready(function() {
    // Handle login form submission
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        const username = $('#username').val();
        const password = $('#password').val();
        const rememberMe = $('#rememberMe').is(':checked');

        $.ajax({
            url: '/api/login',
            method: 'POST',
            data: {
                username: username,
                password: password,
                remember: rememberMe
            },
            success: function(response) {
                window.location.href = 'index.html';
            },
            error: function(xhr) {
                alert('Login failed: ' + (xhr.responseJSON?.message || 'Invalid credentials'));
            }
        });
    });

    // Handle logout
    $('#logoutBtn').click(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '/api/logout',
            method: 'POST',
            success: function() {
                window.location.href = 'login.html';
            },
            error: function() {
                window.location.href = 'login.html';
            }
        });
    });
});