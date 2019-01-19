<?php

namespace BassKey\Components\Runner;


class KeySearch
{
    public static function getContentWhereKey(array $array, $key, $arrayTmp = array()): array
    {
        foreach($array as $keyName => $value)
        {
            if($keyName == $key)
            {
                array_push($arrayTmp, $value);
            }
            else if (is_array($value))
            {
                self::getContentWhereKey($value, $key);
            }
        }

        return $arrayTmp;
    }
}
