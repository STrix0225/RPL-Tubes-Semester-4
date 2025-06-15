$(document).ready(function () {
    // ------------------------------
    // 1. Toggle Password Visibility
    // ------------------------------
    $('#togglePassword').on('click', function () {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        icon.toggleClass('fa-eye fa-eye-slash');
    });

    // ------------------------------
    // 2. Click Particle Effect
    // ------------------------------
    document.addEventListener('click', function (e) {
        const particle = document.createElement('div');
        Object.assign(particle.style, {
            position: 'fixed',
            width: '5px',
            height: '5px',
            backgroundColor: 'rgba(255, 255, 255, 0.7)',
            borderRadius: '50%',
            pointerEvents: 'none',
            left: `${e.clientX}px`,
            top: `${e.clientY}px`,
            zIndex: 1000
        });

        document.body.appendChild(particle);

        const animation = particle.animate([
            { transform: 'scale(1)', opacity: 1 },
            { transform: 'scale(20)', opacity: 0 }
        ], {
            duration: 1000,
            easing: 'cubic-bezier(0, 0.2, 0.8, 1)'
        });

        animation.onfinish = () => particle.remove();
    });

    // ------------------------------
    // 3. Flying Devices Animation
    // ------------------------------
    const container = document.querySelector('.flying-devices');
    if (container) {
        const devices = [
            { class: "laptop", animation: "fly-laptop 40s linear infinite" },
            { class: "smartphone", animation: "fly-smartphone 30s linear infinite" },
            { class: "desktop", animation: "fly-desktop 50s linear infinite" },
            { class: "headphone", animation: "fly-headphone 35s linear infinite" },
            { class: "handphone", animation: "fly-handphone 32s linear infinite" }
        ];

        devices.forEach(device => {
            const div = document.createElement("div");
            div.classList.add("flying-device", device.class);
            div.style.top = `${Math.floor(Math.random() * 80)}%`;
            div.style.left = `${Math.floor(Math.random() * 80)}%`;
            div.style.animation = device.animation;
            container.appendChild(div);
        });
    }

    // ------------------------------
    // 4. OTP Input Validation & Resend OTP
    // ------------------------------
    const otpField = $('#otp');
    const resendBtn = $('#resendBtn');

    if (otpField.length) {
        // Only run OTP logic if on OTP page

        // Validate OTP input (numbers only)
        otpField.on('input', function () {
            this.value = this.value.replace(/\D/g, '');
        });

        // Resend OTP countdown logic
        let countdown = 60;
        let canResend = true;
        let countdownInterval;

        function startCountdown() {
            canResend = false;
            resendBtn.prop('disabled', true);
            updateButtonText();

            countdownInterval = setInterval(() => {
                countdown--;
                updateButtonText();

                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    resendBtn.prop('disabled', false).text('Resend OTP');
                    canResend = true;
                }
            }, 1000);
        }

        function updateButtonText() {
            resendBtn.text(`Resend OTP (${countdown}s)`);
        }

        // Start countdown on page load
        startCountdown();

        // Handle resend button click
        // In your login.js file
            resendBtn.on('click', function (e) {
                e.preventDefault();
                if (!canResend) return;

                // Show loading state
                resendBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');

                $.ajax({
                    url: window.location.href,
                    method: 'POST',
                    data: { resend_otp: true },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Reset countdown
                            countdown = 60;
                            startCountdown();
                            
                            // Show success message
                            alert(response.message);
                        } else {
                            alert(response.message);
                            resendBtn.prop('disabled', false).text('Resend OTP');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        alert('An error occurred while resending OTP. Please try again.');
                        resendBtn.prop('disabled', false).text('Resend OTP');
                    }
                });
            });

        // Clear countdown on page unload
        $(window).on('beforeunload', function () {
            clearInterval(countdownInterval);
        });
    }
});
