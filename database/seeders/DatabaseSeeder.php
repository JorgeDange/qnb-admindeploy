<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Amenidade;
use App\Models\CanalContacto;
use App\Models\Depoimento;
use App\Models\Imobiliaria;
use App\Models\ImobiliariaPlano;
use App\Models\Imovel;
use App\Models\Parceiro;
use App\Models\Plano;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Settings — Geral
        Setting::insert([
            ['chave' => 'site_nome', 'valor' => 'QNB-Imobiliária', 'tipo' => 'string', 'grupo' => 'geral', 'descricao' => 'Nome do site'],
            ['chave' => 'site_logo', 'valor' => '/img/logo.png', 'tipo' => 'file', 'grupo' => 'geral', 'descricao' => 'Caminho do logótipo'],
            ['chave' => 'site_lema', 'valor' => 'A sua plataforma de imóveis em Angola', 'tipo' => 'string', 'grupo' => 'geral', 'descricao' => 'Lema do site'],
            ['chave' => 'site_email', 'valor' => 'comercial@qnbangola.com', 'tipo' => 'string', 'grupo' => 'geral', 'descricao' => 'Email de contacto do site'],
            ['chave' => 'site_telefone', 'valor' => '+244 921 852 727', 'tipo' => 'string', 'grupo' => 'geral', 'descricao' => 'Telefone de contacto'],
            ['chave' => 'endereco', 'valor' => 'Luanda, Angola', 'tipo' => 'string', 'grupo' => 'geral', 'descricao' => 'Endereço físico'],
            // Contactos
            ['chave' => 'whatsapp', 'valor' => '+244921852727', 'tipo' => 'string', 'grupo' => 'contactos', 'descricao' => 'Número de WhatsApp'],
            ['chave' => 'facebook', 'valor' => 'https://facebook.com/qnbangola', 'tipo' => 'string', 'grupo' => 'contactos', 'descricao' => 'URL do Facebook'],
            ['chave' => 'instagram', 'valor' => 'https://instagram.com/qnbangola', 'tipo' => 'string', 'grupo' => 'contactos', 'descricao' => 'URL do Instagram'],
            ['chave' => 'linkedin', 'valor' => 'https://linkedin.com/company/qnbangola', 'tipo' => 'string', 'grupo' => 'contactos', 'descricao' => 'URL do LinkedIn'],
            ['chave' => 'youtube', 'valor' => 'https://youtube.com/@qnbangola', 'tipo' => 'string', 'grupo' => 'contactos', 'descricao' => 'URL do YouTube'],
            // Mapa
            ['chave' => 'mapa_lat', 'valor' => '-8.839998', 'tipo' => 'string', 'grupo' => 'mapa', 'descricao' => 'Latitude do mapa'],
            ['chave' => 'mapa_lng', 'valor' => '13.289447', 'tipo' => 'string', 'grupo' => 'mapa', 'descricao' => 'Longitude do mapa'],
            // Listagem
            ['chave' => 'imoveis_por_pagina', 'valor' => '20', 'tipo' => 'integer', 'grupo' => 'listagem', 'descricao' => 'Imóveis por página na listagem'],
            // Moderação
            ['chave' => 'moderacao_automatica', 'valor' => 'false', 'tipo' => 'boolean', 'grupo' => 'moderacao', 'descricao' => 'Aprovar imóveis automaticamente'],
            // Email
            ['chave' => 'email_notificacoes', 'valor' => 'true', 'tipo' => 'boolean', 'grupo' => 'email', 'descricao' => 'Enviar notificações por email'],
            // Segurança
            ['chave' => 'retencao_logs_dias', 'valor' => '365', 'tipo' => 'integer', 'grupo' => 'seguranca', 'descricao' => 'Dias de retenção de logs de atividade'],
        ]);

        // Planos
        $planoBasico = Plano::create([
            'nome' => 'Básico',
            'posts_limite' => 5,
            'dias_validade' => 30,
            'preco' => 15000,
            'moeda' => 'Kz',
        ]);

        $planoProfissional = Plano::create([
            'nome' => 'Profissional',
            'posts_limite' => 25,
            'dias_validade' => 30,
            'preco' => 45000,
            'moeda' => 'Kz',
        ]);

        $planoEmpresarial = Plano::create([
            'nome' => 'Empresarial',
            'posts_limite' => 100,
            'dias_validade' => 30,
            'preco' => 120000,
            'moeda' => 'Kz',
        ]);

        // Imobiliária demo
        $imobiliaria = Imobiliaria::create([
            'nome' => 'QNB Imobiliária Demo',
            'nif' => '5417693240',
            'email' => 'demo@qnbangola.com',
            'telefone' => '+244921852727',
            'provincia' => 'Luanda',
            'municipio' => 'Maianga',
            'password' => Hash::make('demo1234'),
            'estado' => 'aprovada',
            'aprovado_em' => now(),
        ]);

        // Plano da imobiliária
        ImobiliariaPlano::create([
            'imobiliaria_id' => $imobiliaria->id,
            'plano_id' => $planoProfissional->id,
            'posts_usados' => 0,
            'data_inicio' => now()->subDays(5),
            'data_expiracao' => now()->addDays(25),
            'ativo' => true,
        ]);

        // Canais de contacto da imobiliária
        CanalContacto::insert([
            ['imobiliaria_id' => $imobiliaria->id, 'imovel_id' => null, 'tipo' => 'whatsapp', 'valor' => '244921852727'],
            ['imobiliaria_id' => $imobiliaria->id, 'imovel_id' => null, 'tipo' => 'telefone', 'valor' => '+244 921 852 727'],
            ['imobiliaria_id' => $imobiliaria->id, 'imovel_id' => null, 'tipo' => 'email', 'valor' => 'demo@qnbangola.com'],
            ['imobiliaria_id' => $imobiliaria->id, 'imovel_id' => null, 'tipo' => 'facebook', 'valor' => 'https://facebook.com/qnbangola'],
        ]);

        // Amenidades
        $amenidadesData = [
            ['nome' => 'Ar Condicionado', 'icone' => 'flaticon-air-conditioner'],
            ['nome' => 'Piscina', 'icone' => 'flaticon-swimming-pool'],
            ['nome' => 'Garagem', 'icone' => 'flaticon-parking'],
            ['nome' => 'Segurança 24h', 'icone' => 'flaticon-security-guard'],
            ['nome' => 'Elevador', 'icone' => 'flaticon-elevator'],
            ['nome' => 'Varanda', 'icone' => 'flaticon-balcony'],
            ['nome' => 'Cozinha Equipada', 'icone' => 'flaticon-kitchen'],
            ['nome' => 'Área de Lazer', 'icone' => 'flaticon-amenities'],
            ['nome' => 'Churrasqueira', 'icone' => 'flaticon-barbecue'],
            ['nome' => 'Jardim', 'icone' => 'flaticon-garden'],
            ['nome' => 'Wi-Fi', 'icone' => 'flaticon-wifi'],
            ['nome' => 'Smart TV', 'icone' => 'flaticon-tv'],
            ['nome' => 'Lavandaria', 'icone' => 'flaticon-laundry'],
            ['nome' => 'Quintal', 'icone' => 'flaticon-backyard'],
            ['nome' => 'Condomínio Fechado', 'icone' => 'flaticon-gated-community'],
            ['nome' => 'Vista Panorâmica', 'icone' => 'flaticon-panoramic-view'],
        ];

        $amenidades = [];
        foreach ($amenidadesData as $i => $data) {
            $amenidades[] = Amenidade::create(array_merge($data, ['ativo' => true]));
        }

        // Imóveis demo
        $imoveisData = [
            [
                'titulo' => 'Apartamento Moderno na Talatona',
                'tipo' => 'apartamento',
                'finalidade' => 'comprar',
                'preco' => 8500000,
                'moeda' => 'Kz',
                'area' => 120.00,
                'quartos' => 3,
                'wc' => 2,
                'ano_construcao' => 2023,
                'provincia' => 'Luanda',
                'municipio' => 'Talatona',
                'bairro' => 'Kilamba',
                'descricao' => '<p>Excelente apartamento com acabamentos de primeira qualidade. Localizado numa das zonas mais nobres de Luanda, com acesso fácil a supermercados, escolas e hospitais.</p><p>O imóvel dispõe de ampla sala de estar, cozinha moderna, varanda com vista para a cidade e 3 quartos amplos.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => true,
                'visualizacoes' => 245,
                'contactos' => 18,
                'estado_imovel' => 'novo',
                'estacionamento' => 2,
            ],
            [
                'titulo' => 'Vivenda de Luxo em Miramar',
                'tipo' => 'vivenda',
                'finalidade' => 'comprar',
                'preco' => 25000000,
                'moeda' => 'Kz',
                'area' => 350.00,
                'quartos' => 5,
                'wc' => 4,
                'ano_construcao' => 2022,
                'provincia' => 'Luanda',
                'municipio' => 'Miramar',
                'bairro' => 'Costa do Sol',
                'descricao' => '<p>Vivenda de luxo com design contemporâneo. Possui piscina, jardim amplo, churrasqueira e garagem para 3 viaturas.</p><p>Acabamentos premium,.Domótica e segurança inteligente.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => true,
                'visualizacoes' => 512,
                'contactos' => 35,
                'estado_imovel' => 'novo',
                'estacionamento' => 3,
            ],
            [
                'titulo' => 'Apartamento T2 no Ingombota',
                'tipo' => 'apartamento',
                'finalidade' => 'arrendar',
                'preco' => 350000,
                'moeda' => 'Kz',
                'area' => 85.00,
                'quartos' => 2,
                'wc' => 1,
                'ano_construcao' => 2020,
                'provincia' => 'Luanda',
                'municipio' => 'Ingombota',
                'bairro' => 'Rua Major Kanhangulo',
                'descricao' => '<p>Apartamento bem localizado no centro de Luanda. Ideal para profissionais, com fácil acesso ao transporte público e comércio.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => false,
                'visualizacoes' => 189,
                'contactos' => 12,
                'estado_imovel' => 'usado',
                'estacionamento' => 1,
            ],
            [
                'titulo' => 'Loja Comercial no Viana',
                'tipo' => 'loja',
                'finalidade' => 'comprar',
                'preco' => 12000000,
                'moeda' => 'Kz',
                'area' => 200.00,
                'quartos' => 0,
                'wc' => 2,
                'ano_construcao' => 2021,
                'provincia' => 'Luanda',
                'municipio' => 'Viana',
                'bairro' => 'Zona Industrial',
                'descricao' => '<p>Loja comercial com grande visibilidade. Localizada numa das zonas comerciais mais movimentadas de Viana.</p><p>Espaço amplo com possibilidade de adaptação para diversos tipos de negócio.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => false,
                'visualizacoes' => 98,
                'contactos' => 8,
                'estado_imovel' => 'novo',
                'estacionamento' => 5,
            ],
            [
                'titulo' => 'Terreno na Cacuaco',
                'tipo' => 'terreno',
                'finalidade' => 'comprar',
                'preco' => 4500000,
                'moeda' => 'Kz',
                'area' => 500.00,
                'quartos' => null,
                'wc' => null,
                'ano_construcao' => null,
                'provincia' => 'Luanda',
                'municipio' => 'Cacuaco',
                'bairro' => 'Novo DR',
                'descricao' => '<p>Terreno com 500m² em zona em crescimento. Documentação em ordem, pronto para construção.</p><p>Área residencial com todos os serviços básicos disponíveis.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => false,
                'visualizacoes' => 156,
                'contactos' => 22,
                'estado_imovel' => 'novo',
                'estacionamento' => null,
            ],
            [
                'titulo' => 'Escritório no Centro de Luanda',
                'tipo' => 'escritorio',
                'finalidade' => 'arrendar',
                'preco' => 500000,
                'moeda' => 'Kz',
                'area' => 100.00,
                'quartos' => 0,
                'wc' => 1,
                'ano_construcao' => 2019,
                'provincia' => 'Luanda',
                'municipio' => 'Ingombota',
                'bairro' => 'Av. 4 de Fevereiro',
                'descricao' => '<p>Escritório moderno no coração de Luanda. Edifício com segurança, elevador e estacionamento.</p><p>Ideal para empresas de médio porte.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => true,
                'visualizacoes' => 210,
                'contactos' => 15,
                'estado_imovel' => 'usado',
                'estacionamento' => 2,
            ],
            [
                'titulo' => 'Vivenda T3 em Cabo Ledo',
                'tipo' => 'vivenda',
                'finalidade' => 'comprar',
                'preco' => 15000000,
                'moeda' => 'Kz',
                'area' => 250.00,
                'quartos' => 3,
                'wc' => 2,
                'ano_construcao' => 2024,
                'provincia' => 'Luanda',
                'municipio' => 'Mussulo',
                'bairro' => 'Cabo Ledo',
                'descricao' => '<p>Vivenda à beira-mar com vista panorâmica para o oceano. Acabamentos modernos e área de lazer completa.</p><p>Piscina, churrasqueira e jardim tropicais.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => true,
                'visualizacoes' => 678,
                'contactos' => 42,
                'estado_imovel' => 'novo',
                'estacionamento' => 2,
            ],
            [
                'titulo' => 'Apartamento T1 no Sambizanga',
                'tipo' => 'apartamento',
                'finalidade' => 'arrendar',
                'preco' => 150000,
                'moeda' => 'Kz',
                'area' => 55.00,
                'quartos' => 1,
                'wc' => 1,
                'ano_construcao' => 2018,
                'provincia' => 'Luanda',
                'municipio' => 'Sambizanga',
                'bairro' => 'Centralidade',
                'descricao' => '<p>Apartamento econômico e bem localizado. Ideal para estudantes ou profissionais início de carreira.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => false,
                'visualizacoes' => 320,
                'contactos' => 28,
                'estado_imovel' => 'usado',
                'estacionamento' => null,
            ],
            [
                'titulo' => 'Armazém no Viana Industrial',
                'tipo' => 'armazem',
                'finalidade' => 'comprar',
                'preco' => 18000000,
                'moeda' => 'Kz',
                'area' => 800.00,
                'quartos' => null,
                'wc' => 2,
                'ano_construcao' => 2020,
                'provincia' => 'Luanda',
                'municipio' => 'Viana',
                'bairro' => 'Zona Industrial',
                'descricao' => '<p>Armazém com pé direito alto e acesso para camiões. Ideal para operações logísticas ou armazenamento.</p><p>Rampa de acesso, escritório e áreas de serviço.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => false,
                'visualizacoes' => 87,
                'contactos' => 6,
                'estado_imovel' => 'novo',
                'estacionamento' => 10,
            ],
            [
                'titulo' => 'Vivenda em Benguela',
                'tipo' => 'vivenda',
                'finalidade' => 'comprar',
                'preco' => 8000000,
                'moeda' => 'Kz',
                'area' => 200.00,
                'quartos' => 4,
                'wc' => 3,
                'ano_construcao' => 2021,
                'provincia' => 'Benguela',
                'municipio' => 'Benguela',
                'bairro' => 'Catumbela',
                'descricao' => '<p>Vivenda espaçosa na cidade de Benguela. Zona tranquila com boa infraestrutura e acesso à praia.</p>',
                'estado' => 'aprovado',
                'disponibilidade' => 'disponivel',
                'destaque' => false,
                'visualizacoes' => 145,
                'contactos' => 11,
                'estado_imovel' => 'novo',
                'estacionamento' => 2,
            ],
        ];

        foreach ($imoveisData as $i => $data) {
            $data['referencia'] = 'QNB-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
            $imovel = Imovel::create(array_merge($data, ['imobiliaria_id' => $imobiliaria->id]));
            // Associar 3-6 amenidades aleatórias
            $randomAmenidades = collect($amenidades)->random(rand(3, 6))->pluck('id');
            $imovel->amenidades()->attach($randomAmenidades);
        }

        // Depoimentos
        Depoimento::insert([
            ['nome' => 'Ana Fernandes', 'cargo' => 'Empresária', 'texto' => 'A QNB-Imobiliária ajudou-me a encontrar o escritório perfeito para o meu negócio. Profissionalismo e dedicação do início ao fim.', 'estrelas' => 5, 'avatar' => null, 'ativo' => true, 'ordem' => 1],
            ['nome' => 'Carlos Mendes', 'cargo' => 'Engenheiro', 'texto' => 'Comprei a minha vivenda através da plataforma e foi uma experiência excelente. Todo o processo foi transparente e rápido.', 'estrelas' => 5, 'avatar' => null, 'ativo' => true, 'ordem' => 2],
            ['nome' => 'Maria José', 'cargo' => 'Arquiteta', 'texto' => 'Recomendo a QNB-Imobiliária a todos que procuram imóveis em Angola. A variedade de opções e a qualidade do atendimento são incomparáveis.', 'estrelas' => 5, 'avatar' => null, 'ativo' => true, 'ordem' => 3],
            ['nome' => 'Pedro Santos', 'cargo' => 'Investidor', 'texto' => 'Já investi em vários imóveis pela plataforma. A equipa é muito competente e sempre disponível para ajudar.', 'estrelas' => 4, 'avatar' => null, 'ativo' => true, 'ordem' => 4],
        ]);

        // Parceiros
        Parceiro::insert([
            ['nome' => 'Banco BAI', 'logo' => null, 'url' => 'https://bancobai.ao', 'ativo' => true, 'ordem' => 1],
            ['nome' => 'Sonangol', 'logo' => null, 'url' => 'https://sonangol.co.ao', 'ativo' => true, 'ordem' => 2],
            ['nome' => 'Unitel', 'logo' => null, 'url' => 'https://unitel.ao', 'ativo' => true, 'ordem' => 3],
            ['nome' => 'TAAG', 'logo' => null, 'url' => 'https://taag.com', 'ativo' => true, 'ordem' => 4],
            ['nome' => 'Estrela', 'logo' => null, 'url' => 'https://estrela.ao', 'ativo' => true, 'ordem' => 5],
            ['nome' => 'Refriango', 'logo' => null, 'url' => 'https://refriango.ao', 'ativo' => true, 'ordem' => 6],
        ]);

        // Segunda imobiliária para variedade
        $imobiliaria2 = Imobiliaria::create([
            'nome' => 'Imobiliária Luanda Prime',
            'nif' => '5418963210',
            'email' => 'info@luandaprime.ao',
            'telefone' => '+244923456789',
            'provincia' => 'Luanda',
            'municipio' => 'Samba',
            'password' => Hash::make('senha1234'),
            'estado' => 'aprovada',
            'aprovado_em' => now()->subDays(10),
        ]);

        ImobiliariaPlano::create([
            'imobiliaria_id' => $imobiliaria2->id,
            'plano_id' => $planoBasico->id,
            'posts_usados' => 3,
            'data_inicio' => now()->subDays(10),
            'data_expiracao' => now()->addDays(20),
            'ativo' => true,
        ]);

        Imovel::create([
            'imobiliaria_id' => $imobiliaria2->id,
            'referencia' => 'LP-001',
            'titulo' => 'Estúdio Mobilado no Kinaxixi',
            'tipo' => 'apartamento',
            'finalidade' => 'arrendar',
            'preco' => 200000,
            'moeda' => 'Kz',
            'area' => 40.00,
            'quartos' => 1,
            'wc' => 1,
            'ano_construcao' => 2022,
            'provincia' => 'Luanda',
            'municipio' => 'Ingombota',
            'bairro' => 'Kinaxixi',
            'descricao' => '<p>Estúdio mobilado e equipado no centro histórico de Luanda. Perfeito para quem busca conveniência e localização.</p>',
            'estado' => 'aprovado',
            'disponibilidade' => 'disponivel',
            'destaque' => false,
            'visualizacoes' => 78,
            'contactos' => 5,
            'estado_imovel' => 'novo',
            'estacionamento' => null,
        ]);

        // Admin
        Admin::create([
            'nome' => 'Administrador QNB',
            'email' => 'admin@qnbangola.com',
            'password' => bcrypt('admin1234'),
            'role' => 'super_admin',
            'ativo' => true,
        ]);

        // Cliente demo
        $this->call([ClienteDemoSeeder::class]);

        // Configurações da empresa (faturação)
        $this->call([EmpresaConfigSeeder::class]);
    }
}
