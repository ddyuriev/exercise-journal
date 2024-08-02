<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class StringHelper
{
    public static function randomColor(): string
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }


    public static function httpQueryStringParser(?string $queryString): array
    {
        $queryStringParsedArray = [];
        if (!empty($queryString)) {
            $queryStringParsed = parse_url($queryString);
            if (!empty($queryStringParsed['query'])) {
                parse_str($queryStringParsed['query'], $queryStringParsedArray);
            }
        }
        $queryStringParsedArray['page'] ??= 1;
        return $queryStringParsedArray;
    }

    /**
     * @return string[]
     */
    public static function monthsList(): array
    {
        $months = [
            ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
        ];
        $index = App::getLocale() == 'en' ? 0 : 1;

        return $months[$index];
    }

    public static function monthRange(): array
    {
        return [
            1 => [1 => 'Янв', 2 => 'Фев', 3 => 'Мар', 4 => 'Апр'],
            2 => [1 => 'Май', 2 => 'Июн', 3 => 'Июл', 4 => 'Авг'],
            3 => [1 => 'Сен', 2 => 'Окт', 3 => 'Ноя', 4 => 'Дек']
        ];
    }

}
