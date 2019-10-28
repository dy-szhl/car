<?php
namespace app\admin\controller;

use \app\common\model\SysManager;
use think\Request;

class Orders extends Common
{


    public function index()
    {

        $keyword = $this->request->param('keyword','','trim');
        $state = $this->request->param('state','0','intval');
        $pay_state = $this->request->param('pay_state','0','intval');//支付方式

        $where = [];
        !empty($keyword) && $where[] = ['no','like','%'.$keyword.'%'];

        if ($state == 1) {
            //待付款
            $where['step_flow'] = 0;
            $where['status'] = 0;
        } elseif ($state == 2) {
            //已付款
            $where['step_flow'] = 1;
            $where['status'] = 1;
        } elseif ($state == 3) {
            //待发货
            $where['step_flow'] = 1;
            $where['status'] = 1;
            $where['is_send'] = 0;
        } elseif ($state == 4) {
            //待收货
            $where['step_flow'] = 2;
            $where['is_send'] = 1;
            $where['is_receive'] = 0;
        } elseif ($state == 5) {
            //已完成
            $where['step_flow'] = 3;
            $where['status'] = 3;
        }

        if($pay_state==1){
            //在线付款
            $where['pay_way'] =0;
        }elseif ($pay_state==2){
            //货到付款
            $where['pay_way'] =1;
        }


        $list = \app\common\model\Order::with(['linkUser'])
            ->where($where)
            ->order("id desc")
            ->paginate();

        return view('index',[
            'list'  => $list,
            'pay_state'  => $pay_state,
            'state'  => $state,
            'keyword'  => $keyword,
            'page' => $list->render(),
        ]);
    }

    //订单详情
    public function detail()
    {
        $id = $this->request->param('id');
        $model = \app\common\model\Order::with(['linkUser','linkGoods','linkAddr','linkLogistics'])->where(['id'=>$id])->find();
        //可操作
        $m_handle = [];
        $model && $m_handle = $model->getUserHandleAction('m_handle');
        $location_info = empty($model['linkAddr']['addr'])?[]:explode(' ', $model['linkAddr']['addr']);

        return view('detail',[
            'model'=>$model,
            'm_handle' => $m_handle,
            'location_info' => $location_info,
        ]);
    }


    //删除订单
    public function del()
    {

        $id = $this->request->param('id');
        try{
            \app\common\model\Order::del($this->user_model,$id);
            return $this->_resData(1,'操作成功');
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }



    }

    //取消订单
    public function cancel()
    {

        $id = $this->request->param('id');
        try{
            \app\common\model\Order::cancel($this->user_model,$id);
            return $this->_resData(1,'操作成功');
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }



    }

    //确定支付
    public function surePay()
    {
        $id = $this->request->param('id');
        try{
            \app\common\model\Order::surePay($this->user_model,$id);
            return $this->_resData(1,'操作成功');
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }
    }
    //发货
    public function sendDown()
    {
        $id = $this->request->param('id');
        $logistics = $this->request->param('logistics');
        try{
            \app\common\model\Order::optSend($id,$logistics);
            return $this->_resData(1,'操作成功');
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }
    }

    //调整订单地址
    public function modAddr()
    {
        $php_input = $this->request->post();
        try{
            \app\common\model\Order::modAddr($php_input);
            return $this->_resData(1,'操作成功');
        }catch (\Exception $e){
            return $this->_resData(0,$e->getMessage());
        }
    }

}
