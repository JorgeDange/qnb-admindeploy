@extends('admin.layouts.admin')

@section('title', 'Configurações — Admin | QNB-Imobiliária')
@section('pageTitle', 'Configurações do Site')

@section('content')
<div class="ul-painel-card">
    <h3 class="ul-painel-card-titulo">Informações do Site</h3>

    <form action="{{ route('admin.settings.salvar') }}" method="POST" class="ul-painel-form">
        @csrf
        <div class="ul-painel-form-seccao">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nome do Site</label>
                        <input type="text" name="site_nome" value="{{ old('site_nome', $settings->get('site_nome')) }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email Principal</label>
                        <input type="email" name="email_principal" value="{{ old('email_principal', $settings->get('email_principal')) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Telefone Principal</label>
                        <input type="text" name="telefone_principal" value="{{ old('telefone_principal', $settings->get('telefone_principal')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $settings->get('whatsapp')) }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" name="endereco" value="{{ old('endereco', $settings->get('endereco')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="ul-painel-form-seccao">
            <h3>Redes Sociais</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Facebook</label>
                        <input type="url" name="facebook" value="{{ old('facebook', $settings->get('facebook')) }}" placeholder="https://facebook.com/...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Instagram</label>
                        <input type="url" name="instagram" value="{{ old('instagram', $settings->get('instagram')) }}" placeholder="https://instagram.com/...">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>LinkedIn</label>
                        <input type="url" name="linkedin" value="{{ old('linkedin', $settings->get('linkedin')) }}" placeholder="https://linkedin.com/...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>YouTube</label>
                        <input type="url" name="youtube" value="{{ old('youtube', $settings->get('youtube')) }}" placeholder="https://youtube.com/...">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="ul-btn">Salvar Configurações</button>
    </form>
</div>
@endsection
