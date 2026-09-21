/* QNB-Imobiliária — Painel da Imobiliária
   painel-auth.js — loader + logout */

(function () {
  'use strict';

  /* ---------- Logout ---------- */

  function ligarLogout() {
    document.querySelectorAll('[data-sair]').forEach(function (el) {
      el.addEventListener('click', function (e) {
        e.preventDefault();
        var form = el.closest('form');
        if (form) form.submit();
      });
    });
  }

  /* ---------- Init ---------- */

  document.addEventListener('DOMContentLoaded', function () {
    ligarLogout();
  });
})();
