{{--
    Alertas de flash padronizados dos painéis (admin / anunciante).
    Requer o painel.css carregado (classes .ul-painel-aviso--*).
--}}
@if(session('success'))
<div class="ul-painel-aviso ul-painel-aviso--sucesso" style="margin-bottom:20px;">
    <div class="ul-painel-aviso-icone"><i class="flaticon-check"></i></div>
    <div class="ul-painel-aviso-texto"><p>{{ session('success') }}</p></div>
</div>
@endif

@if(session('warning'))
<div class="ul-painel-aviso ul-painel-aviso--aviso" style="margin-bottom:20px;">
    <div class="ul-painel-aviso-icone"><i class="flaticon-info"></i></div>
    <div class="ul-painel-aviso-texto"><p>{{ session('warning') }}</p></div>
</div>
@endif

@if(session('error'))
<div class="ul-painel-aviso ul-painel-aviso--perigo" style="margin-bottom:20px;">
    <div class="ul-painel-aviso-icone"><i class="flaticon-info"></i></div>
    <div class="ul-painel-aviso-texto"><p>{{ session('error') }}</p></div>
</div>
@endif
