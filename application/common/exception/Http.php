<?php
namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;

class Http extends Handle
{
    public function render(Exception $e)
    {
        // 参数验证错误
        if ($e instanceof ValidateException) {
            return json(['code'=>0, 'msg'=>$e->getError()]);
        }

        // 请求异常
        if ($e instanceof HttpException || request()->isAjax()) {
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }
//        dump(\think\Container::get('app')->config('http_exception_template'));exit;
        // 其他错误交给系统处理
//        return json(['code'=>$e->getCode(), 'msg'=>'异常::'.$e->getMessage()]);

        return parent::render($e);
    }

}