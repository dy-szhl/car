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
}