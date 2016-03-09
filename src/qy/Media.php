<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/20
 * Time: 下午3:29
 */

namespace weixin\qy;


use phpplus\net\CUrl;

class Media extends Base
{
    const URL_UPLOAD = '/cgi-bin/media/upload';
    const URL_DOWNLOAD = '/cgi-bin/media/get';

    const TYPE_IMAGE = 'image';
    const TYPE_VOICE = 'voice';
    const TYPE_VIDEO = 'video';
    const TYPE_FILE = 'file';

    const SIZE_MIN = 5;
    const SIZE_IMAGE_MAX = 2048000;
    const SIZE_VOICE_MAX = 2048000;
    const SIZE_VIDEO_MAX = 10240000;
    const SIZE_FILE_MAX = 20480000;

    public function upload($filename, $type)
    {
        $url = $this->getUrl(self::URL_UPLOAD, ['type' => $type]);
        $media = $this->makeMediaParams($filename);

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

    public function makeMediaParams($filename)
    {
        $file = new \CURLFile($filename);
        return [
            'upload_file' => $file,
            'filename' => $filename,
            'filelength' => filesize($filename),
            'content-type' => 'image/jpeg',
        ];
    }

    public function download($media_id)
    {
        $url = $this->getUrl(self::URL_DOWNLOAD);

        $request = new CUrl();
        $request->returnHeaders(true)->get($url, ['media_id' => $media_id]);

        if ($request->getErrno() === CURLE_OK) {
            $contentType = $request->getResponseHeaders('content-type');
            if (stripos($contentType, 'json') !== false) {
                $response = $request->getJsonData();
                throw new \ErrorException($response['errmsg'], $response['errcode']);
            }
            else
                return $request->getBody();
        }
        else
            throw new \ErrorException($request->getError(), $request->getHttpCode());
    }
}