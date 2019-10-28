<?php
namespace app\common\validate;

use think\Validate;

class UserAddr extends Validate
{
    //验证规则
    protected $rule = [
        'username'      =>  'require',
        'phone'         =>  'require|checkphone',
        'addr'          =>  'require',
        'addr_extra'    =>  'require',
    ];
    //提示信息
    protected $message = [
        'username.require'      => '请输入用户名',
        'phone.require'         => '请输入手机号码',
        'addr.require'          => '请选择地址',
        'addr_extra.require'    => '请输入详细地址',
    ];

    //自定义验证规则
    public function checkphone($value, $rule,$data)
    {
        if(!validPhone($value)){
            return '请输入正确的手机号';
        }
        return true;
    }

}