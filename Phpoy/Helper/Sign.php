<?php
/**
 * Created by PhpStorm.
 * User: mofan
 * Date: 2016/5/24 0024
 * Time: 14:29
 */

namespace Phpoy\Helper;


class Sign {

    private static function _createSignature($string, $start, $end, $salt) {
        return md5($string . $start . $end . $salt);
    }

    /**
     * @param string $string
     * @param int $start
     * @param int $end
     * @param string $salt
     * @return string
     */
    public static function addSignature($string, $start, $end, $salt) {
        return self::_createSignature($string, $start, $end, $salt) . $start . $end . $string;
    }

    /**
     * @param string $string
     * @param string $salt
     * @return false|string
     */
    public static function removeSignature($string, $salt) {
        if (!isset($string{52})) {
            return false;
        }

        $now = time();
        $signature = substr($string, 0, 32);
        $start = substr($string, 32, 10);
        $end = substr($string, 42, 10);
        $string = substr($string, 52);

        if ($signature != self::_createSignature($string, $start, $end, $salt)) {
            return false;
        }

        if ($start == $end || ($start <= $now && $now <= $end)) {
            return $string;
        } else {
            return false;
        }
    }

}
