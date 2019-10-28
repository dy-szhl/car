<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsCate extends BaseModel
{
    use SoftDelete;

    /**
     * 获取分页数据
     * @param $php_input array 请求数据
     * @throws
     * @return \think\Paginator
     * */
    public static function getPaginateData(array $php_input=[])
    {
        return self::where(['status'=>1,'pid'=>0])->paginate();
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