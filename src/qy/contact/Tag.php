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

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['tagid'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
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

    public function delete($id)
    {
        $url = $this->getUrl(self::URL_DELETE);
        $request = new CUrl();
        $request->get($url, ['tagid' => $id]);

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

    public function getUsers($id)
    {
        $url = $this->getUrl(self::URL_GET_USERS);
        $request = new CUrl();
        $request->get($url, ['tagid' => $id]);

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

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                if ($response['invalidlist'] || $response['invalidparty'])
                    throw new \ErrorException($response['errmsg'], $response['invalidlist'] . $response['invalidparty']);
                else
                    return true;
            }
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
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

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                $invalid = [
                    'invalidlist' => $response['invalidlist'],
                    'invalidparty' => $response['invalidparty'],
                ];
                $invalid = array_filter($invalid);

                if ($invalid) {
                    $invalidText = join('；', $invalid);
                    throw new \ErrorException($response['errmsg'] . $invalidText);
                }
                else
                    return true;
            }
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function all()
    {
        $url = $this->getUrl(self::URL_LIST);
        $request = new CUrl();
        $request->get($url);

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['taglist'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }
}