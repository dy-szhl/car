<?php
namespace app\admin\widget;


class Components
{
    //导航
    public function menu($current_index='')
    {
        $menu = [
            ['name'=>'用户管理','icon'=>'','url'=>'','active_index'=>[],'child'=>[
                ['name'=>'用户列表','url'=>'Users/index']
            ]],
            ['name'=>'产品管理','icon'=>'','url'=>'','active_index'=>[],'child'=>[
                ['name'=>'商品分类','url'=>'Goods/cate'],
                ['name'=>'商品列表','url'=>'Goods/index'],
            ]],
            ['name'=>'订单管理','icon'=>'','url'=>'','active_index'=>[],'child'=>[
                ['name'=>'订单列表','url'=>'Orders/index']
            ]],
            ['name'=>'系统管理','icon'=>'','url'=>'','active_index'=>[],'child'=>[
//                ['name'=>'发送通知','url'=>'system/notice'],
                ['name'=>'投诉列表','url'=>'system/feedback'],
                ['name'=>'轮播图设置','url'=>'System/image'],
                ['name'=>'系统设置','url'=>'System/setting'],
                ['name'=>'管理员列表','url'=>'System/manager']
            ]],
        ];

        foreach ($menu as &$vo){
            foreach ($vo['child'] as &$item){
                $item['url'] = strtolower($item['url']);
                array_push($vo['active_index'],$item['url']);

            }
        }

        return view('/common/menu',[
            'menu'=>$menu,
            'current_index'=>strtolower($current_index)
        ])->getContent();
    }
}