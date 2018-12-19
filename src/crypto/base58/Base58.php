<?php
/**
 * Created by PhpStorm.
 * User: dw
 * Date: 2018/8/9
 * Time: 18:08
 */
namespace src\crypto\base58;

class Base58
{
    private static $alphabet = '123456789AbCDEFGHJKLMNPQRSTuVWXYZaBcdefghijkmnopqrstUvwxyz'; //bumo
    private static $base = 58;
    /**
     * @param $string
     * @return bool|string
     */
    public static function encode($string)
    {
        if (is_string($string) === false) {
            return false;
        }
        if (strlen($string) === 0) {
            return '';
        }
        $bytes = array_values(unpack('C*', $string));
        // var_dump($bytes);exit;
        $decimal = $bytes[0];
        for ($i = 1, $l = count($bytes); $i < $l; $i++) {
            $decimal = bcmul($decimal, 256);
            $decimal = bcadd($decimal, $bytes[$i]);
        }
        $output = '';
        while ($decimal >= self::$base) {
            $div = bcdiv($decimal, self::$base, 0);
            $mod = bcmod($decimal, self::$base);
            $output .= self::$alphabet[$mod];
            $decimal = $div;
        }
        if ($decimal > 0) {
            $output .= self::$alphabet[$decimal];
        }
        $output = strrev($output);
        foreach ($bytes as $byte) {
            if ($byte === 0) {
                $output = self::$alphabet[0] . $output;
                continue;
            }
            break;
        }
        return (string) $output;
    }

    public static function decode($base58)
    {
        if (is_string($base58) === false) {
            return false;
        }
        if (strlen($base58) === 0) {
            return '';
        }
        $indexes = array_flip(str_split(self::$alphabet));
        $chars = str_split($base58);
        foreach ($chars as $char) {
            if (isset($indexes[$char]) === false) {
                return false;
            }
        }
        $decimal = $indexes[$chars[0]];
        for ($i = 1, $l = count($chars); $i < $l; $i++) {
            $decimal = bcmul($decimal, self::$base);
            $decimal = bcadd($decimal, $indexes[$chars[$i]]);
        }
        $output = '';
        while ($decimal > 0) {
            $byte = bcmod($decimal, 256);
            $output = pack('C', $byte) . $output;
            $decimal = bcdiv($decimal, 256, 0);
        }
        foreach ($chars as $char) {
            if ($indexes[$char] === 0) {
                $output = "\x00" . $output;
                continue;
            }
            break;
        }
        return $output;
    }
}
?>