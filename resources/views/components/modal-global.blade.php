<!-- Modal Global -->
<div id="ul-modal-overlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:99999;align-items:center;justify-content:center;">
    <div id="ul-modal-box" style="background:#fff;border-radius:12px;padding:0;max-width:450px;width:90%;box-shadow:0 10px 40px rgba(0,0,0,0.3);overflow:hidden;">
        <div id="ul-modal-header" style="padding:20px 25px 0;display:flex;align-items:center;gap:12px;">
            <span id="ul-modal-icone" style="font-size:24px;"></span>
            <h3 id="ul-modal-titulo" style="margin:0;font-size:18px;font-weight:600;"></h3>
        </div>
        <div style="padding:15px 25px 25px;">
            <p id="ul-modal-mensagem" style="margin:0;color:#555;font-size:14px;line-height:1.6;"></p>
        </div>
        <div id="ul-modal-acoes" style="padding:0 25px 20px;display:flex;gap:10px;justify-content:flex-end;"></div>
    </div>
</div>

<style>
#ul-modal-overlay { transition: opacity 0.2s; }
#ul-modal-box { animation: ulModalIn 0.2s ease-out; }
@keyframes ulModalIn { from { opacity:0; transform:scale(0.95); } to { opacity:1; transform:scale(1); } }
.ul-modal-btn { padding:10px 22px;border-radius:6px;font-size:14px;font-weight:600;border:none;cursor:pointer;transition:all 0.2s; }
.ul-modal-btn:hover { opacity:0.85; }
.ul-modal-btn--cancelar { background:#f1f1f1;color:#555; }
.ul-modal-btn--confirmar { background:#1a5276;color:#fff; }
.ul-modal-btn--perigo { background:#e74c3c;color:#fff; }
.ul-modal-btn--fechar { background:#1a5276;color:#fff; }
</style>

<script>
function ulModal(tipo, titulo, mensagem, acoes) {
    var overlay = document.getElementById('ul-modal-overlay');
    var icone = document.getElementById('ul-modal-icone');
    var header = document.getElementById('ul-modal-header');
    var tituloEl = document.getElementById('ul-modal-titulo');
    var msgEl = document.getElementById('ul-modal-mensagem');
    var acoesEl = document.getElementById('ul-modal-acoes');

    tituloEl.textContent = titulo;
    msgEl.textContent = mensagem;
    acoesEl.innerHTML = '';

    if (tipo === 'perigo') {
        icone.innerHTML = '<i class="flaticon-close" style="color:#e74c3c;"></i>';
        header.style.borderBottom = '2px solid #e74c3c';
    } else if (tipo === 'sucesso') {
        icone.innerHTML = '<i class="flaticon-check" style="color:#10b981;"></i>';
        header.style.borderBottom = '2px solid #10b981';
    } else if (tipo === 'erro') {
        icone.innerHTML = '<i class="flaticon-info" style="color:#e67e22;"></i>';
        header.style.borderBottom = '2px solid #e67e22';
    } else {
        icone.innerHTML = '<i class="flaticon-info" style="color:#1a5276;"></i>';
        header.style.borderBottom = '2px solid #1a5276';
    }

    if (acoes && acoes.length) {
        acoes.forEach(function(acao) {
            var btn = document.createElement('button');
            btn.className = 'ul-modal-btn ' + (acao.cls || '');
            btn.textContent = acao.texto;
            btn.onclick = function() {
                overlay.style.display = 'none';
                if (acao.acao) acao.acao();
            };
            acoesEl.appendChild(btn);
        });
    } else {
        var btn = document.createElement('button');
        btn.className = 'ul-modal-btn ul-modal-btn--fechar';
        btn.textContent = 'Fechar';
        btn.onclick = function() { overlay.style.display = 'none'; };
        acoesEl.appendChild(btn);
    }

    overlay.style.display = 'flex';
}

function modalConfirmar(titulo, mensagem, onConfirmar, textoConfirmar) {
    ulModal('confirmar', titulo, mensagem, [
        { texto: 'Cancelar', cls: 'ul-modal-btn--cancelar' },
        { texto: textoConfirmar || 'Confirmar', cls: 'ul-modal-btn--confirmar', acao: onConfirmar }
    ]);
}

function modalPerigo(titulo, mensagem, onConfirmar, textoConfirmar) {
    ulModal('perigo', titulo, mensagem, [
        { texto: 'Cancelar', cls: 'ul-modal-btn--cancelar' },
        { texto: textoConfirmar || 'Confirmar', cls: 'ul-modal-btn--perigo', acao: onConfirmar }
    ]);
}

function modalSucesso(titulo, mensagem) {
    ulModal('sucesso', titulo, mensagem, []);
}

function modalErro(titulo, mensagem) {
    ulModal('erro', titulo, mensagem, []);
}
</script>
