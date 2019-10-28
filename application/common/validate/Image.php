<?php
namespace app\common\validate;

use think\Validate;

class Image extends Validate
{
    //验证规则
    protected $rule = [
        'img'      =>  'require',
    ];
    //提示信息
    protected $message = [
        'img.require'      => '请上传图片',
    ];


}