<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 下午9:48
 */

namespace weixin\qy;


use phpplus\net\CUrl;

class Agent extends Base
{
    const API_INFO = '/cgi-bin/agent/get';
    const API_UPDATE = '/cgi-bin/agent/set';
    const API_LIST = '/cgi-bin/agent/list';

    public function info($agent_id)
    {
        $url = $this->getUrl(self::API_INFO);

        $request = new CUrl();
        $request->get($url, ['agentid' => $agent_id]);

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response;
            });
        });
    }

    public function update($agent_id, $attributes)
    {
        $attributes['agentid'] = $agent_id;

        $url = $this->getUrl(self::API_UPDATE);

        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        static::handleRequest($request, function(CUrl $request){
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return true;
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        });
    }

    public function all()
    {
        $url = $this->getUrl(self::API_LIST);

        $request = new CUrl();
        $request->get($url);

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['agentlist'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }
}