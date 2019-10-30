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
            return self::handleFile($arr[0]);
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

    /**
     * 获取商品数据
     * @param array $input_data 请求内容
     * @param int|null $limit 页面条数
     * @throws
     * @return \think\Paginator
     * */
    public static function getPaginateData(array $input_data=[])
    {
        $where = [];
        $where[] = ['status','=',1];
        //条数
        $limit = isset($input_data['limit'])?$input_data['limit']:null;
        //关键字检索
        $keyword = empty($input_data['keyword'])?'':trim($input_data['keyword']);
        if(!empty($keyword)){
            $where[] = ['name','like','%'.$keyword.'%'];
        }

        //排序
        $order = 'sort asc';
        if(!empty($input_data['order_field'])){
            $sort = empty($input_data['sort_field'])?'asc':($input_data['sort_field']=='asc'?'asc':'desc');
            if($input_data['order_field']=='price'){
                $order = 'price'.' '.$sort;
            }elseif($input_data['order_field']=='sold'){
                $order = 'sold'.' '.$sort;
            }
        }

        //分类删选
        !empty($input_data['cid']) && $where[] = ['cid' ,'=', $input_data['cid']];
        !empty($input_data['ct_id']) && $where[] = ['ct_id' ,'=', $input_data['cid']];

        //新品
        !empty($input_data['is_new']) && $where[] = ['is_new' ,'=',1];
        !empty($input_data['is_hot']) && $where[] = ['is_hot' ,'=',1];

        return self::where($where)->order($order)->paginate($limit);
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