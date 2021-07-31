<?php

declare(strict_types=1);

namespace OpenApiTools\QueryParser;

use OpenApiTools\QueryParser\Exception\InvalidQuery;

interface QueryParser
{

    /**
     * @param string|null $query
     * @return array
     * @throws InvalidQuery
     */
    public function parse(?string $query): array;
}
