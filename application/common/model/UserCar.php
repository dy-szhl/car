<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class UserCar extends BaseModel
{
    use SoftDelete;
    protected $name = 'user_cars';

    public static function getFormatData()
    {
        return self::order('is_default desc')->paginate();
    }

    //删除
    public static function carDel($id,$uid)
    {
        empty($id) && exception('缺少参数');
        $model = self::where(['id'=>$id])->find();
        if($model['uid']!=$uid) exception('无法操作');
        //self::actionDel(['id'=>$id]);
        $model->delete_time = time();
        $model->save();
        return true;
    }
    //默认
    public static function carDefault($id,$uid)
    {
        (empty($id) || empty($uid)) && exception('缺少参数');
        $model = self::where(['id'=>$id])->find();
        if($model['uid']!=$uid) exception('无法操作');
        self::where(['uid'=>$uid])->update(['is_default'=>0]);
        $model->is_default = 1;
        $model->save();
        return true;
    }
}