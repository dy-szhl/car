<?php
namespace app\api\controller;

class Goods extends Common
{
    public function cate()
    {
        $php_input = input();
        $list = [];
        $info = \app\common\model\GoodsCate::getPaginateData($php_input)->each(function($item,$index)use(&$list){
            array_push($list,[
                'id' => $item['id'],
                'name' => $item['name'],
                'date_time' => $item['create_time'],
            ]);
        });

        $data=['list'=>$list,'total_page'=>$info->lastPage()];
        return $this->_resData(1,'获取成功',$data);
    }
    //商品列表
    public function goods_list(){
        $list = [];
        $where[] = ['status','=',1];
        $order_sql = 'update_time desc';
        if(input()){
            $fields = input('fields');
            $order = input('order');
            $keyword = input('keyword');
            if($fields && $order) $order_sql = $fields.' '.$order;
            if($keyword) $where[] = ['name','like','%'.$keyword.'%'];
        }
        $php_input['order'] = $order_sql;
        $php_input['where'] = $where;
        
        $info = \app\common\model\Goods::getFormatData($php_input)->each(function($item,$index)use(&$list){
            array_push($list,[
                'id' => $item['id'],
                'name' => $item['name'],
                'img' => $item['img'],
                'date_time' => $item['create_time'],
                'price' => $this->user_id?$item['price0']:'登录后查看',
                'sold_num' => $item['sold_num'],
            ]);
        });
        $data=['list'=>$list,'total_page'=>$info->lastPage()];
        return $this->_resData(1,'获取成功',$data);
    }

}