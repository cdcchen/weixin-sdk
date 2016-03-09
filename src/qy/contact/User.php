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

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return true;
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function update($user_id, $attributes)
    {
        $attributes['userid'] = $user_id;

        $url = $this->getUrl(self::URL_UPDATE);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return true;
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function delete($user_id)
    {
        $url = $this->getUrl(self::URL_DELETE);
        $request = new CUrl();
        $request->get($url, ['userid' => $user_id]);

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return true;
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function batchDelete($users)
    {
        $attributes = ['useridlist' => $users];

        $url = $this->getUrl(self::URL_BATCH_DELETE);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return true;
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function info($user_id)
    {
        $url = $this->getUrl(self::URL_INFO);
        $request = new CUrl();
        $request->get($url, ['userid' => $user_id]);

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response;
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());

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

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['userlist'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
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

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['userlist'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function invite($user_id)
    {
        $attributes = ['userid' => $user_id];

        $url = $this->getUrl(self::URL_INVITE);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['type'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function userIdToOpenId($user_id, $agent_id = '')
    {
        $attributes = ['userid' => $user_id];
        if ($agent_id)
            $attributes['agent_id'] = $agent_id;

        $url = $this->getUrl(self::URL_CONVERT_TO_OPENID);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return [
                    'openid' => $response['openid'],
                    'appid' => $response['appid'],
                ];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function openIdToUserId($open_id)
    {
        $attributes = ['openid' => $open_id];

        $url = $this->getUrl(self::URL_CONVERT_TO_USERID);
        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['userid'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }
}