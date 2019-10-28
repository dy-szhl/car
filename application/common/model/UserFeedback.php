<?php
namespace app\common\model;



class UserFeedback extends BaseModel
{

    public static $fields_status = ['提交','已处理','未处理'];

    /**
     * 记录反馈信息
     * @param $data array 提交的数据
     * @param $user_model User|null 用户模型
     * @throws
     * */
    public static function record(array $data=[],User $user_model=null)
    {
        empty($data['content']) && exception('请输入反馈内容');
        !empty($data['phone']) && !validPhone($data['phone']) && exception('请输入正确的手机号');
        $model = new self();
        !empty($user_model) && $model->uid = $user_model->id;
        !empty($data['phone']) && $model->phone = $data['phone'];

        $model->content = $data['content'];
        $model->save();
    }
}