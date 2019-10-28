<?php
namespace app\common\service\third;

require_once \think\facade\Env::get('vendor_path').'wechat'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'WxPay.Data.php';
class Wechat implements IPay
{


    protected static $config_instance;



    /**
     * 获取微信配置单例
     * */
    public static function configInstance()
    {
        $instance = self::$config_instance;
        if(empty($instance)){
            $instance = new WechatConfg();
        }
        return $instance;
    }

    /**
     * 获取code换access_token
     * @param string $mode 平台 app/web
     * @param string $code 用户换取access_token的code
     * @throws
     * @return array
     * */
    public static function codeToAct($code)
    {
        //换取微信信息
        $config = self::configInstance();
        $param = [
            'appid' => $config->GetAppId(),
            'secret' => $config->GetAppSecret(),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $result = \app\common\HttpCurl::req('https://api.weixin.qq.com/sns/oauth2/access_token',$param);
        $info = json_decode($result,true);
        if(empty($info)){
            exception('授权信息为空');
        }else{
            if(!empty($info['errcode'])){
                //报错
                exception('授权异常:'.$info['errmsg'].' 错误代码:'.$info['errcode']);
            }else{
                return $info;
            }
        }
    }

    /**
     * 获取accessToken获取用户资料
     * @param string $access_token 用户授权凭证
     * @throws
     * @return array
     * */
    public static function actToUserInfo($access_token, $openid)
    {
        $param = [
            'access_token' => $access_token,
            'openid' => $openid,
        ];
        $result = \app\common\HttpCurl::req('https://api.weixin.qq.com/sns/userinfo',$param);
        $info = json_decode($result,true);
        if(empty($info)){
            exception('获取用户信息异常');
        }else{
            if(!empty($info['errcode'])){
                //报错
                exception('异常:'.$info['errmsg'].' 错误代码:'.$info['errcode']);
            }else{
                return $info;
            }
        }
    }


    /**
     * 微信access_token
     * @throws
     * @return array
     * */
    public static function accessToken()
    {
        $wechat = self::configInstance();
        //换取微信信息
        $cache_name = 'wechat_access_token';
        $access_token = cache('wechat_access_token');
        if( !empty($access_token) ){
            $param = [
                'grant_type' => 'client_credential',
                'appid' => $wechat->config->GetAppId(),
                'secret' => $wechat->config->GetAppSecret(),
            ];
            $result = \app\common\HttpCurl::req('https://api.weixin.qq.com/cgi-bin/token',$param);
            $info = json_decode($result,true);
            if(empty($info)){
                exception('获取access_token异常');
            }else{
                if(!empty($info['errcode'])){
                    //报错
                    exception('异常:'.$info['errmsg'].' 错误代码:'.$info['errcode']);
                }else{
                    $access_token = $info['access_token'];
                    cache($cache_name, $access_token, 6000);
                }
            }
        }
        return $access_token;
    }

    public function jssdkPay(\think\Model $model,$open_id)
    {
        //引入第三方
        require_once \think\facade\Env::get('vendor_path').'wechat/lib/WxPay.Api.php';


        $input = $this->handleOrderInput($model,'JSAPI');
        $config = self::configInstance();
        $input->SetOpenid($open_id);

        $result = \WxPayApi::unifiedOrder($config, $input);
        if($result['return_code']!='SUCCESS'){
            exception('支付信息异常,请联系管理员');
        }
        if($result['result_code']!='SUCCESS'){
            exception($result['err_code'].','.$result['err_code_des']);
        }
//        dump($result);
        $result_data = array(
            'appId'  => $config->GetAppId(),
            'nonceStr' => $result['nonce_str'],
            'timeStamp' => time(),
            'signType' => $config->GetSignType(),
            'package' => 'prepay_id='.$result['prepay_id'],
        );
//        dump($result_data);exit;
        ksort($result_data,SORT_STRING);
        $str = '';
        foreach($result_data as $key=>$vo){
            $str.=$key.'='.$vo.'&';
        }
        $str.='key='.$config->GetKey();
        if($config->GetSignType()=='MD5'){
            $sign = strtoupper(md5($str));
        }elseif($config->GetSignType()=='HMAC-SHA256'){
            $sign = strtoupper(hash_hmac("sha256",$str ,$config->GetKey()));
        }else{
            throw new \Exception('签名类型不支持!!');
        }
        $result_data['paySign'] =$sign;

        return $result_data;
    }


    //app支付
    public function appPay(\think\Model $model)
    {
        //引入第三方
        require_once \think\facade\Env::get('vendor_path').'wechat/lib/WxPay.Api.php';

        $input = $this->handleOrderInput($model,'APP');
        $input->SetBody(config('app.app_name'));

        $config = self::configInstance();
        $result = \WxPayApi::unifiedOrder($config, $input);

        if(isset($result['result_code']) && $result['result_code']=='SUCCESS' && isset($result['return_code']) && $result['return_code']=='SUCCESS'){
            $result_data = array(
                'appid'  => $config->GetAppId(),
                'partnerid' => $config->GetAppSecret(),
                'prepayid' => $result['prepay_id'],
                'noncestr' => $result['nonce_str'],
                'timestamp' => time(),
                'package' => 'Sign=WXPay',
            );
            ksort($result_data,SORT_STRING);
            $str = '';
            foreach($result_data as $key=>$vo){
                $str.=$key.'='.$vo.'&';
            }
            $str.=$config->GetKey();
            if($config->GetSignType()=='MD5'){
                $sign = strtoupper(md5($str));
            }elseif($config->GetSignType()=='HMAC-SHA256'){
                $sign = strtoupper(hash_hmac("sha256",$str ,$config->GetKey()));
            }else{
                throw new \Exception('签名类型不支持!!');
            }

            $result_data['sign'] =$sign;
//            dump($result_data);exit;
            return $result_data;
        }else{
            $error_msg='调起支付失败';
            if($result['return_code']!='SUCCESS'){
                $error_msg = $result['return_msg'];
            }elseif($result['err_code']!='SUCCESS'){
                $error_msg = $result['err_code_des'];
            }
            exception($error_msg);
        }
    }

    public function h5Pay(\think\Model $model){
        //引入第三方
        require_once \think\facade\Env::get('vendor_path').'wechat/lib/WxPay.Api.php';

        $input = $this->handleOrderInput($model,'MWEB');
        //$input->SetBody(config('app.app_name'));

        $config = self::configInstance();
        $result = \WxPayApi::unifiedOrder($config, $input);
//        print_r($result);die;
        return $result;
    }
    //二维码支付
    public function nativePay(\think\Model $model)
    {
        //引入第三方
        require_once \think\facade\Env::get('vendor_path').'wechat/lib/WxPay.Api.php';
//        require_once \think\facade\Env::get('vendor_path').'wechat\example\WxPay.NativePay.php';

        $input=$this->handleOrderInput($model,'NATIVE');

        $input->SetProduct_id('123456');

        $config = self::configInstance();
        $result = \WxPayApi::unifiedOrder($config, $input);
        //print_r($result);die;
        //return $result;
        return "<img src='http://qr.liantu.com/api.php?text=" . $result['code_url'] . "' alt='扫描进行支付'>";
    }

    protected function handleOrderInput($model,$trade_type)
    {
        $input = new \WxPayUnifiedOrder();
        $pay_info = $model->getOrderPayInfo('wechat');
        isset($pay_info['body']) &&$input->SetBody($pay_info['body']);
        isset($pay_info['attach']) &&$input->SetAttach($pay_info['attach']);
        isset($pay_info['no']) &&$input->SetOut_trade_no($pay_info['no']);
        $input->SetTotal_fee($pay_info['pay_money']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + (isset($pay_info['expire_time'])?$pay_info['expire_time']:600)));
        isset($pay_info['goods_tag']) && $input->SetGoods_tag($pay_info['goods_tag']);
        $input->SetNotify_url($pay_info['notify_url']);
        $input->SetTrade_type($trade_type);

        return $input;
    }


    //支付回调
    public static function notify()
    {
        //引入第三方
        require_once \think\facade\Env::get('vendor_path').'wechat/lib/WxPay.Api.php';
//        $data = file_get_contents("php://input");
//        \think\facade\Log::write("wechat-notify:" .json_encode(input()),'-------input-----');
//        \think\facade\Log::write("wechat-notify:" .$data,'-----file_get_contents-----');
        $config = self::configInstance();
        $notify = new PayNotifyCallBack();
        $notify->Handle($config,false);
//        return 'success';
    }




}


require_once \think\facade\Env::get('vendor_path').'wechat/lib/WxPay.Config.Interface.php';
class WechatConfg extends \WxPayConfigInterface
{
    protected $APPID = '';
    protected $APP_SECRET = '';

    protected $KEY = '';
    protected $MCH_ID = '';

    public function __construct()
    {
        $this->APPID = config('third.wx_mch.app_id');
        $this->APP_SECRET = config('third.wx_mch.app_secret');
        $this->KEY = config('third.wx_mch.key');
        $this->MCH_ID = config('third.wx_mch.mch_id');
    }

    public function GetAppId()
    {
        return $this->APPID;
    }

    public function GetMerchantId()
    {
        return $this->MCH_ID;
    }

    public function GetNotifyUrl()
    {
        // TODO: Implement GetNotifyUrl() method.
    }

    public function GetSignType()
    {
        return 'MD5';
    }

    public function GetProxy(&$proxyHost, &$proxyPort)
    {
        // TODO: Implement GetProxy() method.
    }

    public function GetReportLevenl()
    {
        // TODO: Implement GetReportLevenl() method.
    }

    public function GetKey()
    {
        return $this->KEY;
    }

    public function GetAppSecret()
    {
        return $this->APP_SECRET;
    }

    public function GetSSLCertPath(&$sslCertPath, &$sslKeyPath)
    {
        // TODO: Implement GetSSLCertPath() method.
    }
}

//回调处理逻辑
require_once \think\facade\Env::get('vendor_path').'wechat/lib/WxPay.Notify.php';
class PayNotifyCallBack extends \WxPayNotify
{


    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);

//        $config = new WxPayConfig();
        $result = \WxPayApi::orderQuery(Wechat::configInstance(), $input);
//        Log::DEBUG("query:" . json_encode($result));
        trace("query:" . json_encode($result),'微信支付回调查询'.__METHOD__);

        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return $result;
        }
        return false;
    }

    /**
     *
     * 回包前的回调方法
     * 业务可以继承该方法，打印日志方便定位
     * @param string $xmlData 返回的xml参数
     *
     **/
    public function LogAfterProcess($xmlData)
    {
        trace("call back， return xml:" . $xmlData,'微信支付回调查询'.__METHOD__);
//        Log::DEBUG("call back， return xml:" . $xmlData);
        return;
    }

    //重写回调处理函数
    /**
     * @param WxPayNotifyResults $data 回调解释出的参数
     * @param WxPayConfigInterface $config
     * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
     * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($objData, $config, &$msg)
    {
        $data = $objData->GetValues();
        //TODO 1、进行参数校验
        if(!array_key_exists("return_code", $data)
            ||(array_key_exists("return_code", $data) && $data['return_code'] != "SUCCESS")) {
            //TODO失败,不是支付成功的通知
            //如果有需要可以做失败时候的一些清理处理，并且做一些监控
            $msg = "异常异常";
            return false;
        }
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }

        //TODO 2、进行签名验证
        try {
            $checkResult = $objData->CheckSign($config);
            if($checkResult == false){
                //签名错误
//                Log::ERROR("签名错误...");
                trace("进行签名验证异常:\"签名错误..." ,'微信支付回调查询'.__METHOD__);
                return false;
            }
        } catch(Exception $e) {
            trace("进行签名验证异常:" . $e->getMessage(),'微信支付回调查询'.__METHOD__);
//            Log::ERROR(json_encode($e));
        }

        //TODO 3、处理业务逻辑
        trace("处理业务逻辑:" . json_encode($data),'微信支付回调查询'.__METHOD__);
        $notfiyOutput = array();


        //查询订单，判断订单真实性
        $query_info = $this->Queryorder($data["transaction_id"]);
        if($query_info===false){
            trace("订单查询失败:" . json_encode($query_info),'微信支付回调查询'.__METHOD__);
        }

        //订单处理
        if($query_info['trade_state']=='SUCCESS'){
            //支付成功
            \app\common\model\Order::handleNotify($data["out_trade_no"],$data);
        }



        return [true,$query_info['trade_state']];
    }
}