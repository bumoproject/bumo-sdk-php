<?php
/**
 * Created by PhpStorm.
 * User: dw
 * Date: 2018/8/10
 * Time: 17:25
 */

namespace src\common;


class Http {
    /**
     * @param $url
     * @param $data
     * @param null $header
     * @param int $post
     * @return mixed
     */
    public static function post($url, $data, $header = null, $post=1) {
        //初始化curl
        $ch = curl_init();
        //参数设置
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $post);
        if($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        if($header) {
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        $result = curl_exec ($ch);

        curl_close($ch);
        return $result;
    }

    /**
     * @param $url
     * @param null $header
     * @return mixed
     */
    public static function get($url, $header = null) {
        $ch = curl_init();
        //参数设置
        curl_setopt ($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        if($header) {
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        $result = curl_exec ($ch);
        curl_close($ch);
        return $result;
    }
}