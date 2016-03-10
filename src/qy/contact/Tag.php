<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 下午2:57
 */

namespace weixin\qy\contact;


use phpplus\net\CUrl;
use weixin\qy\Base;
use weixin\qy\base\ResponseException;

class Tag extends Base
{
    const URL_CREATE = '/cgi-bin/tag/create';
    const URL_UPDATE = '/cgi-bin/tag/update';
    const URL_DELETE = '/cgi-bin/tag/delete';
    const URL_LIST = '/cgi-bin/tag/list';
    const URL_GET_USERS = '/cgi-bin/tag/get';
    const URL_ADD_USERS = '/cgi-bin/tag/addtagusers';
    const URL_DELETE_USERS = '/cgi-bin/tag/deltagusers';

    public function create($name, $id = 0)
    {
        $attributes = ['tagname' => $name];
        if ($id > 0) $attributes['tagid'] = $id;

        $url = $this->getUrl(self::URL_CREATE);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response['tagid'];
            });
        });
    }

    public function update($id, $name)
    {
        $attributes = [
            'tagid' => $id,
            'tagname' => $name,
        ];

        $url = $this->getUrl(self::URL_UPDATE);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return true;
            });
        });
    }

    public function delete($id)
    {
        $url = $this->getUrl(self::URL_DELETE);
        $request = new CUrl();
        $request->get($url, ['tagid' => $id]);

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return true;
            });
        });
    }

    public function getUsers($id)
    {
        $url = $this->getUrl(self::URL_GET_USERS);
        $request = new CUrl();
        $request->get($url, ['tagid' => $id]);

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response['userlist'];
            });
        });
    }

    public function addUsers($id, array $user_list = [], array $party_list = [])
    {
        if (empty($user_list) && empty($party_list))
            throw new \ErrorException('userlist and partylist can\'t at the same time is empty.');

        $attributes = [
            'tagid' => $id,
            'userlist' => $user_list,
            'partylist' => $party_list,
        ];

        $url = $this->getUrl(self::URL_ADD_USERS);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                if ($response['invalidlist'] || $response['invalidparty'])
                    throw new ResponseException($response['errmsg'], $response['invalidlist'] . $response['invalidparty']);
                else
                    return true;
            });
        });
    }

    public function deleteUsers($id, array $user_list = [], array $party_list = [])
    {
        if (empty($user_list) && empty($party_list))
            throw new \ErrorException('userlist and partylist can\'t at the same time is empty.');

        $attributes = [
            'tagid' => $id,
            'userlist' => $user_list,
            'partylist' => $party_list,
        ];

        $url = $this->getUrl(self::URL_DELETE_USERS);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                $invalid = [
                    'invalidlist' => $response['invalidlist'],
                    'invalidparty' => $response['invalidparty'],
                ];
                $invalid = array_filter($invalid);

                if ($invalid) {
                    $invalidText = join('；', $invalid);
                    throw new ResponseException($response['errmsg'] . $invalidText);
                }
                else
                    return true;
            });
        });
    }

    public function all()
    {
        $url = $this->getUrl(self::URL_LIST);
        $request = new CUrl();
        $request->get($url);

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return $response['taglist'];
            });
        });
    }
}