<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/23
 * Time: 下午5:12
 */

namespace weixin\qy\push;


use weixin\qy\push\models\Event;
use weixin\qy\push\models\Model;
use weixin\qy\security\PrpCrypt;

class Request
{
    static protected $modelMap = [
        Model::TYPE_TEXT => 'weixin\qy\push\models\Text',
        Model::TYPE_IMAGE => 'weixin\qy\push\models\Image',
        Model::TYPE_VOICE => 'weixin\qy\push\models\Voice',
        Model::TYPE_VIDEO => 'weixin\qy\push\models\Video',
        Model::TYPE_SHORT_VIDEO => 'weixin\qy\push\models\ShortVideo',
        Model::TYPE_LOCATION => 'weixin\qy\push\models\Location',

        Model::TYPE_EVENT . Event::EVENT_SUBSCRIBE => 'weixin\qy\push\models\Event',
        Model::TYPE_EVENT . Event::EVENT_UNSUBSCRIBE => 'weixin\qy\push\models\Event',
        Model::TYPE_EVENT . Event::EVENT_LOCATION => 'weixin\qy\push\models\LocationEvent',
        Model::TYPE_EVENT . Event::EVENT_CLICK => 'weixin\qy\push\models\ClickEvent',
        Model::TYPE_EVENT . Event::EVENT_VIEW => 'weixin\qy\push\models\ViewEvent',
        Model::TYPE_EVENT . Event::EVENT_SCANCODE_PUSH => 'weixin\qy\push\models\ScanCodePushEvent',
        Model::TYPE_EVENT . Event::EVENT_SCANCODE_WAITMSG => 'weixin\qy\push\models\ScanCodeWaitMsgEvent',
        Model::TYPE_EVENT . Event::EVENT_PIC_SYSPHOTO => 'weixin\qy\push\models\PicPhotoEvent',
        Model::TYPE_EVENT . Event::EVENT_PIC_PHOTO_OR_ALBUM => 'weixin\qy\push\models\PicPhotoAlbumEvent',
        Model::TYPE_EVENT . Event::EVENT_PIC_WEIXIN_PHOTO => 'weixin\qy\push\models\PicWeixinPhotoEvent',
        Model::TYPE_EVENT . Event::EVENT_LOCATION_SELECT => 'weixin\qy\push\models\LocationSelectEvent',
        Model::TYPE_EVENT . Event::EVENT_ENTER_AGENT => 'weixin\qy\push\models\EnterAgentEvent',
        Model::TYPE_EVENT . Event::EVENT_BATCH_JOB_RESULT => 'weixin\qy\push\models\BatchJobEvent',
    ];

    public $debug = false;

    private $_token;
    private $_encodingAesKey;
    private $_corpID;

    public function __construct($corp_id, $token, $encoding_aes_key)
    {
        $this->_token = $token;
        $this->_encodingAesKey = $encoding_aes_key;
        $this->_corpID = $corp_id;
    }

    public function buildModel($body)
    {
        $decrypt = $this->decrypt($body);

        if ($this->debug) {
            static::traceLog('push_decrypt.log', $decrypt);
        }
        return static::createModel($decrypt);
    }

    protected function decrypt($body)
    {
        $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA);

        $crypt = new PrpCrypt($this->_encodingAesKey);
        return $crypt->decrypt((string)$xml->Encrypt, $this->_corpID);
    }

    protected static function createModel($decrypt)
    {
        $xml = simplexml_load_string($decrypt, 'SimpleXMLElement', LIBXML_NOCDATA);

        $msgType = (string)$xml->MsgType;
        $eventType = (string)$xml->Event;

        $key = strtolower($msgType . $eventType);
        $model = static::$modelMap[$key];

        if ($model) {
            return new $model($xml);
        }
        else
            throw new \ErrorException('Unsupported msg type or event type.');
    }

    private static function traceLog($filename, $text)
    {
        $log = app()->runtimePath . '/' . $filename;
        $handle = fopen($log, 'a');
        if (flock($handle, LOCK_EX | LOCK_NB)) {
            fwrite($handle, $text . "\n\n");
            fflush($handle);
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}