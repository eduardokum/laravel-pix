<?php

namespace Eduardokum\LaravelPix\Tests\Api\Filters;

use Illuminate\Support\Str;
use Eduardokum\LaravelPix\Api\Filters\ReceivedPixFilters;
use Eduardokum\LaravelPix\Exceptions\ValidationException;
use Eduardokum\LaravelPix\Tests\TestCase;

class ReceivedPixFiltersTest extends TestCase
{
    public function test_it_return_filters_in_the_correct_format()
    {
        $expected = [
            'inicio'                   => $start = now()->subMonth()->toISOString(),
            'fim'                      => $end = now()->subMonth()->toISOString(),
            'txIdPresente'             => 'false',
            'cpf'                      => '12345678900',
            'paginacao.itensPorPagina' => 2,
            'paginacao.paginaAtual'    => 1,
            'txid'                     => $txid = Str::random(),
        ];

        $filters = (new ReceivedPixFilters())
            ->startingAt($start)
            ->withoutTransactionIdPresent()
            ->currentPage(1)
            ->transactionId($txid)
            ->itemsPerPage(2)
            ->cpf('12345678900')
            ->endingAt($end);

        $this->assertEquals($expected, $filters->toArray());
    }

    public function test_it_throws_exception_if_start_or_end_are_empty()
    {
        $this->expectException(ValidationException::class);

        (new ReceivedPixFilters())->toArray();
    }
}
