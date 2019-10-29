<?php
namespace app\api\controller;

class Common
{
    /**
     * @var \think\Request
     *
     * */
    protected $request;

    protected $user_id=0;
    //是否需要登录
    protected $need_login=false;
    //忽略需要验证的操作
    protected $ignore_action = [];

    public function __construct()
    {
        $this->request = request();

        $user_token = isset($_SERVER['HTTP_USER_TOKEN'])?$_SERVER['HTTP_USER_TOKEN']:'';
//        print_r($_SERVER);
        $user_token_arr = \app\common\model\User::validUserToken($user_token);
        if($user_token_arr){
            //验证成功
            $this->user_id = $user_token_arr[0];//用户id
            //获取用户凭证
            //$token = \app\common\model\User::where(['id'=>$this->user_id])->value('token');
            //if($user_token!=$token){
                //abort(-9,'帐号已在其它地方登录');
            //}
        }
        //in_array() 搜索数组中是否存在指定的值。
        if(!in_array($this->request->action(),$this->ignore_action) && $this->need_login && empty($this->user_id)){
            //未登录跳转登录页
            abort(-1,'请先登录');
        }
    }

    public function _empty()
    {
        return json(['code'=>-10,'msg'=>'接口不存在'.$this->request->controller(true)."/".$this->request->action(true)]);
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
//            'emit_url'=>url('index/warring',[],false,true)
        ];
        !empty($data) && $res_data['data']= $data;
        return $res_data;
    }
}