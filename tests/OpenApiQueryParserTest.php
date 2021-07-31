<?php

declare(strict_types=1);

namespace OpenApiTools\Tests\QueryParser;

use OpenApiTools\QueryParser\Exception\InvalidQuery;
use OpenApiTools\QueryParser\OpenApiQueryParser;
use OpenApiTools\QueryParser\QueryParser;
use PHPUnit\Framework\TestCase;

class OpenApiQueryParserTest extends TestCase
{

    /**
     * @param string $query
     * @dataProvider dataSetInvalidQuery
     */
    public function testInvalidQuery(string $query)
    {
        $this->expectException(InvalidQuery::class);
        $this->createParser()->parse($query);
    }

    /**
     * @param string|null $query
     * @param array $expected
     * @dataProvider dataSetSimpleString
     */
    public function testParseQuery(?string $query, array $expected)
    {
        $parsed = $this->createParser()->parse($query);
        self::assertSame($expected, $parsed);
    }

    public function dataSetSimpleString(): array
    {
        return [
            [
                'query' => null,
                'expected' => [],
            ],
            [
                'query' => '',
                'expected' => [],
            ],
            [
                'query' => ' ',
                'expected' => [
                    ' ' => null,
                ],
            ],
            [
                'query' => '%25car=%25car',
                'expected' => ['%car' => '%car'],
            ],
            [
                'query' => '&&',
                'expected' => [],
            ],
            [
                'query' => 'test_obj[1]=foo&test%20obj[2]=bar&test.obj[3][deep]=baz',
                'expected' => [
                    'test_obj' => ['1' => 'foo'],
                    'test obj' => ['2' => 'bar'],
                    'test.obj' => ['3' => ['deep' => 'baz']],
                ],
            ],
            [
                'query' => 'obj[one]=foo&obj[two][three]=bar',
                'expected' => [
                    'obj' => ['one' => 'foo', 'two' => ['three' => 'bar']],
                ],
            ],
            [
                'query' => 'foo=%46&bar=%r&baz=%z',
                'expected' => [
                    'foo' => 'F',
                    'bar' => '%r',
                    'baz' => '%z',
                ],
            ],
            [
                'query' => 'foo&bar=&baz=bar&fo.o',
                'expected' => [
                    'foo' => null,
                    'bar' => '',
                    'baz' => 'bar',
                    'fo.o' => null,
                ],
            ],
            [
                'query' => 'foo&foo=&foo=bar',
                'expected' => [
                    'foo' => [null, '', 'bar'],
                ],
            ],
            [
                'query' => 'foo=bar&foo=baz',
                'expected' => [
                    'foo' => ['bar', 'baz'],
                ],
            ],
            [
                'query' => 'foo[]=bar&foo[]=baz',
                'expected' => [
                    'foo[]' => ['bar', 'baz'],
                ],
            ],
            [
                'query' => 'arr[1=foo&arr[4][2=bar',
                'expected' => [
                    'arr[1' => 'foo',
                    'arr' => ['4' => 'bar'],
                ],
            ],
            [
                'query' => 'arr1]=foo&arr[4]2]=bar',
                'expected' => [
                    'arr1]' => 'foo',
                    'arr' => ['4' => 'bar'],
                ],
            ],
            [
                'query' => 'arr[one=foo&arr[4][two=bar',
                'expected' => [
                    'arr[one' => 'foo',
                    'arr' => ['4' => 'bar'],
                ],
            ],
            [
                'query' => 'arr[][]=one',
                'expected' => [
                    'arr[][]' => 'one',
                ],
            ],
            [
                'query' => 'arr[one][][two]=foo',
                'expected' => [
                    'arr[one][][two]' => 'foo',
                ],
            ],
            [
                'query' => 'foo=one&+foo+=two&foo+=three&+foo=four',
                'expected' => [
                    'foo' => 'one',
                    ' foo ' => 'two',
                    'foo ' => 'three',
                    ' foo' => 'four',
                ],
            ],
        ];
    }

    public function dataSetInvalidQuery(): iterable
    {
        return [
            ["foo\x00=1"],
            ["foo=\x01"],
            ["foo=yes\x00"],
        ];
    }

    private function createParser(): QueryParser
    {
        return new OpenApiQueryParser();
    }
}
