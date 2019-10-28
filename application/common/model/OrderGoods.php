<?php

namespace app\common\model;


class OrderGoods extends BaseModel
{
    public $sold_num = 0;//销售数量

    /**
     * 订单商品评论
     * @param $user_model User 评论用户
     * @param $data array 对应数据
     * @throws
     * */
    public static function com(User $user_model, array $data = [])
    {
        $content = empty($data['content']) ? '' : $data['content'];
        $img = empty($data['img']) ? [] : $data['img'];
        $level = empty($data['level']) ? 5 : $data['level'];
        $level = ($level>5||$level<1) ?5:$level;
        $og_id = empty($data['og_id']) ? 0 : $data['og_id'];
        $gid = empty($data['gid']) ? 0 : $data['gid'];

        empty($og_id) && exception('订单商品参数异常:og_id');
        empty($gid) && exception('订单商品参数异常:gid');
        empty($content) && empty($img)&& exception('请输入评论内容/上传图片');

        //查看商品信息
        $model = self::get($og_id);
        empty($model) && exception('订单商品信息异常');
        !empty($model['is_comment']) && exception('已评论');

        //写入评论信息
        $com_model  = new GoodsComment();
        $com_model->uid = $user_model->id;
        $com_model->og_id = $og_id;
        $com_model->gid = $gid;
        !empty($content) && $com_model->content = $content;
        !empty($img) && $com_model->img = implode(',',$img);
        $com_model->level = $level;
        $com_model->save();
        //修改评论状态
        $model->is_comment =1;
        $model->save();

    }

    public function linkOrder()
    {
        return $this->belongsTo('Order', 'oid');
    }

}