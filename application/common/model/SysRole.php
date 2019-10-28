<?php
namespace app\common\model;

//管理员
use think\model\concern\SoftDelete;

class SysRole extends BaseModel
{
    use SoftDelete;
    //数据库表名
    protected $table = 'sys_role';


}