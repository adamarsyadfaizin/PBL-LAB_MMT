// ===============================
// VALIDASI FORM TANPA CAPTCHA JS
// (captcha div & input sudah di-handle PHP)
// ===============================

// Hapus pesan error inline (jika ada)
function clearInlineError(fieldId) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    field.style.borderColor = "";
    field.style.boxShadow = "";

    const err = field.parentNode.querySelector(".error-message");
    if (err) err.remove();
}

// Tampilkan error inline
function showInlineError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (!field) return;

    clearInlineError(fieldId);

    field.style.borderColor = "var(--color-error)";
    field.style.boxShadow = "0 0 0 3px rgba(220, 53, 69, 0.15)";

    const div = document.createElement("div");
    div.className = "error-message";
    div.style.color = "#dc3545";
    div.style.marginTop = "4px";
    div.style.fontSize = "0.85rem";
    div.textContent = message;

    field.parentNode.appendChild(div);
}

// Validasi dasar form
function validateFormBasic() {
    let ok = true;

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const captcha  = document.getElementById("captcha").value.trim();

    if (!username) {
        showInlineError("username", "Username atau email wajib diisi");
        ok = false;
    } else clearInlineError("username");

    if (!password) {
        showInlineError("password", "Password wajib diisi");
        ok = false;
    } else clearInlineError("password");

    if (!captcha) {
        showInlineError("captcha", "Isi kode captcha");
        ok = false;
    } else clearInlineError("captcha");

    return ok;
}

// ===============================
// HANDLE SUBMIT FORM
// ===============================

document.getElementById("loginForm").addEventListener("submit", function (e) {
    // Validasi ringan
    if (!validateFormBasic()) {
        e.preventDefault();
        return;
    }

    // Jika lolos → biarkan form submit ke PHP normal
    const btn = document.getElementById("loginButton");
    btn.disabled = true;
    btn.textContent = "Memproses...";
});

// Hapus error realtime saat user mengetik
["username", "password", "captcha"].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener("input", () => clearInlineError(id));
    }
});

// Animasi muncul card
document.addEventListener("DOMContentLoaded", () => {
    const card = document.querySelector(".login-card");
    if (card) {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        card.style.transition = "0.5s";

        setTimeout(() => {
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, 100);
    }
});
