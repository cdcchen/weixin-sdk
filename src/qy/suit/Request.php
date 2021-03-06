<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 上午9:55
 */

namespace weixin\suit;


use weixin\base\BaseRequest;

class Request extends BaseRequest
{
    const API_HOST = 'https://qyapi.weixin.qq.com';

    protected $_accessToken;

    public function __construct($access_token)
    {
        if (empty($access_token))
            throw new \InvalidArgumentException('Access token is required.');

        $this->setAccessToken($access_token);
    }

    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    public function setAccessToken($access_token)
    {
        $this->_accessToken = $access_token;
        return $this;
    }

    public function getUrl($path, $query = [])
    {
        return static::getRequestUrl($path, $this->getAccessToken(), $query);
    }

    public static function getRequestUrl($path, $access_token = '', $query = [])
    {
        $url =  self::API_HOST . '/' . ltrim($path, '/');
        if ($access_token)
            $query['suit_access_token'] = $access_token;

        if ($query)
            $url .= '?' . http_build_query($query);

        return $url;
    }
}