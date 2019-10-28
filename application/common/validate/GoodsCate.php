<?php
namespace app\common\validate;

use think\Validate;

class GoodsCate extends Validate
{
    //验证规则
    protected $rule = [
        'name'      =>  'require',
    ];
    //提示信息
    protected $message = [
        'name.require'      => '分类名必须输入',
    ];


}