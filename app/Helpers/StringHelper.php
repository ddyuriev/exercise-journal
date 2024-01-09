<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

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

    /**
     * @return string[]
     */
    public static function monthsList() :array
    {
        $months = [
            ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь']
        ];
        $index = App::getLocale() == 'en' ? 0 : 1;

        return $months[$index];
    }

    /**
     * @return array
     */
    public static function monthRange(): array
    {
        $monthRange[1][1] = 'Янв';
        $monthRange[1][2] = 'Фев';
        $monthRange[1][3] = 'Мар';
        $monthRange[1][4] = 'Апр';
        $monthRange[2][1] = 'Май';
        $monthRange[2][2] = 'Июн';
        $monthRange[2][3] = 'Июл';
        $monthRange[2][4] = 'Авг';
        $monthRange[3][1] = 'Сен';
        $monthRange[3][2] = 'Окт';
        $monthRange[3][3] = 'Ноя';
        $monthRange[3][4] = 'Дек';

        return $monthRange;
    }

}
