// Fungsi untuk generate CAPTCHA acak
function generateCaptcha() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    let captcha = '';
    const length = 5;
    
    for (let i = 0; i < length; i++) {
        captcha += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    return captcha;
}

// Fungsi untuk menampilkan CAPTCHA
function displayCaptcha() {
    const captchaText = generateCaptcha();
    document.getElementById('captchaText').textContent = captchaText;
    // Simpan CAPTCHA yang benar untuk validasi
    document.getElementById('captchaBox').dataset.correctCaptcha = captchaText;
    
    // Reset error message
    document.getElementById('captchaError').style.display = 'none';
    document.getElementById('captcha').classList.remove('error');
}

// Fungsi validasi CAPTCHA
function validateCaptcha() {
    const userInput = document.getElementById('captcha').value;
    const correctCaptcha = document.getElementById('captchaBox').dataset.correctCaptcha;
    const captchaError = document.getElementById('captchaError');
    
    if (userInput.toUpperCase() !== correctCaptcha.toUpperCase()) {
        captchaError.style.display = 'block';
        document.getElementById('captcha').classList.add('error');
        document.getElementById('captcha').style.borderColor = 'var(--color-error)';
        return false;
    }
    
    captchaError.style.display = 'none';
    document.getElementById('captcha').classList.remove('error');
    document.getElementById('captcha').style.borderColor = '';
    return true;
}

// Fungsi validasi form lengkap
function validateForm() {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const role = document.getElementById('role').value;
    
    let isValid = true;
    
    // Validasi role
    if (!role) {
        showFieldError('role', 'Pilih peran Anda');
        isValid = false;
    } else {
        clearFieldError('role');
    }
    
    // Validasi username
    if (!username) {
        showFieldError('username', 'Username atau email harus diisi');
        isValid = false;
    } else {
        clearFieldError('username');
    }
    
    // Validasi password
    if (!password) {
        showFieldError('password', 'Password harus diisi');
        isValid = false;
    } else if (password.length < 6) {
        showFieldError('password', 'Password minimal 6 karakter');
        isValid = false;
    } else {
        clearFieldError('password');
    }
    
    // Validasi CAPTCHA
    if (!validateCaptcha()) {
        isValid = false;
    }
    
    return isValid;
}

// Fungsi menampilkan error field
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    clearFieldError(fieldId);
    
    field.style.borderColor = 'var(--color-error)';
    field.style.boxShadow = '0 0 0 3px rgba(220, 53, 69, 0.15)';
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.display = 'block';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

// Fungsi menghapus error field
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    field.style.borderColor = '';
    field.style.boxShadow = '';
    
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError && existingError.id !== 'captchaError') {
        existingError.remove();
    }
}

// Fungsi simulasi login
function simulateLogin(formData) {
    return new Promise((resolve) => {
        setTimeout(() => {
            // Simulasi response dari server
            const success = Math.random() > 0.2; // 80% success rate untuk simulasi
            if (success) {
                resolve({
                    success: true,
                    message: 'Login berhasil! Mengalihkan...',
                    redirectUrl: '../beranda.php'
                });
            } else {
                resolve({
                    success: false,
                    message: 'Username, password, atau peran tidak sesuai'
                });
            }
        }, 1500);
    });
}

// Fungsi menampilkan pesan status
function showStatusMessage(message, type) {
    // Hapus pesan sebelumnya
    const existingMessage = document.querySelector('.status-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `status-message status-${type}`;
    messageDiv.style.padding = '12px 16px';
    messageDiv.style.margin = '16px 0';
    messageDiv.style.borderRadius = '6px';
    messageDiv.style.fontWeight = '600';
    messageDiv.style.textAlign = 'center';
    
    if (type === 'success') {
        messageDiv.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
        messageDiv.style.color = '#28a745';
        messageDiv.style.border = '1px solid #28a745';
    } else {
        messageDiv.style.backgroundColor = 'rgba(220, 53, 69, 0.1)';
        messageDiv.style.color = '#dc3545';
        messageDiv.style.border = '1px solid #dc3545';
    }
    
    messageDiv.textContent = message;
    
    // Sisipkan sebelum form
    const form = document.getElementById('loginForm');
    form.parentNode.insertBefore(messageDiv, form);
    
    // Auto-hide pesan sukses setelah 3 detik
    if (type === 'success') {
        setTimeout(() => {
            messageDiv.remove();
        }, 3000);
    }
}

// Event listener untuk form submission
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        return;
    }
    
    const loginButton = document.getElementById('loginButton');
    const originalText = loginButton.textContent;
    
    // Disable form dan tampilkan loading state
    loginButton.disabled = true;
    loginButton.textContent = 'Memproses...';
    const formElements = this.elements;
    for (let element of formElements) {
        element.disabled = true;
    }
    
    try {
        // Kumpulkan data form
        const formData = {
            role: document.getElementById('role').value,
            username: document.getElementById('username').value,
            password: document.getElementById('password').value,
            remember: document.getElementById('remember').checked
        };
        
        // Simulasi proses login
        const result = await simulateLogin(formData);
        
        if (result.success) {
            showStatusMessage(result.message, 'success');
            // Redirect setelah delay
            setTimeout(() => {
                window.location.href = result.redirectUrl;
            }, 2000);
        } else {
            showStatusMessage(result.message, 'error');
            // Regenerate CAPTCHA jika login gagal
            displayCaptcha();
            document.getElementById('captcha').value = '';
        }
        
    } catch (error) {
        showStatusMessage('Terjadi kesalahan. Silakan coba lagi.', 'error');
        console.error('Login error:', error);
    } finally {
        // Enable form kembali
        loginButton.disabled = false;
        loginButton.textContent = originalText;
        for (let element of formElements) {
            element.disabled = false;
        }
    }
});

// Event listener untuk refresh CAPTCHA
document.getElementById('refreshCaptcha').addEventListener('click', function() {
    displayCaptcha();
    document.getElementById('captcha').value = '';
});

// Event listener untuk validasi real-time CAPTCHA
document.getElementById('captcha').addEventListener('input', function() {
    if (this.value.length > 0) {
        document.getElementById('captchaError').style.display = 'none';
        this.classList.remove('error');
        this.style.borderColor = '';
    }
});

// Event listener untuk validasi real-time pada field lainnya
document.getElementById('username').addEventListener('input', function() {
    clearFieldError('username');
});

document.getElementById('password').addEventListener('input', function() {
    clearFieldError('password');
});

document.getElementById('role').addEventListener('change', function() {
    clearFieldError('role');
});

// Event listener untuk tombol Enter pada CAPTCHA field
document.getElementById('captcha').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('loginForm').dispatchEvent(new Event('submit'));
    }
});

// Event listener untuk forgot password
document.querySelector('.forgot-password').addEventListener('click', function(e) {
    e.preventDefault();
    alert('Fitur lupa password akan mengirimkan reset link ke email terdaftar.\n(Silahkan hubungi administrator untuk reset password.)');
});

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    displayCaptcha();
    
    // Set focus ke field username
    document.getElementById('username').focus();
    
    // Tambahkan efek subtle animation pada card
    const loginCard = document.querySelector('.login-card');
    loginCard.style.opacity = '0';
    loginCard.style.transform = 'translateY(20px)';
    loginCard.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    
    setTimeout(() => {
        loginCard.style.opacity = '1';
        loginCard.style.transform = 'translateY(0)';
    }, 100);
});