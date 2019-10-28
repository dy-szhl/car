<?php
namespace app\common\service;

class Sms
{

    const USER_ID = '60379';
    const ACCOUNT = '13809488328';
    const PASSWORD = 'A93D10511AF0F361A1E5B72DFEF2';
    const URL = 'http://web.1xinxi.cn/asmx/smsservice.aspx';
//    const SIGN = '【养众天使】';
    public static function send($content,$mobile)
    {

        $data = [
            'name'      => self::ACCOUNT,
            'pwd'       => self::PASSWORD,
            'sign'      => self::_getSign(),
            'mobile'    => $mobile,
            'content'   => $content,
            'stime'     => '',
            'type'      => 'pt',
            'extno'     => '',

        ] ;
        return \app\common\HttpCurl::req(self::URL,$data,'get');
    }

    private static function _getSign()
    {
        return config('app.app_name');
    }

}