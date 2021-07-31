# open-api-tools/query-parser

the standard PHP function `parse_str()` function replaces the same parameter names 
in the query string. To get an array, you need to add `[]` to the parameter name, 
which is not in accordance with the 
[Open-API specification](https://swagger.io/docs/specification/serialization/#query).

This library solves the indicated problem.

```php

$parser = new \OpenApiTools\QueryParser\OpenApiQueryParser();
$parser->parse('id=1&id=2&id=3');

// returns
[
  'id' => [
    '1',
    '2',
    '3',
  ],
];

```