<?php

namespace App\Helpers;


class StringHelper
{
    public static function parseQueryString(?string $queryString)
    {
        try {
            $queryStringParsed = parse_url($queryString);
            parse_str($queryStringParsed['query'], $result);
        } catch (\Throwable $exception) {
            return [];
        }

        return $result;
    }
}
