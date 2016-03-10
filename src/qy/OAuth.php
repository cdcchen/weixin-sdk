<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/22
 * Time: 下午2:26
 */

namespace weixin\qy;


use phpplus\net\CUrl;
use weixin\qy\base\RequestException;
use weixin\qy\base\ResponseException;

class OAuth extends Base
{
    CONST URL_AUTHORIZE = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=%s#wechat_redirect';
    const API_INFO = '/cgi-bin/user/getuserinfo';

    public static function buildAuthorizeUrl($app_id, $redirect_uri, $state = '')
    {
        return sprintf(self::URL_AUTHORIZE, $app_id, urlencode($redirect_uri), $state);
    }

    public function getUserInfo($code)
    {
        $url = $this->getUrl(self::API_INFO, ['code' => $code]);

        $request = new CUrl();
        $request->get($url);
        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if (isset($response['errcode'])) {
                throw new ResponseException($response['errmsg'], $response['errcode']);
            }
            else
                return $response;
        }
        else
            throw new RequestException($request->getError(), $request->getHttpCode());
    }
}