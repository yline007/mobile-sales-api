<?php
declare (strict_types = 1);

namespace app\middleware;

use app\model\Salesperson;
use think\facade\Request;

class SalespersonAuth
{
    public function handle($request, \Closure $next)
    {
        // 获取token
        $token = Request::header('Authorization');
        if (empty($token)) {
            return json(['code' => 401, 'msg' => 'Token验证失败，请重新登录']);
        }

        // 解析token
        $token = str_replace('Bearer ', '', $token);
        try {
            // 临时解析测试token
            $payload = json_decode(base64_decode($token), true);
            if (!$payload || !isset($payload['id'])) {
                return json(['code' => 401, 'msg' => 'Token验证失败，请重新登录']);
            }

            // 验证销售员是否存在且状态正常
            $salesperson = Salesperson::find($payload['id']);
            if (!$salesperson || $salesperson->status != 1) {
                return json(['code' => 401, 'msg' => 'Token验证失败，请重新登录']);
            }

            // 将销售员信息保存到请求中
            $request->salesperson = $salesperson;

            return $next($request);
        } catch (\Exception $e) {
            return json(['code' => 401, 'msg' => 'Token验证失败，请重新登录']);
        }
    }
} 