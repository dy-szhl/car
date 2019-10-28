<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class User extends BaseModel
{
    use SoftDelete;

    public static $fields_type = [
        ['name'=>'普通会员'     ,'img'=>''],
        ['name'=>'vip会员'      ,'img'=>''],
        ['name'=>'高级vip会员'  ,'img'=>''],
    ];
    protected $insert=['face','name','status'=>1];
    /**
     * 生成user_token
     * @return string
     * */
    public function generateUserToken()
    {
        $time = time();
        $access_token = $this->id.'.'.$time.'.'.rand(10000,9999).'.'.self::generateSign($this->id,$time);
        //更新登录凭证
        //$this->token = $access_token;
        //$this->save();

        return $access_token;
    }

    /**
     * 验证user_token
     * @param string $user_token
     * @return bool|array
     * */
    public static function validUserToken($user_token)
    {
        if(empty($user_token)){
            return false;
        }
        $arr = explode('.',$user_token);
        $arr_len = count($arr);
        if($arr_len!=4){
            return false;
        }
        if($arr[$arr_len-1] !=self::generateSign($arr[0],$arr[1])){
            return false;
        }
        return $arr;

    }

    //自动完成名称
    protected function setNameAttr($value,$data)
    {
        if($value){
            $name =  $value;
        }else{
            $phone = empty($data['phone'])?'':$data['phone'];

            if(!empty($phone)){
                $name = substr($phone,-4).'用户';
            } else{
                $name = '用户昵称';
            }
        }
        return $name;
    }

    //生成token签名
    protected static function generateSign($user_id,$time)
    {
        return md5($user_id.md5($time));
    }


    /**
     * 修改用户数据
     * @param $data array
     * */
    public function modeInfo(array $data)
    {
        $this->readonly(['money']);
        $this->data($data,true);

        $this->save($data);
    }
    /**
     * 登录
     */
    public static function handleLogin($account){
        empty($account) && exception('请输入手机号');
        $model = self::where(['phone'=>$account])->find();
        if(empty($model)){
            $model = new self();
            $data['phone'] = $account;
        }
        if(!empty($data)){
            //赋值数据
            foreach ($data as $key=>$vo){
                $model->setAttr($key,$vo);
            }
            $bool = $model->save();
            empty($bool) && exception('更新失败');
        }
        return $model;
    }

    /**
     * 添加购物车
     * @param $gid int 商品id
     * @param $num int 数量
     * @param $mod bool 调整数量
     * @return bool
     */
    public function addShoppingCart($gid,$num=1,$mod=false)
    {
        empty($gid) && exception('参数异常:'.$gid);
        $model = UserCart::where(['uid'=>$this->id,'gid'=>$gid])->find();
        if(!empty($model)){
            if($num<0 && $model->num<=1){

            }elseif($mod){
                $model->num=$num;
                $model->save();
            }else{
                $model->setInc('num',$num);
            }
        }else{
            $model = new UserCart();
            $model->uid = $this->id;
            $model->gid = $gid;
            $model->num = $num;
            $model->save(false);
        }
    }
    /**
     * 商品收藏
     * @param $goods_id int|array 商品id
     * @throws
     * @return array
     */
    public function goodsColl($goods_id)
    {
        empty($goods_id) && exception('操作对象异常');
        if(!is_array($goods_id)){
            if(strpos(',',$goods_id)){
                $goods_id = explode(',',$goods_id);
            }else{
                $goods_id = [$goods_id];
            }
        }
        $state = 1;
        //查询已收藏记录
        $exist_goods_info = UserColl::where([['uid','=',$this->id],['gid','in',$goods_id]])->column('gid,add_time');
        $exist_goods_ids = array_keys($exist_goods_info);
        //添加收藏
        $add_goods_ids = array_diff($goods_id,$exist_goods_ids);
        //取消收藏
        $cancel_goods_ids = array_intersect($goods_id,$exist_goods_ids);
        //
        if($add_goods_ids){
            $add_goods_data = [];
            $reset_goods_data = [];
            foreach ($add_goods_ids as $vo){
                if(isset($exist_goods_info[$vo]) && empty($exist_goods_info[$vo])){
                    //恢复
                    array_push($reset_goods_data, $vo);
                }else{
                    //新增
                    array_push($add_goods_data,[
                        'uid'=>$this->id,
                        'gid'=>$vo,
                        'add_time'=>date('Y-m-d H:i:s')
                    ]);
                }

            }
            UserColl::insertAll($add_goods_data);
//            dump($reset_goods_data);exit;
            $reset_goods_data && UserColl::where([['uid','=',$this->id],['gid','in',$reset_goods_data]])->update(['add_time'=>date('Y-m-d H:i:s')]);
        }
        if($cancel_goods_ids){
            $state = 0;
            $cancel_goods_data = [];
            $reset_goods_cancel = [];
            foreach ($cancel_goods_ids as $vo){
                if($exist_goods_info[$vo]){
                    //取消
                    array_push($cancel_goods_data, $vo);
                }else{
                    //恢复
                    array_push($reset_goods_cancel, $vo);
                }

            }
//            dump($exist_goods_info);
//            dump($reset_goods_cancel);
//            dump($cancel_goods_data);
//            exit;
            //取消
            $cancel_goods_data && UserColl::where([['uid','=',$this->id],['gid','in',$cancel_goods_data]])->update(['add_time'=>null]);
            if($reset_goods_cancel){
                //恢复
                $state = 1;
                UserColl::where([['uid','=',$this->id],['gid','in',$reset_goods_cancel]])->update(['add_time'=>date('Y-m-d H:i:s')]);
            }
        }
        if(count($goods_id)!==1){
            $state = 0;
        }
        return [$state];

    }

    /**
     * 绑定手机号
     * @param $phone string 手机号码
     * @param $type int|bool 验证码
     * @param $verify string|bool 验证码
     * @throws
     * */
    public function bindPhone($phone,$type=false,$verify=false)
    {
        !validPhone($phone) && exception('请输入正确的手机号');

        $model = self::where([['phone','=',$phone],['id','<>',$this->id]])->find();
        !empty($model) && exception('手机号已被使用,请更换其它手机号');
        if($type!==false){

            //验证手机号
            Sms::validVerify($type,$phone,$verify);
        }
        //验证通过
        $this->phone=$phone;
        $this->save();
    }


    /**
     * 购物车商品选中和取消选中效果
     * @var $cart_id int 购物车id
     * @var $is_full_checked int|null 全选和反全选
     * @throws
     * @return array
     */
    public function cartChoose($gid,$is_full_checked=null)
    {
        // 1选中 0未选中
        $is_checked = 1;
        $bool = true;
        if(!is_null($is_full_checked)){
            if(!$is_full_checked){
                $is_checked=0;
            }
            //全选
            UserCart::where(['uid'=>$this->id])->update(['is_checked'=>$is_full_checked?1:0]);
        }else{

            $model = UserCart::where(['uid'=>$this->id,'gid'=>$gid])->find();
            if(empty($model)){

            }else{
                if($model->is_checked==1){
                    //取消选中
                    $model->is_checked = 0;
                    $is_checked = 0;
                }else{
                    $model->is_checked = 1;
                }
                $model->save();
            }
        }

        return [$bool,$is_checked];
    }

    /**
     * 删除购物车
     * @param $gid int|array 商品id
     * @throws
     *
     * */
    public function cartDel($gid)
    {
        if(!is_array($gid)){
            $gid = [$gid];
        }
        empty($gid) && exception('参数异常:gid');
        UserCart::where(['gid'=>$gid,'uid'=>$this->id])->delete();
    }

}