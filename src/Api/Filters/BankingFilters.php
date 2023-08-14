<?php

namespace Junges\Pix\Api\Filters;

use Junges\Pix\Api\Contracts\ApplyApiFilters;
use Junges\Pix\Exceptions\ValidationException;

class BankingFilters implements ApplyApiFilters
{
    const EVENT_DATE_START = 'event_date_start';
    const EVENT_DATE_END = 'event_date_end';
    const PAGINATION_LIMIT = 'page_limit';
    const PAGINATION_OFFSET = 'page_offset';
    const SORT_BY = 'sort_by';
    const SORT_TYPE = 'sort_type';
    const CUT_EDGE_TYPE = 'cut_edge_type';

    private string $start;
    private string $end;
    private string $sortBy;
    private string $sortType;
    private string $cutEdgeType;
    private int $pageLimit;
    private int $pageOffset;

    public function startingAt(string $start): BankingFilters
    {
        $this->start = $start;

        return $this;
    }

    public function endingAt(string $end): BankingFilters
    {
        $this->end = $end;

        return $this;
    }

    public function itemsPerPage(int $itemsPerPage): BankingFilters
    {
        $this->pageLimit = $itemsPerPage;

        return $this;
    }

    public function currentPage(int $pageOffset): BankingFilters
    {
        $this->currentPage = $pageOffset;

        return $this;
    }

    public function sortBy(string $sortBy = 'EVENT_DATE'): BankingFilters
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    public function sortAsc(): BankingFilters
    {
        $this->sortType = 'ASC';

        return $this;
    }

    public function sortDesc(): BankingFilters
    {
        $this->sortType = 'DESC';

        return $this;
    }

    public function cutBeginOfDay(): BankingFilters
    {
        $this->cutEdgeType = 'BEGIN_OF_DAY';

        return $this;
    }

    public function cutEndOfDay(): BankingFilters
    {
        $this->cutEdgeType = 'END_OF_DAY';

        return $this;
    }

    /**
     * @throws ValidationException
     *
     * @return array
     */
    public function toArray(): array
    {
        if (empty($this->start) || empty($this->end)) {
            throw ValidationException::invalidStartAndEndFields();
        }

        $filters = [
            self::EVENT_DATE_START => $this->start,
            self::EVENT_DATE_END   => $this->end,
        ];

        if (!empty($this->sortBy)) {
            $filters[self::SORT_BY] = $this->sortBy;
        }

        if (!empty($this->sortType)) {
            $filters[self::SORT_TYPE] = $this->sortType;
        }

        if (!empty($this->cutEdgeType)) {
            $filters[self::CUT_EDGE_TYPE] = $this->cutEdgeType;
        }

        if (!empty($this->pageLimit)) {
            $filters[self::PAGINATION_LIMIT] = $this->pageLimit;
        }

        if (!empty($this->pageOffset)) {
            $filters[self::PAGINATION_OFFSET] = $this->pageOffset;
        }

        return $filters;
    }
}
