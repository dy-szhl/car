<?php
namespace app\api\controller;

class Personal extends Common
{
    protected $need_login = true;

    //用户信息
    public function info()
    {

    }

    //爱车列表
    public function carlist()
    {
        $list = [];
        $info = \app\common\model\UserCar::getFormatData()->each(function($item,$index)use(&$list){
            array_push($list,[
                'id' => $item['id'],
                'number' => $item['number'],
                'brand_model' => $item['brand_model'],
                'car_frame' => $item['car_frame'],
                'engine' => $item['engine'],
                'is_default' => $item['is_default'],
            ]);
        });
        $data=['list'=>$list,'total_page'=>$info->lastPage()];
        return $this->_resData(1,'获取成功',$data);
    }
    //添加爱车
    public function add_car()
    {
        if($this->request->isPost()){
            $php_input = $this->request->param();
            try{
                $php_input['uid'] = $this->user_id;
                $validate = new \app\common\validate\UserCar();
                $model = new \app\common\model\UserCar();
                $model->actionAdd($php_input,$validate);
            }catch (\Exception $e){
                return $this->_resData(0,$e->getMessage());
            }
            return $this->_resData(1,'成功添加');
        }else{
            return $this->_resData(0,'错误请求');
        }
    }
    //删除爱车
    public function cardel()
    {
        $id = $this->request->param('id',0,'int');
        try{
            \app\common\model\UserCar::carDel($id,$this->user_id);
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }
        return $this->_resData(1,'已删除');
    }
    //默认爱车
    public function cardefault()
    {
        $id = $this->request->param('id',0,'int');
        try{
            \app\common\model\UserCar::carDefault($id,$this->user_id);
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }
        return $this->_resData(1,'已设为默认');
    }
}