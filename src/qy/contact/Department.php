<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 上午10:23
 */

namespace weixin\qy\contact;


use phpplus\net\CUrl;
use weixin\qy\Base;

class Department extends Base
{
    const URL_CREATE = '/cgi-bin/department/create';
    const URL_UPDATE = '/cgi-bin/department/update';
    const URL_DELETE = '/cgi-bin/department/delete';
    const URL_LIST = '/cgi-bin/department/list';


    public function select($id = null)
    {
        $url = $this->getUrl(self::URL_LIST);

        $request = new CUrl();
        $request->get($url, ['id' => $id]);

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['department'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function create($name, $parent_id = 1, $order = 0, $id = 0)
    {
        $params = [
            'name' => $name,
            'parentid' => $parent_id,
        ];

        if ($order > 0) $params['order'] = $order;
        if ($id > 0) $params['id'] = $id;

        $url = $this->getUrl(self::URL_CREATE);

        $request = new CUrl();
        $request->post($url, json_encode($params, 320));

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['id'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function update($id, $name, $parent_id = 1, $order = 0)
    {
        $params = [
            'id' => $id,
            'name' => $name,
            'parentid' => $parent_id,
        ];

        if ($order > 0) $params['order'] = $order;

        $url = $this->getUrl(self::URL_UPDATE);

        $request = new CUrl();
        $request->post($url, json_encode($params, 320));

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
        $params = ['id' => $id];

        $url = $this->getUrl(self::URL_DELETE);

        $request = new CUrl();
        $request->get($url, $params);

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
}