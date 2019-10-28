<?php
namespace app\common\model;


use think\model\concern\SoftDelete;

class UserNotice extends BaseModel
{
    use SoftDelete;
    public static $fields_type=[
        ['name'=>'系统通知','img'=>'/assets/index/images/message04.png'],
        ['name'=>'订单通知','img'=>'/assets/index/images/message02.png'],
        ['name'=>'物流通知','img'=>'/assets/index/images/message01.png'],
    ];

    /**
     * 记录发送的通知信息
     * @param $title string 信息标题
     * @param $content string 信息详细内容
     * @param $cond_id int 条件id
     * @param $uid int 用户id
     * @param $type int 消息类型
     * */
    public static function recordLog($title='',$content='',$cond_id=0,$uid=0,$type=0)
    {
        $model = new self();
        $model->data([
            'type' => $type,
            'uid' => $uid,
            'cond_id' => $cond_id,
            'title' => $title,
            'content' => $content,
        ],true);
        $model->save();
    }


    //阅读记录
    public function linkRead()
    {
        return $this->hasMany('UserNoticeRead','nid');
    }

    //用户阅读记录
    public function linkUserRead()
    {
        return $this->hasOne('UserNoticeRead','nid');
    }
}