<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/7/23
 * Time: 下午5:58
 */

namespace weixin\qy\push;


use weixin\qy\Base;
use weixin\qy\security\PrpCrypt;

class Response
{
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';

    private $_token;
    private $_encodingAesKey;
    private $_corpID;

    private $_toUser;
    private $_fromUser;
    private $_msgType;

    public function __construct($corp_id, $token, $encoding_aes_key)
    {
        $this->_token = $token;
        $this->_encodingAesKey = $encoding_aes_key;
        $this->_corpID = $corp_id;
    }

    public function text($text, $to_user = null, $from_user = null)
    {
        if ($to_user) $this->_toUser = $to_user;
        if ($from_user) $this->_fromUser = $from_user;

        $this->_msgType = self::TYPE_TEXT;

        $tpl = '<Content><![CDATA[%s]]></Content>';
        $extraXml = sprintf($tpl, $text);

        return $this->buildXml($extraXml);
    }

    public function image($media_id, $to_user = null, $from_user = null)
    {
        if ($to_user) $this->_toUser = $to_user;
        if ($from_user) $this->_fromUser = $from_user;

        $this->_msgType = self::TYPE_IMAGE;

        $tpl = '<Image><MediaId><![CDATA[%s]]></MediaId></Image>';
        $extraXml = sprintf($tpl, $media_id);

        return $this->buildXml($extraXml);
    }

    public function setToUser($to_user)
    {
        $this->_toUser = $to_user;
        return $this;
    }

    public function setFromUser($from_user)
    {
        $this->_fromUser = $from_user;
        return $this;
    }

    protected function buildXml($extraXml)
    {
        $tpl = '<xml>
           <Encrypt><![CDATA[%s]]></Encrypt>
           <MsgSignature><![CDATA[%s]]></MsgSignature>
           <TimeStamp>%s</TimeStamp>
           <Nonce><![CDATA[%s]]></Nonce>
        </xml>';

        $timestamp = time();
        $nonce = uniqid();
        $plainXml = $this->buildPlainXml($extraXml);
        $encryptXml = $this->buildEncryptedXml($plainXml);
        $signature = Base::getSHA1($this->_token, $timestamp, $nonce, $encryptXml);

        return sprintf($tpl, $encryptXml, $signature, $timestamp, $nonce);
    }

    protected function buildEncryptedXml($xml)
    {
        $crypt = new PrpCrypt($this->_encodingAesKey);
        return $crypt->encrypt($xml, $this->_corpID);
    }

    protected function buildPlainXml($extraXml)
    {
        return '<xml>' . $this->defaultPlainXml() . $extraXml . '</xml>';
    }

    protected function defaultPlainXml()
    {
        $tpl = '<ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%d</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>';

        return sprintf($tpl, $this->_toUser, $this->_fromUser, time(), $this->_msgType);
    }
}