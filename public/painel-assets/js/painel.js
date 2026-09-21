/* ============================
   QNB-Imobiliária — Painel JS
   ============================ */

document.addEventListener('DOMContentLoaded', function () {
    // Sidebar toggle (mobile)
    const toggle = document.querySelector('.ul-painel-sidebar-toggle');
    const sidebar = document.querySelector('.ul-painel-sidebar');

    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });

        // Close on click outside
        document.addEventListener('click', function (e) {
            if (sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    // Close alerts after 5s
    document.querySelectorAll('.alert-success').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.3s';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 300);
        }, 5000);
    });

    // Confirm delete
    document.querySelectorAll('form[onsubmit]').forEach(function (form) {
        // Handled inline
    });

    // Photo preview on file input change
    document.querySelectorAll('input[name="fotos[]"]').forEach(function (input) {
        input.addEventListener('change', function () {
            var container = input.closest('.ul-painel-card');
            var preview = container ? container.querySelector('.ul-painel-photos-preview') : null;
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'ul-painel-photos-preview mb-3';
                if (container) {
                    var formGroup = input.closest('.form-group');
                    if (formGroup) {
                        formGroup.parentNode.insertBefore(preview, formGroup);
                    }
                }
            }
            preview.innerHTML = '';

            Array.from(input.files).forEach(function (file, i) {
                if (!file.type.startsWith('image/')) return;
                var reader = new FileReader();
                reader.onload = function (e) {
                    var thumb = document.createElement('div');
                    thumb.className = 'ul-painel-photo-thumb';
                    thumb.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    if (i === 0) {
                        thumb.innerHTML += '<span class="ul-painel-photo-badge">Capa</span>';
                    }
                    preview.appendChild(thumb);
                };
                reader.readAsDataURL(file);
            });
        });
    });
});
