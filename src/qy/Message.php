<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 下午10:11
 */

namespace weixin\qy;


use phpplus\net\CUrl;
use weixin\qy\base\RequestException;
use weixin\qy\base\ResponseException;

class Message extends Base
{
    const API_SEND = '/cgi-bin/message/send';
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_VOICE = 'voice';
    const TYPE_VIDEO = 'video';
    const TYPE_FILE = 'file';
    const TYPE_NEWS = 'news';
    const TYPE_MPNEWS = 'mpnews';

    public function text($agentId, $content, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_TEXT;
        $attributes['text']['content'] = $content;

        return $this->send($agentId, $attributes);
    }

    public function image($agentId, $media_id, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_IMAGE;
        $attributes['image']['media_id'] = $media_id;

        return $this->send($agentId, $attributes);
    }

    public function voice($agentId, $media_id, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_VOICE;
        $attributes['voice']['media_id'] = $media_id;

        return $this->send($agentId, $attributes);
    }

    public function video($agentId, $video, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_VIDEO;
        if (is_array($video))
            $attributes['video'] = $video;
        else
            $attributes['video']['media_id'] = $video;

        return $this->send($agentId, $attributes);
    }

    public function file($agentId, $media_id, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_FILE;
        $attributes['file']['media_id'] = $media_id;

        return $this->send($agentId, $attributes);
    }

    public function news($agentId, $articles, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_NEWS;
        $attributes['news']['articles'] = $articles;

        return $this->send($agentId, $attributes);
    }

    public function mpnews($agentId, $articles, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_MPNEWS;
        if (is_array($articles))
            $attributes['mpnews']['articles'] = $articles;
        else
            $attributes['mpnews']['media_id'] = $articles;

        return $this->send($agentId, $attributes);
    }

    public function send($agentId, array $attributes)
    {
//        var_dump($attributes);exit;

        $attributes['agentid'] = $agentId;
        $url = $this->getUrl(self::API_SEND);

        $request = new CUrl();
        $request->post($url, json_encode($attributes, 320));
        if ($request->getErrno() === CURLE_OK) {
            $response = $request->getJsonData();
            if ($response['errcode'] == 0) {
                return static::checkResponse($response);
            }
            else
                throw new ResponseException($response['errmsg'], $response['errcode']);
        }
        else
            throw new RequestException($request->getError(), $request->getHttpCode());
    }

    private static function checkResponse($response)
    {
        $invalid = [
            'invaliduser' => $response['invaliduser'],
            'invalidparty' => $response['invalidparty'],
            'invalidtag' => $response['invalidtag'],
        ];
        $invalid = array_filter($invalid);

        if ($invalid) {
            $invalidMsg= [];
            foreach ($invalid as $key => $value)
                $invalidMsg[] = $key . ': ' . $value;
            $invalidText = join('; ', $invalidMsg);

            throw new ResponseException('Invalid user or party or tag. ' . $invalidText);
        }
        else
            return true;
    }
}