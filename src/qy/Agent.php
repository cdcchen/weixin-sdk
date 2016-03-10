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

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return true;
            });
        });
    }

    public function all()
    {
        $url = $this->getUrl(self::API_LIST);

        $request = new CUrl();
        $request->get($url);

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response['agentlist'];
            });
        });
    }
}