<?php
namespace app\common\model;

use think\Model;
use think\route\dispatch\Callback;
use think\Validate;

class BaseModel extends Model
{
    public static $fields_status = ['--','正常','关闭'];
    protected $jsonAssoc  = true;

    public static function init()
    {
        parent::init();


    }




    //状态
    public static function getPropInfo($propOrFunc,$key=null,$field=null)
    {
        $class = get_called_class();
        if(method_exists($class,$propOrFunc)){
            $data = $class::$propOrFunc();
        }elseif(property_exists($class,$propOrFunc)){
            $data = $class::$$propOrFunc;
        }else{
            return false;
        }

        if(is_null($key)){
            return is_null($field)?$data:false;
        }else{
            $info = isset($data[$key])?$data[$key] : [];
            return is_null($field)?$info:(isset($info[$field])?$info[$field]:'');
        }
    }
    /**
     * 数据保存/更新
     * @param array $data 数据
     * @param Validate $validate 验证器
     * @param \Closure $func 闭包
     * @throws
     * @return \stdClass;
     * */
    public function actionAdd(array $data=[],Validate $validate=null)
    {
        if ($validate && !$validate->check($data)) {
            exception($validate->getError());
        }
        $model = $this;
        $pk = $this->getPk();
        if(!empty($data[$pk])){  //编辑状态
            $model = $this->find($data[$pk]);
        }else{
            //清除主键影响
            unset($data[$pk]);
        }

        try{
            $model && $model->save($data);
            return $model;
        }catch (\Exception $e) {
            exception('操作异常'.$e->getMessage());
        }
    }

    /*
     * 数据删除
     * */
    public function actionDel(array $where=[])
    {
        try{
            !count($where) && exception('条件异常');
            $model = $this->where($where)->find();
            $model && $model->delete();
            return ['code'=>1,'msg'=>'删除成功'];
        }catch (\Exception $e) {
            return ['code'=>0,'msg'=>'删除异常:'.$e->getMessage()];
        }
    }


    /**
     * 评论删除
     * @param Users $user_model|null;
     * @param array $data;
     * @throws
     * @return self
     * */
    public static function commentDel(Users $user_model=null,array $data=[])
    {
        empty($data['id']) && exception('参数异常:id');
        $where[] = ['id', '=', $data['id']];
        if(!empty($user_model)){
            $where[] = ['uid','=',$user_model->id];
        }

        $model = self::where($where)->find();
        if(empty($model)){
            exception('评论已删除');
        }
        self::whereOr([['id','=',$model['id']],['pid','=',$model['id']]])->delete();
        return $model;
    }


    //验证文件是否是图片
    public static function checkImg($file)
    {
        if(empty($file)){
            return null;
        }
        return preg_match('/.*(\.?png|\.?jpg|\.?jpeg|\.?gif)$/', $file);
    }

    //处理图片路径问题
    public static function handleFile($file='')
    {

        if(empty($file)) return '';
        if(preg_match('/^https?:\/\//',$file)) return $file;
        //当前模块
        $module = request()->module();
        if($module=='api'){
            //api模块增加域名前缀
            //当前服务器的域名
            $file = request()->domain().$file;
        }
        return $file;


    }

    // 制作邀请码
    protected function make_coupon_card() {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        return  $d;
    }

    //计算时间
    public static function time_tranx($the_time)
    {
        $now_time = time();
        $dur = $now_time - $the_time;
        if ($dur <= 0) {
            $mas =  '刚刚';
        } else {
            if ($dur < 60) {
                $mas =  $dur . '秒前';
            } else {
                if ($dur < 3600) {
                    $mas =  floor($dur / 60) . '分钟前';
                } else {
                    if ($dur < 86400) {
                        $mas =  floor($dur / 3600) . '小时前';
                    } else {
                        if ($dur < 259200) { //3天内
                            $mas =  floor($dur / 86400) . '天前';
                        } else {
                            $mas =  date("Y-m-d H:i:s",$the_time);
                        }
                    }
                }
            }
        }
        return $mas;
    }



}