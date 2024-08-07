<?php

if (!function_exists('printVariable')) {
    function printVariable(mixed $variable, string $alias, bool $addTime = true)
    {
        $time = time();
        $timeFormatted = $addTime ? date('g', $time) . '-' . date('i', $time) . '-' . date('s', $time) : '';
        $debugFile = "_debug  $alias" . '  ' . $timeFormatted . ".txt";
        file_exists($debugFile) ? $current = file_get_contents($debugFile) : $current = NULL;
        $new = print_r($variable, true);
        isset($current) ? $current .= "\r\n" . $new : $current = $new;
        file_put_contents($debugFile, $current);
    }
}
