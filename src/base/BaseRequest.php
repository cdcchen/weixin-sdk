<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/3/13
 * Time: 21:44
 */

namespace weixin\base;


use phpplus\net\CUrl;


abstract class BaseRequest extends Object
{
    protected static function handleRequest(CUrl $request, callable $success = null, callable $failed = null)
    {
        if ($request->hasError()) {
            if ($failed)
                return call_user_func($failed, $request);
            else
                throw new RequestException($request->getError(), $request->getHttpCode());
        }
        else
            return call_user_func($success, $request);
    }

    protected static function handleResponse(CUrl $request, callable $success = null, callable $failed = null)
    {
        $response = $request->getJsonData();
        if ($response['errcode'] == 0 || !isset($response['errcode'])) {
            return call_user_func($success, $response);
        }
        else {
            if ($failed)
                return call_user_func($failed, $response);
            else
                throw new ResponseException($response['errmsg'], $response['errcode']);
        }
    }

}