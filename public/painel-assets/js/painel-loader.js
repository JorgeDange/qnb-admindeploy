/* QNB-Imobiliária — Painel do Cliente
   painel-loader.js — preloader visível durante o carregamento da página
   (com tempo mínimo de exibição) e ocultado após o evento load. */

(function () {
  'use strict';

  var preloader = document.getElementById('preloader');
  if (!preloader) return;

  var inicio = Date.now();
  var MIN_VISIVEL = 500;

  function esconderPreloader() {
    var restante = MIN_VISIVEL - (Date.now() - inicio);
    setTimeout(function () {
      preloader.style.display = 'none';
      document.body.style.position = 'static';
    }, Math.max(0, restante));
  }

  if (document.readyState === 'complete') {
    esconderPreloader();
  } else {
    window.addEventListener('load', esconderPreloader);
  }
})();