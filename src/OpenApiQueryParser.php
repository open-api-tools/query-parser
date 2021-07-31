<?php

declare(strict_types=1);

namespace OpenApiTools\QueryParser;

use OpenApiTools\QueryParser\Exception\InvalidQuery;

final class OpenApiQueryParser implements QueryParser
{

    public function parse(?string $query): array
    {
        if (!$query) {
            return [];
        }
        if (preg_match('/[\x00-\x1f\x7f]/', $query)) {
            throw new InvalidQuery($query);
        }
        $result = [];
        $query = str_replace('+', ' ', $query);
        $params = explode('&', $query);
        foreach ($params as $param) {
            [$name, $value] = array_replace([null, null], explode('=', $param, 2));
            if (!$name) {
                continue;
            }
            $name = urldecode($name);
            if ($value) {
                $value = urldecode($value);
            }
            $this->addToResult($result, $name, $value);
        }
        return $result;
    }

    private function parseVariable(string $variable): array
    {
        if ($variable === '') {
            return [null, []];
        }
        $arr = explode('[', $variable, 2);
        if (!$arr[0]) {
            return [null, []];
        }
        if (count($arr) === 1) {
            return [$variable, []];
        }
        [$name, $subVariable] = $arr;

        $arr = explode(']', $subVariable, 2);
        if (count($arr) === 1) {
            return [$name . '[' . $subVariable, []];
        }
        $subVariable = '[' . $subVariable;

        $path = [];
        while ($subVariable) {
            $arr = explode('[', $subVariable, 2);
            if (count($arr) !== 2 || $arr[0]) {
                return [$name, $path];
            }
            $subVariable = $arr[1];
            $arr = explode(']', $subVariable, 2);
            if (count($arr) !== 2) {
                return [$name, $path];
            }
            if (!$arr[0]) {
                return [$variable, []];
            }
            $path[] = $arr[0];
            $subVariable = $arr[1];
        }
        return [$name, $path];
    }

    private function addToResult(array &$result, string $variable, ?string $value): void
    {
        [$name, $path] = $this->parseVariable($variable);
        if (!$name) {
            return;
        }
        $isNew = false;
        if (!array_key_exists($name, $result)) {
            $isNew = true;
            $result[$name] = null;
        }
        $link = &$result[$name];
        foreach ($path as $key) {
            if (!is_array($link)) {
                $link = [];
            }
            if (!array_key_exists($key, $link)) {
                $link[$key] = null;
                $isNew = true;
            }
            $link = &$link[$key];
        }
        if ($isNew) {
            $link = $value;
        } else {
            if (!is_array($link)) {
                $link = [$link];
            }
            $link[] = $value;
        }
        unset($link);
    }
}
