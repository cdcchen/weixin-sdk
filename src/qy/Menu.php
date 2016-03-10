<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/22
 * Time: 下午1:59
 */

namespace weixin\qy;


use phpplus\net\CUrl;
use weixin\qy\base\RequestException;
use weixin\qy\base\ResponseException;

class Menu extends Base
{
    const API_CREATE = '/cgi-bin/menu/create';
    const API_DELETE = '/cgi-bin/menu/delete';
    const API_LIST = '/cgi-bin/menu/get';

    public function create($agent_id, $attributes)
    {
        if (!isset($attributes['button']))
            $attributes = ['button' => $attributes];

        $url = $this->getUrl(self::API_CREATE, ['agentid' => $agent_id]);

        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));
        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                return true;
            }
            else
                throw new ResponseException($response['errmsg'], $response['errcode']);
        }
        else
            throw new RequestException($request->getError(), $request->getHttpCode());
    }

    public function delete($agent_id)
    {
        $url = $this->getUrl(self::API_DELETE, ['agentid' => $agent_id]);

        $request = new CUrl();
        $request->post($url);
        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                return true;
            }
            else
                throw new  ResponseException($response['errmsg'], $response['errcode']);
        }
        else
            throw new RequestException($request->getError(), $request->getHttpCode());
    }

    public function query($agent_id)
    {
        $url = $this->getUrl(self::API_LIST, ['agentid' => $agent_id]);

        $request = new CUrl();
        $request->get($url);
        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                return $response;
            }
            else
                throw new ResponseException($response['errmsg'], $response['errcode']);
        }
        else
            throw new RequestException($request->getError(), $request->getHttpCode());
    }
}