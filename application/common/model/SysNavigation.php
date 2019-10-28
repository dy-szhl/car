<?php
namespace app\common\model;

class SysNavigation extends BaseModel
{
    //数据库表名
    protected $table = 'sys_navigation';

    public function linkChild()
    {
        return $this->hasMany('SysNavigation','pid')->where('status',1)->order('sort asc');
    }
    public function linkParent()
    {
        return $this->belongsTo('SysNavigation','pid')->where('status',1);
    }
}