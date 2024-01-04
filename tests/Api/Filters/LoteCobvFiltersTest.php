<?php

namespace Eduardokum\LaravelPix\Tests\Api\Filters;

use Eduardokum\LaravelPix\Api\Filters\LoteCobvFilter;
use Eduardokum\LaravelPix\Exceptions\ValidationException;
use Eduardokum\LaravelPix\Tests\TestCase;

class LoteCobvFiltersTest extends TestCase
{
    public function test_it_return_filters_in_the_correct_format()
    {
        $expected = [
            'inicio'                   => $start = now()->subMonth()->toISOString(),
            'fim'                      => $end = now()->subMonth()->toISOString(),
            'paginacao.itensPorPagina' => 2,
            'paginacao.paginaAtual'    => 1,
        ];

        $filters = (new LoteCobvFilter())
            ->startingAt($start)
            ->currentPage(1)
            ->itemsPerPage(2)
            ->endingAt($end);

        $this->assertEquals($expected, $filters->toArray());
    }

    public function test_it_throws_exception_if_start_or_end_are_empty()
    {
        $this->expectException(ValidationException::class);

        (new LoteCobvFilter())->toArray();
    }
}
