$(document).ready(function() {
    $('#togglePassword').on('click', function() {
    const passwordField = $('#password');
    const icon = $(this).find('i');
    
    const fieldType = passwordField.attr('type');
    passwordField.attr('type', fieldType === 'password' ? 'text' : 'password');
    
    icon.toggleClass('fa-eye fa-eye-slash');
});

        // Add particles effect on click
        document.addEventListener('click', function(e) {
            const particle = document.createElement('div');
            particle.style.position = 'fixed';
            particle.style.width = '5px';
            particle.style.height = '5px';
            particle.style.backgroundColor = 'rgba(255, 255, 255, 0.7)';
            particle.style.borderRadius = '50%';
            particle.style.pointerEvents = 'none';
            particle.style.left = e.clientX + 'px';
            particle.style.top = e.clientY + 'px';
            particle.style.zIndex = '1000';
            
            document.body.appendChild(particle);
            
            // Animate particle
            const animation = particle.animate([
                { transform: 'scale(1)', opacity: 1 },
                { transform: 'scale(20)', opacity: 0 }
            ], {
                duration: 1000,
                easing: 'cubic-bezier(0, 0.2, 0.8, 1)'
            });
            
            animation.onfinish = () => particle.remove();
        });

        
});