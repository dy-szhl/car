<?php
namespace app\common\model;

use think\model\concern\SoftDelete;

class Goods extends BaseModel
{
    use SoftDelete;

    protected $json = ['spu'];

    public static $fields_default_spu = [
        ['name'=>'品牌','value'=>''],
        ['name'=>'型号','value'=>''],
        ['name'=>'材质','value'=>''],
        ['name'=>'尺寸','value'=>''],
    ];
    /**
    * 获取分页数据
    * @param $php_input array 请求数据
    * @throws
    * @return \think\Paginator
    * */
    public static function getFormatData(array $php_input=[])
    {
        return self::where($php_input['where'])->order($php_input['order'])->paginate();
    }

    public function setCidAttr($value,$data)
    {
        if(empty($value)){
            return 0;
        }
        $arr = explode(',',$value);
        if(!empty($arr[1])){
            $this->setAttr('ct_id',$arr[1]);
        }
        return $arr[0];
    }

    //商品封面图
    public  function getCoverImgAttr($value,$data)
    {
        if(empty($data['img'])){
           return '';
        }else{
            $arr = explode(',',$data['img']);
            return $arr[0];
        }
    }

    public function getIntroAttr($value)
    {
        return empty($value)?'':$value;
    }

    public function getImgAttr($value)
    {
        return empty($value)?[]:explode(',',$value);
    }
    public function setImgAttr($value)
    {
        if(is_array($value)){
            return implode(',',$value);
        }
        return $value;
    }

    //商品分类
    public function linkCate()
    {
        return $this->belongsTo('GoodsCate','cid');
    }

    //商品评论
    public function linkGoodsComment()
    {
        return $this->hasMany('GoodsComment','gid');
    }
}