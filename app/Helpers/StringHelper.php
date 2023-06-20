<?php

namespace App\Helpers;


class StringHelper
{
    /**
     * @param string|null $queryString
     * @return array|mixed
     */
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

    /**
     * @return string
     */
    public static function randomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
