<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/27
 * Time: 下午4:32
 */

namespace weixin\qy;


use phpplus\net\CUrl;

class Server extends Base
{
    const URL_IP_LIST = '/cgi-bin/getcallbackip';

    public function ipList()
    {
        $url = $this->getUrl(self::URL_IP_LIST);

        $request = new CUrl();
        $request->get($url);

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if (isset($response['ip_list']))
                return $response['ip_list'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }
}