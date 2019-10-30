<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class Image extends BaseModel
{
    use SoftDelete;
    public static $fields_type = [
//        ['name'=>'首页轮播'],
//        ['name'=>'商品捆绑页面'],
    ];

    public function getImgAttr($value)
    {
        return self::handleFile($value);
    }


    /**
     * 页面数据
     * @param int $type 图片类型
     * @throws
     * @return \think\Collection|array|\PDOStatement|string
     * */
    public static function getPageData($type=0)
    {
        return self::where(['type'=>$type,'status'=>1])->order('sort asc')->select();
    }
}