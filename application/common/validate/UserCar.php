<?php
namespace app\common\validate;

use think\Validate;

class UserCar extends Validate
{
    //验证规则
    protected $rule = [
        'number' => 'require',
        'brand_model' => 'require',
        'car_frame' => 'require',
        'engine' => 'require',
    ];
    //提示信息
    protected $message = [
        'number.require' => '请输入车牌号',
        'brand_model.require' => '请输入品牌和车型',
        'car_frame.require' => '请输入车架号',
        'engine.require' => '请输入发动机号',
    ];
}