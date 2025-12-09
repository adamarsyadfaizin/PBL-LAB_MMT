<?php
// components/comment_form.php - Form komentar universal dengan stored procedure
if (!isset($entity_type) || !isset($entity_id)) {
    die('Error: entity_type dan entity_id harus diset');
}
?>

<style>
.comment-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}
.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}
.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}
.rating-group {
    display: flex;
    gap: 10px;
    align-items: center;
}
.rating-stars {
    display: flex;
    gap: 5px;
}
.star {
    font-size: 20px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}
.star:hover,
.star.active {
    color: #ffc107;
}
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}
.btn-primary {
    background: #007bff;
    color: white;
}
.btn-primary:hover {
    background: #0056b3;
}
.btn-primary:disabled {
    background: #6c757d;
    cursor: not-allowed;
}
.alert {
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.loading {
    display: none;
    text-align: center;
    padding: 10px;
}
</style>

<div class="comment-form" id="commentForm">
    <h4>Tinggalkan Komentar</h4>
    
    <div id="commentAlert"></div>
    
    <form id="commentFormElement">
        <input type="hidden" id="entity_type" value="<?= htmlspecialchars($entity_type) ?>">
        <input type="hidden" id="entity_id" value="<?= htmlspecialchars($entity_id) ?>">
        
        <div class="form-group">
            <label for="author_name">Nama *</label>
            <input type="text" id="author_name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="author_email">Email</label>
            <input type="email" id="author_email" class="form-control" placeholder="email@example.com">
        </div>
        
        <?php if (in_array($entity_type, ['project', 'media'])): ?>
        <div class="form-group">
            <label>Rating</label>
            <div class="rating-group">
                <div class="rating-stars" id="ratingStars">
                    <span class="star" data-rating="1">★</span>
                    <span class="star" data-rating="2">★</span>
                    <span class="star" data-rating="3">★</span>
                    <span class="star" data-rating="4">★</span>
                    <span class="star" data-rating="5">★</span>
                </div>
                <span id="ratingText">Belum ada rating</span>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label for="content">Komentar *</label>
            <textarea id="content" class="form-control" rows="4" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary" id="submitBtn">
            Kirim Komentar
        </button>
        
        <div class="loading" id="loading">
            <i class="fas fa-spinner fa-spin"></i> Mengirim...
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('commentFormElement');
    const alertDiv = document.getElementById('commentAlert');
    const loading = document.getElementById('loading');
    const submitBtn = document.getElementById('submitBtn');
    let selectedRating = 0;
    
    // Rating functionality
    const stars = document.querySelectorAll('.star');
    const ratingText = document.getElementById('ratingText');
    
    if (stars.length > 0) {
        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                updateStars(selectedRating);
            });
            
            star.addEventListener('mouseenter', function() {
                const hoverRating = parseInt(this.dataset.rating);
                updateStars(hoverRating);
            });
        });
        
        document.getElementById('ratingStars').addEventListener('mouseleave', function() {
            updateStars(selectedRating);
        });
        
        function updateStars(rating) {
            stars.forEach((star, index) => {
                star.classList.toggle('active', index < rating);
            });
            
            const ratingTexts = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];
            ratingText.textContent = rating > 0 ? ratingTexts[rating] : 'Belum ada rating';
        }
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            entity_type: document.getElementById('entity_type').value,
            entity_id: parseInt(document.getElementById('entity_id').value),
            author_name: document.getElementById('author_name').value.trim(),
            author_email: document.getElementById('author_email').value.trim() || null,
            content: document.getElementById('content').value.trim(),
            rating: selectedRating > 0 ? selectedRating : null
        };
        
        // Validation
        if (!formData.author_name || !formData.content) {
            showAlert('Silakan lengkapi field yang wajib diisi', 'danger');
            return;
        }
        
        // Show loading
        loading.style.display = 'block';
        submitBtn.disabled = true;
        
        // Send to API
        fetch('admin/api/add_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Komentar berhasil dikirim! ' + 
                    (formData.rating ? 'Terima kasih atas rating Anda.' : ''), 'success');
                form.reset();
                selectedRating = 0;
                updateStars(0);
                
                // Refresh comments if function exists
                if (typeof refreshComments === 'function') {
                    setTimeout(refreshComments, 1000);
                }
            } else {
                showAlert(data.message || 'Gagal mengirim komentar', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        })
        .finally(() => {
            loading.style.display = 'none';
            submitBtn.disabled = false;
        });
    });
    
    function showAlert(message, type) {
        alertDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            alertDiv.innerHTML = '';
        }, 5000);
    }
});
</script>
