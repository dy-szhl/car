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
    public function goods_list()
    {
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
        $info = \app\common\model\Goods::where($where)->order($order_sql)->paginate()->each(function($item,$index)use(&$list){
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
    //商品详情
    public function goods_detail()
    {
        $id = input('id',0,'intval');
        $model = \app\common\model\Goods::get($id);
        if(empty($model)){
            return $this->_resData(0,'商品不存在');
        }
        $data = [];
        $data['goods'] = [
            'id' => $model['id'],
            'name' => $model['name'],
            'img' => $model['img'],
            'stock' => $model['stock'],
            'spu' => $model['spu'],
            'price0' => $model['price0'],
            'price1' => $model['price1'],
            'price2' => $model['price2'],
            'content' => $model['content'],
        ];
        return $this->_resData(1,'获取成功',$data);
    }

    //关键字搜索
    public function search()
    {
        $content = \app\common\model\SysSetting::getContent('hot_key');
        $data['hot_key'] = explode("\r\n",$content);
        return $this->_resData(1,'获取成功',$data);
    }
}