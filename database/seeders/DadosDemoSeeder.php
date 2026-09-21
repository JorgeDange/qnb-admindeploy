<?php

namespace Database\Seeders;

use App\Models\CanalContacto;
use App\Models\Depoimento;
use App\Models\Imobiliaria;
use App\Models\ImobiliariaPlano;
use App\Models\Imovel;
use App\Models\Mensagem;
use App\Models\Parceiro;
use App\Models\PedidoAtivacao;
use App\Models\Plano;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DadosDemoSeeder extends Seeder
{
    public function run(): void
    {
        $imobiliaria = Imobiliaria::updateOrCreate(
            ['email' => 'demo@qnbangola.com'],
            [
                'nome' => 'QNB Imobiliária',
                'nif' => '541000000',
                'telefone' => '+244 921 852 727',
                'provincia' => 'Luanda',
                'municipio' => 'Talatona',
                'password' => 'demo1234',
                'estado' => 'aprovada',
                'aprovado_em' => now(),
            ]
        );

        if (!$imobiliaria->plano()->exists()) {
            $plano = Plano::where('nome', 'Profissional')->first();
            ImobiliariaPlano::create([
                'imobiliaria_id' => $imobiliaria->id,
                'plano_id' => $plano->id,
                'posts_usados' => 3,
                'data_inicio' => now()->subDays(5)->toDateString(),
                'data_expiracao' => now()->addDays(25)->toDateString(),
                'ativo' => true,
            ]);
        }

        CanalContacto::updateOrCreate(
            ['imobiliaria_id' => $imobiliaria->id, 'tipo' => 'whatsapp'],
            ['valor' => '+244921852727']
        );
        CanalContacto::updateOrCreate(
            ['imobiliaria_id' => $imobiliaria->id, 'tipo' => 'telefone'],
            ['valor' => '+244 921 852 727']
        );
        CanalContacto::updateOrCreate(
            ['imobiliaria_id' => $imobiliaria->id, 'tipo' => 'email'],
            ['valor' => 'comercial@qnbangola.com']
        );

        $imoveis = [
            [
                'titulo' => 'Apartamento T3 com Vista Mar no Bairro Azul',
                'tipo' => 'apartamento', 'finalidade' => 'arrendar',
                'preco' => 850000, 'moeda' => 'Kz', 'area' => 145, 'quartos' => 3, 'wc' => 3,
                'ano_construcao' => 2022, 'provincia' => 'Luanda', 'municipio' => 'Ingombota',
                'bairro' => 'Bairro Azul', 'endereco' => 'Av. 4 de Fevereiro, n.º 12',
                'descricao' => 'Apartamento T3 com vista mar, cozinha equipada, 2 lugares de estacionamento e segurança 24h. A 5 minutos da Marginal.',
                'estado' => 'aprovado', 'destaque' => true,
            ],
            [
                'titulo' => 'Vivenda T4 Moderna com Piscina',
                'tipo' => 'vivenda', 'finalidade' => 'comprar',
                'preco' => 320000, 'moeda' => 'USD', 'area' => 420, 'quartos' => 4, 'wc' => 4,
                'ano_construcao' => 2023, 'provincia' => 'Luanda', 'municipio' => 'Talatona',
                'bairro' => 'Belas Business', 'endereco' => 'Rua do Golf, Talatona',
                'descricao' => 'Vivenda T4 moderna com piscina privada, jardim, gerador e casa de apoio. Condomínio fechado com segurança 24h.',
                'estado' => 'aprovado', 'destaque' => true,
            ],
            [
                'titulo' => 'Terreno de 1.000 m² na Zona do Gamek',
                'tipo' => 'terreno', 'finalidade' => 'vender',
                'preco' => 95000, 'moeda' => 'USD', 'area' => 1000, 'quartos' => null, 'wc' => null,
                'ano_construcao' => null, 'provincia' => 'Luanda', 'municipio' => 'Viana',
                'bairro' => 'Gamek', 'endereco' => 'Estrada do Gamek',
                'descricao' => 'Terreno plano de 1.000 m², pronto para construção, com documentação em ordem e acesso a estrada principal.',
                'estado' => 'aprovado', 'destaque' => false,
            ],
            [
                'titulo' => 'Loja Comercial no Shopping Belas',
                'tipo' => 'loja', 'finalidade' => 'arrendar',
                'preco' => 450000, 'moeda' => 'Kz', 'area' => 85, 'quartos' => null, 'wc' => 1,
                'ano_construcao' => 2019, 'provincia' => 'Luanda', 'municipio' => 'Talatona',
                'bairro' => 'Shopping Belas', 'endereco' => 'Piso 1, Shopping Belas',
                'descricao' => 'Loja comercial em shopping de referência, com boa circulação e estacionamento. Ideal para retalho ou serviços.',
                'estado' => 'pendente', 'destaque' => false,
            ],
        ];

        foreach ($imoveis as $dados) {
            $imovel = $imobiliaria->imoveis()->firstOrCreate(
                ['titulo' => $dados['titulo']],
                $dados + ['referencia' => $this->referencia()]
            );

            if (!$imovel->fotos()->exists()) {
                foreach (range(1, 5) as $i) {
                    $imovel->fotos()->create([
                        'caminho' => 'demo/imoveis/' . $imovel->id . '-' . $i . '.jpg',
                        'ordem' => $i - 1,
                        'capa' => $i === 1,
                    ]);
                }
            }
        }

        Mensagem::updateOrCreate(
            ['nome' => 'Carlos Mendes', 'texto' => 'Boa tarde. O apartamento T3 do Bairro Azul ainda está disponível para visita?'],
            ['imobiliaria_id' => $imobiliaria->id, 'contacto' => '+244 923 111 222', 'origem' => 'imovel', 'lida' => false]
        );
        Mensagem::updateOrCreate(
            ['nome' => 'Ana Domingos', 'texto' => 'Tenho interesse na vivenda T4. Qual é o valor final e o processo de aquisição?'],
            ['imobiliaria_id' => $imobiliaria->id, 'contacto' => '+244 924 333 444', 'origem' => 'imovel', 'lida' => true]
        );

        $depoimentos = [
            ['nome' => 'João Baptista', 'cargo' => 'Diretor Comercial', 'texto' => 'Anunciar na QNB-Imobiliária aumentou consideravelmente os contactos da nossa empresa. O processo é simples e rápido.', 'estrelas' => 5],
            ['nome' => 'Maria Cordeiro', 'cargo' => 'Gestora Imobiliária', 'texto' => 'Plataforma séria e organizada. Os anúncios são verificados e os clientes chegam qualificados.', 'estrelas' => 5],
            ['nome' => 'Pedro Quissanga', 'cargo' => 'Promotor Imobiliário', 'texto' => 'Excelente visibilidade para os nossos projetos. A equipa da QNB acompanha em todo o processo.', 'estrelas' => 4],
        ];
        foreach ($depoimentos as $d) {
            Depoimento::updateOrCreate(['nome' => $d['nome']], $d + ['ativo' => true, 'ordem' => 0]);
        }

        $parceiros = ['BIC Angola', 'Standard Bank', 'Unitel', 'Angola Cables', 'Atlantic Ventures', 'Keve'];
        foreach ($parceiros as $i => $nome) {
            Parceiro::updateOrCreate(['nome' => $nome], ['ativo' => true, 'ordem' => $i]);
        }

        $settings = [
            'telefone' => '+244 921 852 727',
            'email' => 'comercial@qnbangola.com',
            'endereco' => 'Morro Bento, Rua da Anghotel, Luanda',
            'whatsapp' => '+244921852727',
            'facebook' => 'https://facebook.com/qnbangola',
            'instagram' => 'https://instagram.com/qnbangola',
            'linkedin' => 'https://linkedin.com/in/qnbangola',
            'horario' => 'Segunda a Sexta, 8h às 17h',
        ];
        foreach ($settings as $chave => $valor) {
            Setting::set($chave, $valor);
        }
    }

    private function referencia(): string
    {
        $num = Imovel::count() + 1;
        return '#QNB-' . str_pad((string) $num, 3, '0', STR_PAD_LEFT);
    }
}
