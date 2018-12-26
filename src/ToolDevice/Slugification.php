<?php

namespace App\ToolDevice;


class Slugification
{
    public function slugify($text)
    {
        $text = strtolower($text);
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);
        $text = trim($text, '-');
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (function_exists('iconv'))
        {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        if (empty($text))
        {
            return 'n-a';
        }

        return $text;
    }
}