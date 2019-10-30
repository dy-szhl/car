<?php
namespace app\api\controller;

class Index extends Common
{
    public function index()
    {
        echo 123123213;
        return 'api';
    }
    //发送短信
    public function sendSms(){

        $type = $this->request->param('type',0);
        $phone = $this->request->param('phone');
        try{
            \app\common\model\Sms::send($type,$phone);
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }
        return $this->_resData(1,'发送成功');
    }
    //登录
    public function login(){
        $code = $this->request->param('code');
        $phone = $this->request->param('phone');
        //小程序
        $min_data = $this->request->param('min_data');
        $min_data = empty($min_data)?[]:json_decode($min_data, true);

        try{
            $model = \app\common\model\User::handleCodeLogin($phone, $code,$min_data);
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }

        //验证用户是否添加过车辆
        $car_num = 1;

        return $this->_resData(1,'登录成功',[
            'user_token' => $model->generateUserToken(),
            'user_face'  => $model->getAttr('face'),
            'type'       => $model->getAttr('type'),
            'name'       => $model->getAttr('name'),
            'money'      => $model->getAttr('money'),
            'car_num'    => $car_num,
        ]);
    }

    //轮播图
    public function followImg($is_return=false)
    {
        $list = [];
        \app\common\model\Image::getPageData(0)->each(function($item,$index)use(&$list){
            array_push($list,[
                'id'    => $item['id'],
                'img'   => $item['img'],
                'title' => $item['title'],
                'url'   => $item['url'],
            ]);
        });

        return $is_return ? $list : $this->_resData(1,'获取成功',$list);
    }




    //首页数据
    public function data()
    {
        $list=[];
        //轮播图
        $list['follow_img'] = $this->followImg(true);

        $goods_controller = new Goods();
        //分类
        $list['goods_cate'] = $goods_controller->cate(true,7);

        //新品
        $this->request->is_new = 1;
        $this->request->limit = 3;
        $list['new_goods_list'] = $goods_controller->goodsList(true);
        unset($this->request->is_new);
        //热门
        $this->request->limit = 8;
        $this->request->is_hot = 1;
        $list['new_hot_list'] = $goods_controller->goodsList(true);
        //销毁对象
        unset($this->request->is_hot,$this->request->limit);

        return $this->_resData(1,'获取成功',$list);
    }

}
