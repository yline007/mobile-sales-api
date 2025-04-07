<?php
declare (strict_types = 1);

namespace app\middleware;

use app\model\Admin;
use jwt\JWT;
use think\Request;
use think\Response;

/**
 * 身份验证中间件
 * Class Auth
 * @package app\middleware
 */
class Auth
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        // 获取请求头中的token
        $token = JWT::getTokenFromHeader($request->header('Authorization'));
        
        // 如果没有token，返回未授权
        if (empty($token)) {
            return json(['code' => 1, 'msg' => '未登录或登录已过期'], 401);
        }
        
        // 验证token
        $payload = JWT::verifyToken($token);
        if ($payload === false) {
            return json(['code' => 1, 'msg' => 'Token验证失败，请重新登录'], 401);
        }
        
        // 检查是否为刷新token
        if (isset($payload['is_refresh']) && $payload['is_refresh'] === true) {
            return json(['code' => 1, 'msg' => '无效的访问令牌'], 401);
        }
        
        // 查询管理员信息
        $admin = Admin::find($payload['admin_id']);
        if (!$admin) {
            return json(['code' => 1, 'msg' => '管理员不存在'], 401);
        }
        
        // 判断管理员状态
        if ($admin->status != 1) {
            return json(['code' => 1, 'msg' => '账号已被禁用'], 403);
        }
        
        // 将管理员信息附加到请求对象
        $request->adminInfo = $admin;
        
        return $next($request);
    }
} 