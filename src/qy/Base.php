<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 上午9:55
 */

namespace weixin\qy;


use phpplus\net\CUrl;
use weixin\qy\base\RequestException;
use weixin\qy\base\Response;
use weixin\qy\base\ResponseException;

class Base
{
    const API_HOST = 'https://qyapi.weixin.qq.com';

    protected $_cropID;
    protected $_accessToken;

    public function __construct($access_token, $crop_id = '')
    {
        if (empty($access_token))
            throw new \InvalidArgumentException('Access token is required.');

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
        return $this;
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

    public static function getRequestUrl($path, $access_token = '', $query = [])
    {
        $url =  self::API_HOST . '/' . ltrim($path, '/');
        if ($access_token)
            $query['access_token'] = $access_token;

        if ($query)
            $url .= '?' . http_build_query($query);

        return $url;
    }

    protected static function checkAccessTokenExpire(array $response)
    {
        return $response['errcode'] == 42001;
    }

    protected static function handleRequest(CUrl $request, callable $success = null, callable $failed = null)
    {
        if ($request->hasError()) {
            if ($failed)
                return call_user_func($failed, $request);
            else
                throw new RequestException($request->getError(), $request->getHttpCode());
        }
        else
            return call_user_func($success, $request);
    }

    protected static function handleResponse(CUrl $request, callable $success = null, callable $failed = null)
    {
        $response = $request->getJsonData();
        if ($response['errcode'] == Response::E_SUCCESS) {
            return call_user_func($success, $response);
        }
        else {
            if ($failed)
                return call_user_func($failed, $response);
            else
                throw new ResponseException($response['errmsg'], $response['errcode']);
        }
    }
}