<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/22
 * Time: 下午1:59
 */

namespace weixin\qy;


use phpplus\net\CUrl;

class Menu extends Base
{
    const URL_CREATE = '/cgi-bin/menu/create';
    const URL_DELETE = '/cgi-bin/menu/delete';
    const URL_LIST = '/cgi-bin/menu/get';

    public function create($agent_id, $attributes)
    {
        if (!isset($attributes['button']))
            $attributes = ['button' => $attributes];

        $url = $this->getUrl(self::URL_CREATE, ['agentid' => $agent_id]);

        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));
        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                return true;
            }
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function delete($agent_id)
    {
        $url = $this->getUrl(self::URL_DELETE, ['agentid' => $agent_id]);

        $request = new CUrl();
        $request->post($url);
        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                return true;
            }
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function query($agent_id)
    {
        $url = $this->getUrl(self::URL_LIST, ['agentid' => $agent_id]);

        $request = new CUrl();
        $request->get($url);
        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                return $response;
            }
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }
}