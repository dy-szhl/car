<?php
namespace app\api\controller;

class Index extends Common
{
    public function index()
    {
        return 'api';
    }
    //发送短信
    public function sendSms(){
        //$type = $this->request->param('type');
        if($this->request->isPost()){
            $phone = $this->request->param('phone');
            try{
                \app\common\model\Sms::send(0,$phone);
            }catch (\Exception $e){
                return $this->_resData(0,$e->getMessage());
            }
            return $this->_resData(1,'发送成功');
        }else{
            return $this->_resData(0,'错误请求');
        }
    }
    //登录
    public function login(){
       if($this->request->isPost()){
            $code = $this->request->param('code');
            $phone = $this->request->param('phone');
            $input_data = $this->request->param();
            try{
                \app\common\model\Sms::validVerify(0,$phone,$code);
                unset($input_data['code']);
                unset($input_data['phone']);
                $Model = \app\common\model\User::handleLogin($phone,$input_data);
            }catch (\Exception $e){
                return $this->_resData(0,$e->getMessage());
            }
            return $this->_resData(1,'登录成功',['user_token' => $Model->generateUserToken()]);
       }else{
           return $this->_resData(0,'错误请求');
       }
    }

    //
}
