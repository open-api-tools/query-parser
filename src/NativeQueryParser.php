<?php

declare(strict_types=1);

namespace OpenApiTools\QueryParser;

final class NativeQueryParser implements QueryParser
{

    public function parse(?string $query): array
    {
        if (!$query) {
            return [];
        }
        $result = [];
        parse_str($query, $result);
        return $result;
    }
}
