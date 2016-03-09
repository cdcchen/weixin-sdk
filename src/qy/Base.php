<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 上午9:55
 */

namespace weixin\qy;


class Base
{
    const QY_HOST = 'https://qyapi.weixin.qq.com';

    protected $_cropID;
    protected $_accessToken;

    public function __construct($access_token, $crop_id = '')
    {
        if (empty($access_token))
            throw new \InvalidArgumentException('access_token is required.');

        $this->_cropID = $crop_id;
        $this->setAccessToken($access_token);
    }

    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    public function setAccessToken($access_token)
    {
        $this->_accessToken = $access_token;
    }

    public function getUrl($path, $query = [])
    {
        return static::getRequestUrl($path, $this->getAccessToken(), $query);
    }





    public static function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
    {
        $params = [$encrypt_msg, $token, $timestamp, $nonce];
        sort($params, SORT_STRING);
        $str = implode('', $params);

        return sha1($str);
    }

    public static function getRequestUrl($path, $token = '', $query = [])
    {
        $url =  self::QY_HOST . '/' . ltrim($path, '/');
        if ($token)
            $query['access_token'] = $token;

        if ($query) {
            $query = http_build_query($query);
            $url .= '?' . $query;
        }
        return $url;
    }

    protected static function checkAccessTokenExpire(array $response)
    {
        return $response['errcode'] == 42001;
    }
}