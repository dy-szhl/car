<?php
namespace app\common\model;

//
class SysSetting extends BaseModel
{
    const CACHE_PREFIX = 'cache_setting_sql';
    //数据库表名
    protected $table = 'sys_setting';

    protected $pk = 'type';

    //获取内容字段
    public static function getContent($type,$filed=null)
    {
        $cache_name= $type;
        if(!is_null($filed)){
            $cache_name= $type.'_'.$filed;
        }
        $content = cache($cache_name);
        if(empty($content)){
            $content = self::where('type',$cache_name)->value('content');
            //保存内容
            self::_cacheData($type, $cache_name, $content);
        }
        return $content;
    }

    //获取内容字段
    public static function setContent($type,$content)
    {
        if(is_array($content)){
            if(key($content)===0){
                self::_writeData($type,implode(',',$content));
            }else{
                foreach ($content as $key=>$vo){
                    $pk_key = $type.'_'.$key;
                    self::_writeData($pk_key,$vo);
                }
            }
        }else{
            self::_writeData($type,$content);
        }
        self::_cacheclear($type);
    }

    //写入数据
    private static function _writeData($type,$content)
    {
        $model = self::where(['type'=>$type])->find();
        if(empty($model)){
            $model = new self;
        }
        $model->data([
            'type'=>$type,
            'content'=>$content
        ],true);
        $model->save();
        //清空缓存
        \think\facade\Cache::rm($type); ;
    }



    //记录缓存
    private static function _cacheData($tag,$name,$content)
    {
        \think\facade\Cache::tag($tag)->set($name,$content,3600);
    }

    //删除缓存
    private static function _cacheclear($tag)
    {
        \think\facade\Cache::clear($tag);
    }
}