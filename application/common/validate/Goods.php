<?php
namespace app\common\validate;

use think\Validate;

class Goods extends Validate
{
    //验证规则
    protected $rule = [
        'cid'       =>  'require',
        'name'      =>  'require',
        'img'       =>  'require',
        'sold'      =>  'between:0,99999',
//        'og_price'  =>  'require|between:0.01,99999',
//        'content'   =>  'require',
    ];
    //提示信息
    protected $message = [
        'cid.require'           => '请选择分类',
        'bid.require'           => '请选择品牌分类',
        'name.require'          => '商品名必须输入',
        'img.require'           => '请上传商品图片',
        'content.require'       => '内容不能为空',
        'og_price.require'      => '请输入商品原价',
        'og_price.between'      => '商品原价只能在:1-:2之间',
        'sold.between'      => '销售数量只能在:1-:2之间',

    ];

    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        parent::__construct($rules, $message, $field);

        $user_type = \app\common\model\User::getPropInfo('fields_type');
        foreach ($user_type as $key=>$vo){
            $this->rule = array_merge($this->rule,
                ['price'.$key=>'require|between:0.01,99999']
            );
            $this->message = array_merge($this->message,
                ['price'.$key.'.require'=>'请输入'.$vo['name'].'商品售价'],
                ['price'.$key.'.between'=>'商品'.$vo['name'].'售价只能在:1-:2之间']
            );
        }

    }
}