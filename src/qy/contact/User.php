<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 下午1:48
 */

namespace weixin\qy\contact;


use phpplus\net\CUrl;
use weixin\qy\Base;

class User extends Base
{
    const URL_CREATE = '/cgi-bin/user/create';
    const URL_UPDATE = '/cgi-bin/user/update';
    const URL_DELETE = '/cgi-bin/user/delete';
    const URL_BATCH_DELETE = '/cgi-bin/user/batchdelete';
    const URL_INFO = '/cgi-bin/user/get';
    const URL_SIMPLE_LIST = '/cgi-bin/user/simplelist';
    const URL_DETAIL_LIST = '/cgi-bin/user/list';
    const URL_CONVERT_TO_OPENID = '/cgi-bin/user/convert_to_openid';
    const URL_CONVERT_TO_USERID = '/cgi-bin/user/convert_to_userid';
    const URL_INVITE = '/cgi-bin/invite/send';

    public function create(array $attributes, array $extattr = [])
    {
        if ($extattr)
            $attributes['extattr'] = $extattr;

        $url = $this->getUrl(self::URL_CREATE);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return true;
            });
        });
    }

    public function update($user_id, $attributes)
    {
        $attributes['userid'] = $user_id;

        $url = $this->getUrl(self::URL_UPDATE);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return true;
            });
        });
    }

    public function delete($user_id)
    {
        $url = $this->getUrl(self::URL_DELETE);
        $request = new CUrl();
        $request->get($url, ['userid' => $user_id]);

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return true;
            });
        });
    }

    public function batchDelete($users)
    {
        $attributes = ['useridlist' => $users];

        $url = $this->getUrl(self::URL_BATCH_DELETE);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return true;
            });
        });
    }

    public function info($user_id)
    {
        $url = $this->getUrl(self::URL_INFO);
        $request = new CUrl();
        $request->get($url, ['userid' => $user_id]);

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response;
            });
        });

    }

    public function simpleList($department_id, $status = 0, $fetch_child = false)
    {
        $attributes = [
            'department_id' => (int)$department_id,
            'status' => (int)$status,
            'fetch_child' => $fetch_child ? 1 : 0,
        ];

        $url = $this->getUrl(self::URL_SIMPLE_LIST);
        $request = new CUrl();
        $request->get($url, $attributes);

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response['userlist'];
            });
        });
    }

    public function detailList($department_id, $status = 0, $fetch_child = false)
    {
        $attributes = [
            'department_id' => (int)$department_id,
            'status' => (int)$status,
            'fetch_child' => $fetch_child ? 1 : 0,
        ];

        $url = $this->getUrl(self::URL_DETAIL_LIST);
        $request = new CUrl();
        $request->get($url, $attributes);

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response['userlist'];
            });
        });
    }

    public function invite($user_id)
    {
        $attributes = ['userid' => $user_id];

        $url = $this->getUrl(self::URL_INVITE);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response['type'];
            });
        });
    }

    public function userIdToOpenId($user_id, $agent_id = '')
    {
        $attributes = ['userid' => $user_id];
        if ($agent_id)
            $attributes['agent_id'] = $agent_id;

        $url = $this->getUrl(self::URL_CONVERT_TO_OPENID);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return [
                    'openid' => $response['openid'],
                    'appid' => $response['appid'],
                ];
            });
        });
    }

    public function openIdToUserId($open_id)
    {
        $attributes = ['openid' => $open_id];

        $url = $this->getUrl(self::URL_CONVERT_TO_USERID);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response['userid'];
            });
        });
    }
}