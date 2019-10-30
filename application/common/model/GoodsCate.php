<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsCate extends BaseModel
{
    use SoftDelete;

    /**
     * 获取分页数据
     * @param $limit int|null 请求数据
     * @throws
     * @return \think\Collection|array|\PDOStatement|string
     * */
    public static function getPageData($limit=null)
    {
        return self::where(['status'=>1,'pid'=>0])->order('sort asc')->limit($limit)->select();
    }

    public function getImgAttr($value)
    {
        return self::handleFile($value);
    }

    public function linkChild()
    {
        return $this->hasMany('GoodsCate','pid')->order('sort asc');
    }

    public function linkGoods()
    {
        return $this->hasMany('Goods','ct_id')->order('sort asc');
    }


}