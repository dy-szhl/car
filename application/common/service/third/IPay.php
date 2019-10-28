<?php
namespace app\common\service\third;

interface IPay
{
    /*app支付*/
    public function appPay(\think\Model $model);
    /*扫码支付*/
    public function nativePay(\think\Model $model);

    /*通知回调*/
    public static function notify();
}