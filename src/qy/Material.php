<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 下午9:41
 */

namespace weixin\qy;


use phpplus\net\CUrl;

class Material extends Base
{
    const BATCH_GET_MAX_COUNT = 50;

    const API_UPLOAD = '/cgi-bin/material/add_material';
    const API_DOWNLOAD = '/cgi-bin/material/get';
    const API_ADD_NEWS = '/cgi-bin/material/add_mpnews';
    const API_UPDATE_NEWS = '/cgi-bin/material/update_MPNEWS';
    const API_GET_COUNT = '/cgi-bin/material/get_count';
    const API_LIST = '/cgi-bin/material/batchget';
    const API_DELETE = '/cgi-bin/material/del';

    const TYPE_IMAGE = 'image';
    const TYPE_VOICE = 'voice';
    const TYPE_VIDEO = 'video';
    const TYPE_FILE = 'file';
    const TYPE_NEWS = 'mpnews';

    const SIZE_MIN = 5;
    const SIZE_IMAGE_MAX = 2048000;
    const SIZE_VOICE_MAX = 2048000;
    const SIZE_VIDEO_MAX = 10240000;
    const SIZE_FILE_MAX = 20480000;

    public function uploadFile($filename, $type)
    {
        $url = $this->getUrl(self::API_UPLOAD, ['type' => $type]);
        $media = static::makeMediaParams($filename);

        $request = new CUrl();
        $request->post($url, $media, true);

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0)
                return $response['media_id'];
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    protected static function makeMediaParams($filename)
    {
        $file = new \CURLFile($filename);
        return [
            'upload_file' => $file,
            'filename' => $filename,
            'filelength' => filesize($filename),
            'content-type' => 'image/jpeg',
        ];
    }

    public function downloadFile($media_id, $agent_id)
    {
        $url = $this->getUrl(self::API_DOWNLOAD);

        $request = new CUrl();
        $request->returnHeaders(true)->get($url, ['media_id' => $media_id, 'agentid' => $agent_id]);

        if ($request->getErrno() === CURLE_OK) {
            $contentType = $request->getResponseHeaders('content-type');
            if (stripos($contentType, 'json') === false)
                return $request->getBody();
            else {
                $response = $request->getJsonData();
                if (isset($response['errcode']))
                    throw new \ErrorException($response['errmsg'], $response['errcode']);
                else
                    throw new \ErrorException('Please call downloadNews method for get mpnews.');

            }
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function addNews($agent_id, $news)
    {
        $url = $this->getUrl(self::API_ADD_NEWS, $this->getAccessToken());

        $attributes = [
            'agentid' => $agent_id,
            'mpnews' => $news,
        ];

        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                return $response['media_id'];
            }
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function updateNews($agent_id, $news, $media_id)
    {
        $url = $this->getUrl(self::API_UPDATE_NEWS, $this->getAccessToken());

        $attributes = [
            'agentid' => $agent_id,
            'media_id' => $media_id,
            'mpnews' => $news,
        ];

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

    public function newsInfo($media_id, $agent_id)
    {
        $url = $this->getUrl(self::API_DOWNLOAD);

        $request = new CUrl();
        $request->returnHeaders(true)->get($url, ['media_id' => $media_id, 'agentid' => $agent_id]);

        if ($request->getErrno() === CURLE_OK) {
            $contentType = $request->getResponseHeaders('content-type');
            if (stripos($contentType, 'json') === false)
                throw new \ErrorException('Please call downloadFile method for get files.');
            else {
                $response = $request->getJsonData();
                if (isset($response['mpnews']))
                    return $response['mpnews'];
                else
                    throw new \ErrorException($response['errmsg'], $response['errcode']);
            }
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    public function count($agent_id)
    {
        $url = $this->getUrl(self::API_GET_COUNT);

        $request = new CUrl();
        $request->get($url, ['agentid' => $agent_id]);

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                unset($response['errcode'], $response['errmsg']);
                return $response;
            }
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }

    /**
     * @param int $agent_id
     * @param string $type
     * @param int $count
     * @param int $offset
     * @return array
     * @throws \ErrorException
     */
    public function query($agent_id, $type, $count, $offset = 0)
    {
        $url = $this->getUrl(self::API_LIST);

        $attributes = [
            'agentid' => $agent_id,
            'type' => $type,
            'count' => $count,
            'offset' => $offset,
        ];

        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));

        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                unset($response['errcode'], $response['errmsg']);
                return $response;
            }
            else
                throw new \ErrorException($response['errmsg'], $response['errcode']);
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }


    public function delete($agent_id, $media_id)
    {
        $url = $this->getUrl(self::API_DELETE);

        $attributes = [
            'agentid' => $agent_id,
            'media_id' => $media_id,
        ];

        $request = new CUrl();
        $request->get($url, $attributes);

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
}