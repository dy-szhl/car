<?php
namespace app\http\middleware;

use think\Request;

class  CrossHandle
{
    public function handle($request, \Closure $next)
    {
        header('Access-Control-Allow-Origin:*');
        
        return $next($request);
    }
}