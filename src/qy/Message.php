<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/21
 * Time: 下午10:11
 */

namespace weixin\qy;


use phpplus\net\CUrl;
use weixin\qy\base\ResponseException;

class Message extends Base
{
    const API_SEND = '/cgi-bin/message/send';

    const MAX_USER_COUNT = 1000;
    const MAX_PARTY_COUNT = 100;

    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_VOICE = 'voice';
    const TYPE_VIDEO = 'video';
    const TYPE_FILE = 'file';
    const TYPE_NEWS = 'news';
    const TYPE_MPNEWS = 'mpnews';

    protected $_attributes = [];

    public function send($agentId, array $attributes)
    {
        $attributes = array_merge($this->_attributes, $attributes);
        $attributes['agentid'] = $agentId;

        $request = new CUrl();
        $url = $this->getUrl(self::API_SEND);
        $request->post($url, json_encode($attributes, 320));

        return static::handleRequest($request, function(CUrl $request){
            return static::handleResponse($request, function($response){
                return static::checkResponse($response);
            });
        });
    }



    ############## send *(text|image|voice|video|file|news|mpnews) shortcut methods ############

    public function sendText($agentId, $content, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_TEXT;
        $attributes['text']['content'] = $content;

        return $this->send($agentId, $attributes);
    }

    public function sendImage($agentId, $media_id, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_IMAGE;
        $attributes['image']['media_id'] = $media_id;

        return $this->send($agentId, $attributes);
    }

    public function sendVoice($agentId, $media_id, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_VOICE;
        $attributes['voice']['media_id'] = $media_id;

        return $this->send($agentId, $attributes);
    }

    public function sendVideo($agentId, $video, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_VIDEO;
        if (is_array($video))
            $attributes['video'] = $video;
        else
            $attributes['video']['media_id'] = $video;

        return $this->send($agentId, $attributes);
    }

    public function sendFile($agentId, $media_id, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_FILE;
        $attributes['file']['media_id'] = $media_id;

        return $this->send($agentId, $attributes);
    }

    public function sendNews($agentId, $articles, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_NEWS;
        $attributes['news']['articles'] = $articles;

        return $this->send($agentId, $attributes);
    }

    public function sendMPNews($agentId, $articles, array $attributes)
    {
        $attributes['msgtype'] = self::TYPE_MPNEWS;
        if (is_array($articles))
            $attributes['mpnews']['articles'] = $articles;
        else
            $attributes['mpnews']['media_id'] = $articles;

        return $this->send($agentId, $attributes);
    }



    ########################### send to (user|party|tag) shortcut methods ##############################

    public function sendToAll($agent_id, array $attributes)
    {
        $attributes['touser'] = '@all';
        return $this->send($agent_id, $attributes);
    }

    public function sendToUser($agent_id, $to_user, array $attributes)
    {
        $to_user = (array)$to_user;
        if (count($to_user) > self::MAX_USER_COUNT)
            throw new \InvalidArgumentException('The number of $to_user should not exceed ' . self::MAX_USER_COUNT);

        $attributes['touser'] = join('|', $to_user);
        return $this->send($agent_id, $attributes);
    }

    public function sendToParty($agent_id, $to_party, array $attributes)
    {
        $to_party = (array)$to_party;
        if (count($to_party) > self::MAX_USER_COUNT)
            throw new \InvalidArgumentException('The number of $to_user should not exceed ' . self::MAX_PARTY_COUNT);

        $attributes['toparty'] = join('|', $to_party);
        return $this->send($agent_id, $attributes);
    }

    public function sendToTag($agent_id, $to_tag, array $attributes)
    {
        $attributes['totag'] = join('|', (array)$to_tag);
        return $this->send($agent_id, $attributes);
    }


    ############################## set attributes ##############################

    public function setToUser($value)
    {
        return $this->setAttribute('touser', $value);
    }

    public function setToParty($value)
    {
        return $this->setAttribute('toparty', $value);
    }

    public function setToTag($value)
    {
        return $this->setAttribute('totag', $value);
    }

    public function setSafe($flag)
    {
        return $this->setAttribute('safe', $flag ? 1 : 0);
    }

    protected function setAttribute($attribute, $value)
    {
        $this->_attributes[$attribute] = $value;
        return $this;
    }


    /**
     * Check response is valid
     *
     * @param $response
     * @return bool
     * @throws ResponseException
     */
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