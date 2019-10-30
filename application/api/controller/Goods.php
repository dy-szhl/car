<?php
namespace app\api\controller;

class Goods extends Common
{
    public function cate($is_return=false,$limit=null)
    {
        $list = [];
        \app\common\model\GoodsCate::getPageData($limit)->each(function($item,$index)use(&$list){
            array_push($list,[
                'id' => $item['id'],
                'name' => $item['name'],
                'img' => $item['img'],
            ]);
        });

        return $is_return ? $list : $this->_resData(1,'获取成功',$list);
    }



    //商品列表
    public function goodsList($is_return=false)
    {

        $input_data = input();
        $list = [];
        $info = \app\common\model\Goods::getPaginateData($input_data)->each(function($item,$index)use(&$list){
            array_push($list,[
                'id' => $item['id'],
                'name' => $item['name'],
                'img' => $item['cover_img'],
                'date_time' => $item['create_time'],
                'price' => $this->user_id?$item['price0']:'登录后查看',
                'sold_num' => $item['sold_num'],
            ]);
        });


        $data=['list'=>$list,'total_page'=>$info->lastPage()];
        return $is_return? $list : $this->_resData(1,'获取成功',$data);
    }
    //商品详情
    public function goodsDetail()
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