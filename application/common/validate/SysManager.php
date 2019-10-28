<?php
namespace app\common\validate;

use think\Validate;

class SysManager extends Validate
{
    //验证规则
    protected $rule = [
        'name'      =>  'require|min:1',
        'account'   =>  'require|min:4|checkAccount',
        'password'  =>  'requireCallback:checkRequire|min:6',
    ];
    //提示信息
    protected $message = [
        'name.require'      => '用户名必须输入',
        'name.min'          => '用户名字符长度必须超过:rule位',
        'account.require'   => '帐号必须输入',
        'account.length'    => '帐号长度必须为:rule位',
        'account.unique'    => '帐号已存在',
        'password.requireCallback'=> '密码不能为空',
        'password.min'      => '密码不得低于:rule位',
    ];

    //场景验证Admin_add中验证
    public function sceneAdmin_add_proxy()
    {
        return $this->only(['name','account','password']);
    }


    public function checkRequire($value, $data)
    {
        if(empty($data['id'])){
            return true;
        }
    }

    //自定义验证规则
    public function checkAccount($value, $rule,$data)
    {
        $model = new \app\common\model\SysManager();
        $model = $model->where('account',$value);
        if(!empty($data['id'])){
            $model =$model->where('id','neq',$data['id']);
        }

        if($model->count()){
            return '帐号已存在';
        }
        return true;
    }
}