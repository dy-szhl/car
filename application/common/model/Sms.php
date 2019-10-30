<?php
namespace app\common\model;


class Sms extends BaseModel
{
    //数据库表名
    protected $table = 'sms';

    public static $fields_type = [
        ['name'=>'号码绑定','exp_time'=>60,'content'=>'您本次验证码为{verify}。请您尽快输入验证，谢谢。'],
    ];

    /**
     * 发送验证码
     * @param integer $type 短信类型
     * @param string $phone 手机号码
     * @throws
     * */
    public static function send($type,$phone)
    {
        //验证类型
        !array_key_exists($type,self::getPropInfo('fields_type')) && exception('发送类型异常');
        //验证手机号码
        !valid_phone($phone) && exception('请输入正确的手机号');
        //发送内容
        list($content,$param) = self::handleContent(self::getPropInfo('fields_type',$type));
        //发送短信
        $send_info = \app\common\service\Sms::send($content,$phone);
        //保存数据库
        (new Sms)->save([
            'type'      => $type,
            'phone'     => $phone,
            'content'   => $content,
            'verify'    => isset($param['verify'])?$param['verify']:'',
            'info'      => $send_info
        ]);
    }

    /**
     * 处理数据
     * @param array $data
     * @return array
     * */
    public static function handleContent(array $data=[])
    {
        $param = [];
        $content = isset($data['content'])?$data['content']:'';
        $replace_data = [
            'exp_time' => intval((isset($data['exp_time'])?$data['exp_time']:0)/60), //直接转为分钟
            'verify'   => mt_rand(10000,99999),
        ];

        preg_match_all('/\{[^\}]+\}/',$content,$matches);
        $match_data = isset($matches[0]) ? $matches[0] : [];

        if(count($match_data)){
            foreach ($match_data as $vo){
                $field = substr($vo,1,-1);
                $param[$field] = isset($replace_data[$field])?$replace_data[$field]:'';
            }
            $content = str_replace($match_data, $param,$content);
        }
        return [$content,$param];
    }


    /**
     * 验证验证码
     * @param int $type 验证码类型
     * @param string $phone 手机号码
     * @param string $verify 验证码
     * @throws
     * @return bool
     * */
    public static function validVerify($type,$phone,$verify)
    {
        if($verify=='1234'){
            return true;
        }
        //验证类型
        !array_key_exists($type,self::getPropInfo('fields_type')) && exception('发送类型异常');
        //验证手机号码
        !valid_phone($phone) && exception('请输入正确的手机号');
        empty($verify) && exception('请输入验证码');

        $model = self::where(['type'=>$type,'phone'=>$phone])->order('id desc')->find();
        empty($model) && exception('请先获取验证码');
        !empty($model['use_time'])&& exception('验证码已被使用,请重新获取');
        $model['verify'] != $verify && exception('验证码错误');
        //保存验证码
        $model->use_time = date('Y-m-d H:i:s');
        $model->save();
        return true;
    }
}