/* QNB-Imobiliária — Área do Cliente JS */
document.addEventListener('DOMContentLoaded', function () {
    /* Marca nav item ativo */
    var path = window.location.pathname;
    document.querySelectorAll('.sidebar-nav a').forEach(function (link) {
        if (link.getAttribute('href') === path) {
            link.closest('li').classList.add('active');
        }
    });

    /* Auto-dismiss flash messages */
    document.querySelectorAll('.flash-message').forEach(function (el) {
        setTimeout(function () {
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 300);
        }, 5000);
    });
});
