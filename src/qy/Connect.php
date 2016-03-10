<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/22
 * Time: 下午3:26
 */

namespace weixin\qy;


use phpplus\net\CUrl;
use weixin\qy\base\ApiException;
use weixin\qy\base\RequestException;
use weixin\qy\base\ResponseException;
use weixin\qy\security\PrpCrypt;

class Connect
{
    const ENCODING_AES_KEY_LENGTH = 43;
    const API_ACCESS_TOKEN = '/cgi-bin/gettoken';

    private $_token;
    private $_encodingAesKey;
    private $_corpId;


    /**
     * @param string $token 公众平台上，开发者设置的token
     * @param string $encoding_aes_key 公众平台上，开发者设置的EncodingAESKey
     * @param string $corp_id 公众平台的corpid
     */
    public function __construct($corp_id, $token, $encoding_aes_key)
    {
        $this->_token = $token;
        $this->_encodingAesKey = $encoding_aes_key;
        $this->_corpId = $corp_id;
    }

    /**
     * 验证URL
     *
     * @param string $msg_signature: 签名串，对应URL参数的msg_signature
     * @param string $timestamp: 时间戳，对应URL参数的timestamp
     * @param string $nonce: 随机串，对应URL参数的nonce
     * @param string $echo_str: 随机串，对应URL参数的echostr
     * @return string 成功返回0，失败返回对应的错误码
     */
    public function verifyURL($msg_signature, $timestamp, $nonce, $echo_str)
    {
        if (strlen($this->_encodingAesKey) !== self::ENCODING_AES_KEY_LENGTH)
            return false;

        $pc = new PrpCrypt($this->_encodingAesKey);

        try {
            $signature = $this->getSignature($timestamp, $nonce, $echo_str);
        }
        catch (\Exception $e) {
            return false;
        }

        if ($signature != $msg_signature)
            return false;

        return $pc->decrypt($echo_str, $this->_corpId);
    }

    protected function getSignature($timestamp, $nonce, $echo_str)
    {
        return Base::getSHA1($this->_token, $timestamp, $nonce, $echo_str);
    }

    public function getAccessToken($corp_secret, $only_token = true)
    {
        $params = [
            'corpid' => $this->_corpId,
            'corpsecret' => $corp_secret,
        ];

        $request = new CUrl();
        $request->get(Base::getRequestUrl(self::API_ACCESS_TOKEN), $params);

        if ($request->getErrno() === CURLE_OK) {
            $data = $request->getJsonData();
            static::checkAccessTokenResponse($data);

            return $only_token ? $data['access_token'] : $data;
        }
        else
            throw new RequestException($request->getError(), $request->getHttpCode());
    }

    protected static function checkAccessTokenResponse($data)
    {
        if (isset($data['access_token'])) {
            return true;
        }
        elseif (isset($data['errcode']))
            throw new ApiException($data['errmsg'], $data['errcode']);
        else
            throw new ResponseException('Get access token error.');
    }
}