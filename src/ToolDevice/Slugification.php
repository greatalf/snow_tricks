<?php

namespace App\ToolDevice;


class Slugification
{
    public function slugify($str)
    {
        $str = strtolower($str);
        $str = preg_replace('#[^\\pL\d]+#u', '-', $str);
        $str = trim($str, '-');
        $str = preg_replace('#[^-\w]+#', '', $str);

        if (function_exists('iconv'))
        {
            $str = iconv('utf-8', 'us-ascii//TRANSLIT', $str);
        }

        if (empty($str))
        {
            return 'n-a';
        }

        return $str;
    }
}