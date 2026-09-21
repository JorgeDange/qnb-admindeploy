/* QNB-Imobiliária — qnb-api.js
   Cliente da API /api/v1: fetch com token, tratamento de erros e 401. */

window.QNBApi = (function () {
  'use strict';

  var BASE = 'http://127.0.0.1:8000/api/v1';
  var CHAVE_TOKEN = 'qnb_token';
  var CHAVE_SESSAO = 'qnb_sessao';

  function token() {
    return localStorage.getItem(CHAVE_TOKEN) || '';
  }

  function guardarToken(t) {
    if (t) localStorage.setItem(CHAVE_TOKEN, t);
    else localStorage.removeItem(CHAVE_TOKEN);
  }

  function sessao() {
    try { return JSON.parse(localStorage.getItem(CHAVE_SESSAO) || 'null'); }
    catch (e) { return null; }
  }

  function guardarSessao(s) {
    if (s) localStorage.setItem(CHAVE_SESSAO, JSON.stringify(s));
    else localStorage.removeItem(CHAVE_SESSAO);
  }

  function autenticado() {
    return !!token();
  }

  function sair() {
    guardarToken(null);
    guardarSessao(null);
  }

  function pedir(metodo, caminho, dados, isForm) {
    if (isForm && metodo === 'PUT') {
      dados.append('_method', 'PUT');
      metodo = 'POST';
    }
    var opcoes = {
      method: metodo,
      headers: {
        'Accept': 'application/json'
      }
    };
    if (isForm) {
      opcoes.body = dados;
    } else if (dados !== undefined && dados !== null) {
      opcoes.headers['Content-Type'] = 'application/json';
      opcoes.body = JSON.stringify(dados);
    }
    var t = token();
    if (t) opcoes.headers['Authorization'] = 'Bearer ' + t;

    return fetch(BASE + caminho, opcoes).then(function (resposta) {
      if (resposta.status === 401) {
        sair();
        if (!caminho.startsWith('/login')) {
          window.location.href = 'login.html';
        }
      }
      return resposta.json().catch(function () {
        return { mensagem: 'Resposta inválida do servidor.' };
      }).then(function (corpo) {
        if (!resposta.ok) {
          var erro = new Error(corpo.mensagem || 'Erro do servidor (' + resposta.status + ').');
          erro.status = resposta.status;
          erro.corpo = corpo;
          throw erro;
        }
        return corpo;
      });
    });
  }

  function get(caminho) { return pedir('GET', caminho); }
  function post(caminho, dados, isForm) { return pedir('POST', caminho, dados, isForm); }
  function put(caminho, dados, isForm) { return pedir('PUT', caminho, dados, isForm); }
  function del(caminho) { return pedir('DELETE', caminho); }

  function login(email, password) {
    return post('/login', { email: email, password: password }).then(function (r) {
      guardarToken(r.token);
      guardarSessao(r.imobiliaria);
      return r;
    });
  }

  function loginCliente(email, password) {
    return post('/cliente/login', { email: email, password: password }).then(function (r) {
      guardarToken(r.token);
      var sessao = r.cliente;
      sessao._tipo = 'cliente';
      guardarSessao(sessao);
      return r;
    });
  }

  function registo(dados) {
    return post('/registo', dados);
  }

  function registoCliente(dados) {
    return post('/cliente/registo', dados);
  }

  function logout() {
    return post('/logout').catch(function () {}).then(function () {
      sair();
    });
  }

  function atualizarSessao(dados) {
    var s = sessao() || {};
    Object.keys(dados).forEach(function (k) {
      if (k !== '_tipo' && dados[k] !== undefined) s[k] = dados[k];
    });
    guardarSessao(s);
  }

  function favouritos() {
    return get('/cliente/favoritos');
  }

  function adicionaFavorito(imovel_id) {
    return post('/cliente/favoritos', { imovel_id: imovel_id });
  }

  function removeFavorito(imovel_id) {
    return del('/cliente/favoritos/' + imovel_id);
  }

  function mensagensCliente() {
    return get('/cliente/mensagens');
  }

  function visitasCliente() {
    return get('/cliente/visitas');
  }

  function urlFoto(caminho) {
    if (!caminho) return 'assets/img/project-1.jpg';
    if (/^https?:\/\//.test(caminho)) return caminho;
    return 'http://127.0.0.1:8000/storage/' + caminho.replace(/^\/+/, '');
  }

  return {
    BASE: BASE,
    token: token,
    autenticado: autenticado,
    sessao: sessao,
    guardarSessao: guardarSessao,
    atualizarSessao: atualizarSessao,
    sair: sair,
    get: get,
    post: post,
    put: put,
    del: del,
    login: login,
    loginCliente: loginCliente,
    registo: registo,
    registoCliente: registoCliente,
    logout: logout,
    urlFoto: urlFoto,
    favouritos: favouritos,
    adicionaFavorito: adicionaFavorito,
    removeFavorito: removeFavorito,
    mensagensCliente: mensagensCliente,
    visitasCliente: visitasCliente
  };
})();