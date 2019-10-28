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


}