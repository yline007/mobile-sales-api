<?php
declare (strict_types = 1);

namespace app\middleware;

use Closure;

/**
 * 跨域处理中间件
 */
class Cors
{
    /**
     * 处理跨域请求
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 3600');
        
        // 如果是预检请求（OPTIONS），直接结束执行
        if ($request->method(true) == 'OPTIONS') {
            exit('');
        }
        
        return $next($request);
    }
} 