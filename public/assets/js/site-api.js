/* QNB-Imobiliária — site-api.js
   Integração das páginas públicas com a API /api/v1:
   estatísticas, destaques, abas, cidades, grelhas, filtros, detalhe do
   imóvel e formulários de contacto. */

window.QNBSite = (function () {
  'use strict';

  var TIPOS = {
    apartamento: 'Apartamento', vivenda: 'Moradia', terreno: 'Terreno',
    loja: 'Loja', escritorio: 'Escritório', armazem: 'Armazém', quintal: 'Quintal'
  };
  var FINALIDADES = { arrendar: 'Arrendamento', comprar: 'Compra', vender: 'Venda' };

  var PROVINCIAS = ['Luanda', 'Benguela', 'Huambo', 'Huíla', 'Cabinda', 'Malanje', 'Namibe', 'Uíge'];

  function numero(n) {
    var v = Number(n || 0);
    return v.toLocaleString('pt-PT');
  }

  function precoCompleto(im) {
    var simbolo = im.moeda === 'USD' ? 'US$ ' : '';
    return simbolo + numero(im.preco) + (im.moeda === 'USD' ? '' : ' Kz');
  }

  function fotoCapa(im, fallback) {
    if (im.fotos && im.fotos.length) return window.QNBApi.urlFoto(im.fotos[0].caminho);
    return fallback || 'assets/img/project-1.jpg';
  }

  function rotuloFinalidade(im) {
    return FINALIDADES[im.finalidade] || im.finalidade;
  }

  function infosHtml(im, featured) {
    var extra = featured ? ' ul-featured-property-info' : '';
    var itens = [];
    if (im.quartos) {
      itens.push('<div class="ul-project-info' + extra + '"><span class="icon"><i class="flaticon-bed-color"></i></span><span class="text">' + im.quartos + ' Quartos</span></div>');
    }
    if (im.wc) {
      itens.push('<div class="ul-project-info' + extra + '"><span class="icon"><i class="flaticon-bath"></i></span><span class="text">' + im.wc + ' Casas de Banho</span></div>');
    }
    if (im.area) {
      itens.push('<div class="ul-project-info' + extra + '"><span class="icon"><i class="flaticon-scale"></i></span><span class="text">' + numero(im.area) + ' m²</span></div>');
    }
    return itens.join('');
  }

  function cardGrid(im) {
    return '' +
      '<div class="col wow animate__fadeInUp">' +
        '<div class="ul-project">' +
          '<div class="ul-project-img">' +
            '<img src="' + fotoCapa(im) + '" alt="' + im.titulo + '">' +
            '<span class="ul-project-tag">' + rotuloFinalidade(im) + '</span>' +
          '</div>' +
          '<div class="ul-project-txt">' +
            '<div class="top">' +
              '<div class="left">' +
                '<span class="ul-project-price"><span class="number">' + precoCompleto(im) + '</span></span>' +
                '<a href="project-details.html?id=' + im.id + '" class="ul-project-title">' + im.titulo + '</a>' +
                '<p class="ul-project-location">' + (im.municipio ? im.municipio + ', ' : '') + im.provincia + '</p>' +
              '</div>' +
              '<div class="right"><button class="ul-project-add-to-favorites-btn"><i class="flaticon-heart"></i></button></div>' +
            '</div>' +
            '<div class="ul-project-infos">' + infosHtml(im, false) + '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
  }

  function cardFeatured(im, indice) {
    var idx = String(indice).padStart(2, '0');
    return '' +
      '<div class="swiper-slide">' +
        '<div class="ul-featured-property ul-project">' +
          '<div>' +
            '<div class="header">' +
              '<div class="left"><span class="index">' + idx + '</span></div>' +
              '<div class="right"><button class="ul-project-add-to-favorites-btn"><i class="flaticon-heart"></i></button></div>' +
            '</div>' +
            '<a href="project-details.html?id=' + im.id + '" class="ul-project-title">' + im.titulo + '</a>' +
            '<p class="ul-project-location">' + (im.municipio ? im.municipio + ', ' : '') + im.provincia + '</p>' +
          '</div>' +
          '<div class="ul-project-img">' +
            '<img src="' + fotoCapa(im) + '" alt="' + im.titulo + '">' +
            '<span class="ul-project-tag">' + rotuloFinalidade(im) + '</span>' +
          '</div>' +
          '<div class="ul-project-txt">' +
            '<span class="ul-project-price"><span class="number">' + precoCompleto(im) + '</span></span>' +
            '<div class="ul-project-infos ul-featured-property-infos">' + infosHtml(im, true) + '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
  }

  function cardSidebar(im) {
    return '' +
      '<div class="swiper-slide">' +
        '<div class="ul-project">' +
          '<div class="ul-project-img"><img src="' + fotoCapa(im) + '" alt="' + im.titulo + '"></div>' +
          '<div class="ul-project-txt">' +
            '<span class="ul-project-tag">' + rotuloFinalidade(im) + '</span>' +
            '<div class="top">' +
              '<div class="left">' +
                '<span class="ul-project-price"><span class="number">' + precoCompleto(im) + '</span></span>' +
                '<a href="project-details.html?id=' + im.id + '" class="ul-project-title">' + im.titulo + '</a>' +
                '<p class="ul-project-location">' + (im.municipio ? im.municipio + ', ' : '') + im.provincia + '</p>' +
              '</div>' +
              '<div class="right"><button class="ul-project-add-to-favorites-btn"><i class="flaticon-heart"></i></button></div>' +
            '</div>' +
            '<div class="ul-project-infos ul-featured-property-infos">' + infosHtml(im, true) + '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
  }

  function mensagemVazia(texto) {
    return '<p class="ul-projects-vazio">' + texto + '</p>';
  }

  /* ------------------------- INDEX ------------------------- */

  function carregarEstatisticas() {
    return window.QNBApi.get('/estatisticas').then(function (r) {
      var itens = document.querySelectorAll('.ul-stats-item .number');
      var valores = [r.imoveis_anunciados, r.imobiliarias, r.anuncios_verificados, Math.round(r.area_anunciada || 0)];
      itens.forEach(function (el, i) {
        if (valores[i] !== undefined) el.textContent = numero(valores[i]) + '+';
      });
    });
  }

  function carregarDestaques() {
    var wrapper = document.querySelector('.ul-featured-properties-slider .swiper-wrapper');
    var secao = document.querySelector('.ul-featured-properties');
    if (!wrapper) return Promise.resolve();
    return window.QNBApi.get('/destaques').then(function (r) {
      var lista = r.imoveis || [];
      if (!lista.length) {
        if (secao) secao.style.display = 'none';
        return;
      }
      wrapper.innerHTML = lista.map(cardFeatured).join('');
      if (window.ULSwipers && window.ULSwipers.featured) {
        window.ULSwipers.featured.update();
        window.ULSwipers.featured.slideToLoop(0);
      }
    });
  }

  function carregarAba(id, params) {
    var painel = document.getElementById(id);
    if (!painel) return Promise.resolve();
    var row = painel.querySelector('.row.ul-bs-row');
    if (!row) return Promise.resolve();
    params = params || {};
    params.por_pagina = params.por_pagina || 6;
    return window.QNBApi.get('/imoveis' + parametros(params)).then(function (r) {
      var lista = r.imoveis.data || [];
      row.innerHTML = lista.length ? lista.map(cardGrid).join('') : mensagemVazia('Sem imóveis disponíveis nesta categoria.');
    });
  }

  function carregarAbas() {
    return Promise.all([
      carregarAba('tab-rent', { finalidade: 'arrendar' }),
      carregarAba('tab-buy', { finalidade: 'comprar' }),
      carregarAba('tab-sell', {})
    ]);
  }

  function carregarCidades() {
    var grid = document.querySelector('.ul-cities .row');
    if (!grid) return Promise.resolve();
    return window.QNBApi.get('/cidades').then(function (r) {
      var cidades = (r.cidades || []).slice(0, 8);
      if (!cidades.length) {
        grid.style.display = 'none';
        return;
      }
      grid.innerHTML = cidades.map(function (c) {
        return '' +
          '<div class="col">' +
            '<div class="ul-city">' +
              '<div class="img"><a href="projects.html?provincia=' + encodeURIComponent(c.provincia) + '"><img src="assets/img/city-' + (cidades.indexOf(c) + 1) + '.jpg" alt="' + c.provincia + '"></a></div>' +
              '<div class="txt">' +
                '<h3 class="ul-city-title"><a href="projects.html?provincia=' + encodeURIComponent(c.provincia) + '">' + c.provincia + '</a></h3>' +
                '<span class="ul-city-count">' + c.total + ' Imóveis</span>' +
              '</div>' +
            '</div>' +
          '</div>';
      }).join('');
    });
  }

  function carregarSidebar() {
    var wrapper = document.querySelector('.ul-sidebar-slider .swiper-wrapper');
    if (!wrapper) return Promise.resolve();
    return window.QNBApi.get('/imoveis?por_pagina=3').then(function (r) {
      var lista = r.imoveis.data || [];
      wrapper.innerHTML = lista.map(cardSidebar).join('');
      if (window.ULSwipers && window.ULSwipers.sidebar) {
        window.ULSwipers.sidebar.update();
        window.ULSwipers.sidebar.slideToLoop(0);
      }
    }).catch(function () {});
  }

  function ligarFiltroRapido() {
    var form = document.querySelector('.ul-property-filter-search-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var partes = [];
      var local = form.elements.location && form.elements.location.value;
      var tipo = form.elements['property-type'] && form.elements['property-type'].value;
      var preco = form.elements.price && form.elements.price.value;
      if (local) partes.push('provincia=' + encodeURIComponent(local));
      if (tipo) partes.push('tipo=' + encodeURIComponent(tipo));
      if (preco) partes.push('preco_max=' + encodeURIComponent(preco));
      window.location.href = 'projects.html' + (partes.length ? '?' + partes.join('&') : '');
    });
  }

  /* ------------------------- PROJECTS ------------------------- */

  function parametros(objeto) {
    var partes = [];
    Object.keys(objeto || {}).forEach(function (k) {
      if (objeto[k] !== undefined && objeto[k] !== null && objeto[k] !== '') {
        partes.push(encodeURIComponent(k) + '=' + encodeURIComponent(objeto[k]));
      }
    });
    return partes.length ? '?' + partes.join('&') : '';
  }

  function carregarProjects() {
    var grid = document.querySelector('.ul-projects-page-content-wrapper .row.ul-bs-row');
    var form = document.querySelector('.ul-projects-search-filters');
    if (!grid) return Promise.resolve();

    var params = {};
    var busca = new URLSearchParams(window.location.search);
    ['palavra', 'tipo', 'provincia', 'preco_max', 'quartos', 'finalidade', 'ordem'].forEach(function (k) {
      if (busca.get(k)) params[k] = busca.get(k);
    });
    if (busca.get('page')) params.page = busca.get('page');

    function lerFiltros() {
      var p = {};
      if (form.elements.keyword && form.elements.keyword.value) p.palavra = form.elements.keyword.value;
      if (form.elements['property-type'] && form.elements['property-type'].value) p.tipo = form.elements['property-type'].value;
      if (form.elements.location && form.elements.location.value) p.provincia = form.elements.location.value;
      if (form.elements['max-price'] && form.elements['max-price'].value) p.preco_max = form.elements['max-price'].value.replace(/\D/g, '');
      if (form.elements.beds && form.elements.beds.value) p.quartos = form.elements.beds.value;
      return p;
    }

    function preencherFiltros() {
      if (form.elements.keyword && busca.get('palavra')) form.elements.keyword.value = busca.get('palavra');
      if (form.elements['property-type'] && busca.get('tipo')) form.elements['property-type'].value = busca.get('tipo');
      if (form.elements.location && busca.get('provincia')) form.elements.location.value = busca.get('provincia');
      if (form.elements['max-price'] && busca.get('preco_max')) form.elements['max-price'].value = busca.get('preco_max');
      if (form.elements.beds && busca.get('quartos')) form.elements.beds.value = busca.get('quartos');
    }

    function renderizar(resultado) {
      var pagina = resultado.imoveis;
      var lista = pagina.data || [];
      grid.innerHTML = lista.length ? lista.map(cardGrid).join('') : mensagemVazia('Não foram encontrados imóveis com os filtros selecionados.');

      var nav = grid.nextElementSibling;
      if (nav && nav.classList && nav.classList.contains('ul-paginacao')) nav.remove();
      if (pagina.last_page > 1) {
        var el = document.createElement('nav');
        el.className = 'ul-paginacao';
        var base = window.location.pathname;
        var link = function (paginaNumero, rotulo, classe, desativado) {
          var itens = [];
          ['palavra', 'tipo', 'provincia', 'preco_max', 'quartos', 'finalidade', 'ordem'].forEach(function (k) {
            if (busca.get(k)) itens.push(encodeURIComponent(k) + '=' + encodeURIComponent(busca.get(k)));
          });
          if (paginaNumero > 1) itens.push('page=' + paginaNumero);
          return '<a class="' + classe + (desativado ? ' disabled' : '') + '" href="' + base + (itens.length ? '?' + itens.join('&') : '') + '">' + rotulo + '</a>';
        };
        var html = link(pagina.current_page - 1, '‹', 'prev', pagina.current_page <= 1);
        for (var i = 1; i <= pagina.last_page; i++) {
          html += (i === pagina.current_page)
            ? '<span class="active">' + i + '</span>'
            : link(i, String(i), '');
        }
        html += link(pagina.current_page + 1, '›', 'next', pagina.current_page >= pagina.last_page);
        el.innerHTML = html;
        grid.after(el);
      }
    }

    function atualizar() {
      params = Object.assign({}, lerFiltros());
      if (busca.get('page') && !form.elements.keyword) { /* sem ação */ }
      if (params.palavra) busca.set('palavra', params.palavra); else busca.delete('palavra');
      ['tipo', 'provincia', 'preco_max', 'quartos'].forEach(function (k) {
        if (params[k]) busca.set(k, params[k]); else busca.delete(k);
      });
      busca.delete('page');
      window.history.replaceState({}, '', window.location.pathname + busca.toString());
      return window.QNBApi.get('/imoveis' + parametros(Object.assign({}, params, { page: busca.get('page') }))).then(renderizar);
    }

    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        atualizar();
      });
    }

    preencherFiltros();
    return window.QNBApi.get('/imoveis' + parametros(Object.assign({}, params))).then(renderizar);
  }

  /* ------------------------- DETALHE ------------------------- */

  function carregarDetalhe() {
    var params = new URLSearchParams(window.location.search);
    var id = params.get('id');
    if (!id) {
      window.location.href = 'projects.html';
      return Promise.resolve();
    }
    return window.QNBApi.get('/imoveis/' + id).then(function (r) {
      var im = r.imovel;
      var imobiliaria = im.imobiliaria || {};

      var titulo = document.querySelector('.ul-project-details-heading .ul-project-details-title');
      if (titulo) titulo.textContent = im.titulo;

      var local = document.querySelector('.ul-project-details-heading .ul-project-details-location');
      if (local) {
        var icone = local.querySelector('i');
        local.innerHTML = (icone ? icone.outerHTML + ' ' : '') + (im.municipio ? im.municipio + ', ' : '') + im.provincia;
      }

      var preco = document.querySelector('.ul-project-details-price');
      if (preco) {
        preco.innerHTML = '<span class="number">' + precoCompleto(im) + '</span> ' + (im.moeda === 'USD' ? 'Dólar Americano' : 'Kwanza Angolano');
      }

      /* galeria */
      var mainWrapper = document.querySelector('.ul-project-details-img-slider .swiper-wrapper');
      var thumbWrapper = document.querySelector('.ul-project-details-img-slider-thumb .swiper-wrapper');
      if (mainWrapper && im.fotos.length) {
        mainWrapper.innerHTML = im.fotos.map(function (f) {
          return '<div class="swiper-slide"><img src="' + window.QNBApi.urlFoto(f.caminho) + '" alt="' + im.titulo + '"></div>';
        }).join('');
        if (thumbWrapper) {
          thumbWrapper.innerHTML = im.fotos.map(function (f) {
            return '<div class="swiper-slide"><img src="' + window.QNBApi.urlFoto(f.caminho) + '" alt="' + im.titulo + '"></div>';
          }).join('');
        }
        if (window.ULSwipers) {
          if (window.ULSwipers.detailsMain) window.ULSwipers.detailsMain.update();
          if (window.ULSwipers.detailsThumbs) window.ULSwipers.detailsThumbs.update();
        }
      }

      /* descrição */
      var blocoDesc = document.querySelector('.ul-project-details-block p');
      if (blocoDesc) blocoDesc.textContent = im.descricao;

      /* visão geral */
      var overviewRow = document.querySelector('.ul-project-details-overview-infos .row');
      if (overviewRow) {
        var itens = [
          ['flaticon-buildings', 'Nº de Referência', im.referencia],
          ['flaticon-home-tik-mark', 'Tipo', TIPOS[im.tipo] || im.tipo],
          ['flaticon-bed-color', 'Quartos', im.quartos ? String(im.quartos) : '—'],
          ['flaticon-bath', 'Casas de Banho', im.wc ? String(im.wc) : '—'],
          ['flaticon-buildings', 'Área (m²)', im.area ? numero(im.area) : '—'],
          ['flaticon-house-1', 'Finalidade', FINALIDADES[im.finalidade] || im.finalidade],
          ['flaticon-tools', 'Ano de Construção', im.ano_construcao ? String(im.ano_construcao) : '—']
        ];
        (im.amenidades || []).forEach(function (a) {
          itens.push(['flaticon-check-4', a.nome, 'Sim']);
        });
        overviewRow.innerHTML = itens.map(function (it) {
          return '' +
            '<div class="col">' +
              '<div class="ul-project-details-overview-info">' +
                '<div class="icon"><i class="' + it[0] + '"></i></div>' +
                '<div class="txt"><span class="key">' + it[1] + '</span><span class="value">' + it[2] + '</span></div>' +
              '</div>' +
            '</div>';
        }).join('');
      }

      /* vídeo */
      var videoBloco = document.querySelector('.ul-project-details-video');
      if (videoBloco) {
        if (im.video) {
          videoBloco.querySelector('a').setAttribute('href', im.video);
        } else {
          var videoSec = videoBloco.closest('.ul-project-details-block');
          if (videoSec) videoSec.style.display = 'none';
        }
      }

      /* características */
      var features = document.querySelector('.ul-project-details-features');
      if (features) {
        features.innerHTML = (im.amenidades || []).map(function (a) {
          return '<span class="feature"><span class="icon"><i class="flaticon-check-4"></i></span><span class="txt">' + a.nome + '</span></span>';
        }).join('');
      }

      /* mapa */
      var mapa = document.querySelector('.ul-project-details-map iframe');
      if (mapa) {
        var q = (im.municipio || '') + ' ' + im.provincia;
        mapa.src = 'https://www.google.com/maps?q=' + encodeURIComponent(q) + '&output=embed';
      }

      /* sidebar — imobiliária */
      var nomeOwner = document.querySelector('.ul-project-details-listing-owner-name');
      if (nomeOwner) nomeOwner.textContent = imobiliaria.nome || 'QNB-Imobiliária';

      var contactos = document.querySelectorAll('.ul-project-details-listing-owner .contact-infos');
      contactos.forEach(function (c) {
        if (c.querySelector('a')) {
          c.classList.remove('d-none');
          var tel = c.querySelector('a[href^="tel:"]');
          var mail = c.querySelector('a[href^="mailto:"]');
          if (tel && imobiliaria.telefone) {
            tel.setAttribute('href', 'tel:' + imobiliaria.telefone.replace(/[^\d+]/g, ''));
            tel.textContent = imobiliaria.telefone;
          }
          if (mail && imobiliaria.email) {
            mail.setAttribute('href', 'mailto:' + imobiliaria.email);
            mail.textContent = imobiliaria.email;
          }
        }
      });

      var waBtn = document.querySelector('.ul-project-details-wa-btn');
      if (waBtn) {
        var tel = (imobiliaria.telefone || '+244921852727').replace(/[^\d+]/g, '').replace(/^\+/, '');
        var wa = document.createElement('a');
        wa.className = 'ul-btn ul-project-details-wa-btn';
        wa.href = 'https://wa.me/' + tel + '?text=' + encodeURIComponent('Olá, vi o imóvel "' + im.titulo + '" (' + im.referencia + ') e gostaria de mais informações.');
        wa.target = '_blank';
        wa.rel = 'noopener';
        wa.innerHTML = waBtn.innerHTML;
        waBtn.replaceWith(wa);
      }

      ligarFormImovel(im);
    }).catch(function (err) {
      if (err.status === 404) {
        window.location.href = 'projects.html';
      } else {
        window.alert(err.message || 'Erro ao carregar o imóvel.');
      }
    });
  }

  function ligarFormImovel(im) {
    var form = document.querySelector('.ul-project-details-owner-contact-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var nome = (form.elements.name || {}).value || '';
      var telefone = (form.elements.phone || {}).value || '';
      var email = (form.elements.email || {}).value || '';
      var texto = (form.elements.message || {}).value || '';
      if (!nome || !texto) {
        mostrarResultado(form, 'Preencha pelo menos o nome e a mensagem.', 'erro');
        return;
      }
      var btn = form.querySelector('button');
      if (btn) btn.disabled = true;
      window.QNBApi.post('/mensagens', {
        nome: nome,
        contacto: telefone || email,
        texto: texto,
        imovel_id: im.id,
        origem: 'imovel'
      }).then(function () {
        mostrarResultado(form, 'Mensagem enviada com sucesso. A imobiliária entrará em contacto consigo.', 'ok');
        form.reset();
        if (btn) btn.disabled = false;
      }).catch(function (err) {
        mostrarResultado(form, err.message || 'Erro ao enviar a mensagem.', 'erro');
        if (btn) btn.disabled = false;
      });
    });
  }

  /* ------------------------- CONTACTO ------------------------- */

  function ligarFormContacto() {
    var form = document.querySelector('.ul-contact-form-wrapper form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var primeiro = (form.elements['contact-first-name'] || {}).value || '';
      var ultimo = (form.elements['contact-last-name'] || {}).value || '';
      var email = (form.elements['contact-email'] || {}).value || '';
      var telefone = (form.elements['contact-phone'] || {}).value || '';
      var texto = (form.elements['contact-message'] || {}).value || '';
      if (!primeiro || !texto) {
        mostrarResultado(form, 'Preencha pelo menos o nome e a mensagem.', 'erro');
        return;
      }
      var btn = form.querySelector('button');
      if (btn) btn.disabled = true;
      window.QNBApi.post('/mensagens', {
        nome: (primeiro + ' ' + ultimo).trim(),
        contacto: telefone || email,
        texto: texto,
        origem: 'institucional'
      }).then(function () {
        mostrarResultado(form, 'Mensagem enviada com sucesso. A equipa QNB responderá em breve.', 'ok');
        form.reset();
        if (btn) btn.disabled = false;
      }).catch(function (err) {
        mostrarResultado(form, err.message || 'Erro ao enviar a mensagem.', 'erro');
        if (btn) btn.disabled = false;
      });
    });
  }

  function mostrarResultado(form, texto, tipo) {
    var aviso = form.querySelector('.ul-contact-result');
    if (!aviso) {
      aviso = document.createElement('p');
      aviso.className = 'ul-contact-result';
      form.appendChild(aviso);
    }
    aviso.textContent = texto;
    aviso.className = 'ul-contact-result ul-contact-result--' + tipo;
  }

  /* ------------------------- INICIO ------------------------- */

  function inicializar() {
    var pagina = document.body.getAttribute('data-pagina') || '';
    var tarefas = [carregarSidebar()];

    if (pagina === 'index') {
      tarefas.push(carregarEstatisticas(), carregarDestaques(), carregarAbas(), carregarCidades());
      ligarFiltroRapido();
    } else if (pagina === 'projects') {
      tarefas.push(carregarProjects());
    } else if (pagina === 'detalhe') {
      tarefas.push(carregarDetalhe());
    } else if (pagina === 'contacto') {
      ligarFormContacto();
    }

    return Promise.all(tarefas).catch(function () {});
  }

  document.addEventListener('DOMContentLoaded', inicializar);

  return {
    numero: numero,
    precoCompleto: precoCompleto,
    cardGrid: cardGrid,
    cardFeatured: cardFeatured,
    cardSidebar: cardSidebar,
    parametros: parametros
  };
})();