<?php

namespace Junges\Pix\Tests\Api\Resources\Cob;

use Illuminate\Container\Container;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Junges\Pix\Api\Filters\CobFilters;
use Junges\Pix\Pix;
use Junges\Pix\Tests\TestCase;
use Mockery as m;

class CobTest extends TestCase
{
    private array $response;

    public function setUp(): void
    {
        parent::setUp();
        $this->response = [
            "calendario" => [
                "criacao" => "2021-05-11T03:07:23.845Z",
                "expiracao" => 36000
            ],
            "txid" => "OLtfsYyFwSLs3uGma6Ty5ZEKjg",
            "revisao" => 0,
            "loc"=> [
                "id"=> 24,
                "location"=> "pix-h.example.com/v2/265d9f2d3cdc4a1ba0b311ff2812d0dc",
                "tipoCob"=> "cob",
                "criacao"=> "2021-05-11T03:07:23.880Z"
            ],
            "location" => "pix-h.example.com/v2/265d9f2d3cdc4a1ba0b311ff2812d0dc",
            "status"=> "ATIVA",
            "devedor"=> [
                "cpf"=> "54484011042",
                "nome"=> "Fulano de Tal"
            ],
            "valor"=> [
                "original"=> "8.00"
            ],
            "chave"=> $this->randomKey,
            "solicitacaoPagador"=> "Pagamento de serviço"
        ];
    }

    public function tearDown(): void
    {
        m::close();
        Container::setInstance(null);
    }

    public function test_it_can_create_a_cob()
    {
        Http::fake([
            'https://pix.example.com/v2/cob/*' => $this->response
        ]);

        $request = json_decode(
            '{"calendario":{"expiracao":3600},"devedor":{"cnpj":"12345678000195","nome":"Empresa de Serviços SA"},"valor":{"original":"37.00","modalidadeAlteracao":1},"chave":"7d9f0335-8dcc-4054-9bf9-0dbd61d36906","solicitacaoPagador":"Serviço realizado.","infoAdicionais":[{"nome":"Campo 1","valor":"Informação Adicional1 do PSP-Recebedor"},{"nome":"Campo 2","valor":"Informação Adicional2 do PSP-Recebedor"}]}',
            true
        );

        $transactionId = Str::random();

        $cob = Pix::cob();

        $this->assertEquals($this->response, $cob->create($transactionId, $request));
    }

    public function test_it_can_create_a_cob_without_transaction_id()
    {
        Http::fake([
            'https://pix.example.com/v2/cob/*' => $this->response
        ]);

        $request = json_decode(
            '{"calendario":{"expiracao":3600},"devedor":{"cnpj":"12345678000195","nome":"Empresa de Serviços SA"},"valor":{"original":"37.00","modalidadeAlteracao":1},"chave":"7d9f0335-8dcc-4054-9bf9-0dbd61d36906","solicitacaoPagador":"Serviço realizado.","infoAdicionais":[{"nome":"Campo 1","valor":"Informação Adicional1 do PSP-Recebedor"},{"nome":"Campo 2","valor":"Informação Adicional2 do PSP-Recebedor"}]}',
            true
        );

        $cob = Pix::cob();

        $this->assertEquals($this->response, $cob->createWithoutTransactionId($request));
    }

    public function test_it_can_get_a_cob_by_its_transaction_id()
    {
        Http::fake([
            'https://pix.example.com/v2/cob/*' => $this->response
        ]);

        $this->assertEquals($this->response, Pix::cob()->getByTransactionId("OLtfsYyFwSLs3uGma6Ty5ZEKjg"));
    }

    public function test_it_can_get_all_cobs()
    {
        $response = [
            "parametros" => [
                "inicio" => "2021-04-11T03:45:34.192Z",
                "fim" => "2021-06-11T03:45:34.192Z",
                "paginacao" => [
                    "paginaAtual" => 0,
                    "itensPorPagina" => 100,
                    "quantidadeDePaginas" => 0,
                    "quantidadeTotalDeItens" => 0
                ],
                "cpf" => "12345678900",
                "locationPresente" => "false"
            ],
            "cobs" => []
        ];

        Http::fake([
            'https://pix.example.com/v2/cob/*' => $response
        ]);

        $filters = (new CobFilters())
            ->startingAt(now()->subMonth()->toISOString())
            ->endingAt(now()->addMonth()->toISOString());

        $this->assertEquals($response, Pix::cob()->withFilters($filters)->all());
    }

    public function test_it_can_update_a_cob()
    {
        Http::fake([
            'https://pix.example.com/v2/cob/*' => $this->response
        ]);

        $transactionId = Str::random();

        $request = json_decode(
            '{"calendario":{"expiracao":3600},"devedor":{"cnpj":"12345678000195","nome":"Empresa de Serviços SA"},"valor":{"original":"37.00","modalidadeAlteracao":1},"chave":"7d9f0335-8dcc-4054-9bf9-0dbd61d36906","solicitacaoPagador":"Serviço realizado.","infoAdicionais":[{"nome":"Campo 1","valor":"Informação Adicional1 do PSP-Recebedor"},{"nome":"Campo 2","valor":"Informação Adicional2 do PSP-Recebedor"}]}',
            true
        );

        $this->assertEquals($this->response, Pix::cob()->updateByTransactionId($transactionId, $request));
    }

    public function test_it_apply_filters_to_the_query()
    {
        $response = [
            "parametros" => [
                "inicio" => "2021-04-11T03:45:34.192Z",
                "fim" => "2021-06-11T03:45:34.192Z",
                "paginacao" => [
                    "paginaAtual" => 0,
                    "itensPorPagina" => 100,
                    "quantidadeDePaginas" => 0,
                    "quantidadeTotalDeItens" => 0
                ],
                "cpf" => "12345678900",
                "locationPresente" => "false"
            ],
            "cobs" => []
        ];

        Http::fake([
            'https://pix.example.com/v2/cob/*' => $response
        ]);

        $start = now()->subMonth()->toISOString();
        $end = now()->addMonth()->toIsoString();

        $filters = (new CobFilters())
            ->startingAt($start)
            ->endingAt($end);

        Pix::cob()->withFilters($filters)->all();

        Http::assertSent(function(Request $request) use ($start, $end) {
            return $request->data() === ['inicio' => $start, 'fim' => $end]
                || Str::contains($request->url(), http_build_query([
                    'inicio' => $start,
                    'fim' => $end,
                ]));
        });

        $cpf = '19220677091';
        $status = 'ATIVA';

        $filters->cpf($cpf)
            ->withStatus($status);

        Pix::cob()->withFilters($filters)->all();

        Http::assertSent(function(Request $request) use ($start, $end, $status, $cpf) {
            return $request->data() === [
                    'inicio' => $start,
                    'fim' => $end,
                    'cpf' => $cpf,
                    'status' => $status
                ]
                || Str::contains($request->url(), http_build_query([
                    'inicio' => $start,
                    'fim' => $end,
                    'cpf' => $cpf,
                    'status' => $status
                ]));
        });
    }
}