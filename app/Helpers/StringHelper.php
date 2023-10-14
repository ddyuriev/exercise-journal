<?php

namespace App\Helpers;

class StringHelper
{
    /**
     * @return string
     */
    public static function randomColor(): string
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $queryString
     * @return array
     */
    public static function httpQueryStringParser(?string $queryString): array
    {
        $queryStringParsedArray = [];
        if (!empty($queryString) && !empty($queryStringParsed = parse_url($queryString))) {
            if (!empty($queryStringParsed['query'])) {
                parse_str($queryStringParsed['query'], $queryStringParsedArray);
            }
        }
        if (empty($queryStringParsedArray['page'])) {
            $queryStringParsedArray['page'] = 1;
        }
        return $queryStringParsedArray;
    }
}
