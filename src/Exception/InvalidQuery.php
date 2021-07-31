<?php

declare(strict_types=1);

namespace OpenApiTools\QueryParser\Exception;

use InvalidArgumentException;

class InvalidQuery extends InvalidArgumentException
{

    public function __construct(string $query)
    {
        parent::__construct(sprintf('Invalid query string: %s', $query));
    }
}
