<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Url;

class Common extends Controller
{
    /**
     * 用户id
     * */
    protected $user_id=0;
    /**
     * @var \app\common\model\SysManager
     * */
    protected $user_model;
    //是否需要登录
    protected $need_login=true;
    //忽略需要验证的操作
    protected $ignore_action = [];

    public function initialize()
    {
        $this->user_id = session('?user_info')?session('user_info.user_id'):0;

        if(!empty($this->user_id)){
            //绑定管理员数据
            $this->user_model = \app\common\model\SysManager::get($this->user_id);
            if(empty($this->user_model) || $this->user_model['status']!=1){
                $this->user_id=0;
            }
        }

        //验证登录
        //in_array() 搜索数组中是否存在指定的值。
        if(!in_array($this->request->action(),$this->ignore_action) && $this->need_login && empty($this->user_id)){
            //未登录跳转登录页
            if($this->request->isAjax()){
                $this->error('请先登录','Index/login');exit;
            }else{
                $this->redirect('Index/login');
            }
        }
    }


    /**
     * 响应数据
     * @param int $code 状态码
     * @param string $msg 消息
     * @param array $data
     * @return array
     * */
    final protected function _resData($code=0,$msg='操作失败',array $data=[])
    {
        $res_data = [
            'code' => $code,
            'msg'=>$msg,
        ];
        !empty($data) && $res_data['data']= $data;
        return $res_data;
    }
}
