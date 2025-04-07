<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\BaseController;
use app\model\Admin;
use jwt\JWT;
use think\Request;
use think\Response;

/**
 * 登录控制器
 * Class LoginController
 * @package app\controller\admin
 */
class LoginController extends BaseController
{
    /**
     * 管理员登录
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        // 获取登录参数
        $username = $request->param('username');
        $password = $request->param('password');

        // 参数验证
        if (empty($username) || empty($password)) {
            return json(['code' => 1, 'msg' => '用户名和密码不能为空']);
        }

        // 查询用户
        $admin = Admin::where('username', $username)->find();
        if (!$admin) {
            return json(['code' => 1, 'msg' => '用户不存在']);
        }

        // 验证密码
        if (md5($password) !== $admin->password) {
            return json(['code' => 1, 'msg' => '密码错误']);
        }

        // 验证状态
        if ($admin->status != 1) {
            return json(['code' => 1, 'msg' => '账号已被禁用']);
        }

        // 生成JWT Token
        $payload = [
            'admin_id' => $admin->id,
            'username' => $admin->username,
            'role' => $admin->role
        ];
        
        $accessToken = JWT::generateToken($payload);
        $refreshToken = JWT::generateRefreshToken($admin->id);

        // 返回用户信息
        return json([
            'code' => 0,
            'msg' => '登录成功',
            'data' => [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'user' => [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'nickname' => $admin->nickname,
                    'avatar' => $admin->avatar,
                    'role' => $admin->role
                ]
            ]
        ]);
    }

    /**
     * 刷新Token
     * 
     * @param Request $request
     * @return Response
     */
    public function refreshToken(Request $request): Response
    {
        $refreshToken = JWT::getTokenFromHeader($request->header('Authorization'));
        
        if (empty($refreshToken)) {
            return json(['code' => 1, 'msg' => '刷新令牌不能为空'], 401);
        }
        
        // 验证刷新令牌
        $payload = JWT::verifyToken($refreshToken);
        if ($payload === false) {
            return json(['code' => 1, 'msg' => '刷新令牌无效或已过期'], 401);
        }
        
        // 检查是否为刷新令牌
        if (!isset($payload['is_refresh']) || $payload['is_refresh'] !== true) {
            return json(['code' => 1, 'msg' => '无效的刷新令牌'], 401);
        }
        
        // 获取用户信息
        $admin = Admin::find($payload['user_id']);
        if (!$admin) {
            return json(['code' => 1, 'msg' => '管理员不存在'], 401);
        }
        
        // 生成新的访问令牌
        $newPayload = [
            'admin_id' => $admin->id,
            'username' => $admin->username,
            'role' => $admin->role
        ];
        
        $accessToken = JWT::generateToken($newPayload);
        
        return json([
            'code' => 0,
            'msg' => '刷新令牌成功',
            'data' => [
                'access_token' => $accessToken
            ]
        ]);
    }

    /**
     * 获取当前管理员信息
     *
     * @param Request $request
     * @return Response
     */
    public function getInfo(Request $request): Response
    {
        // 从请求中获取管理员信息（Auth中间件已经验证并附加）
        $admin = $request->adminInfo;

        return json([
            'code' => 0,
            'msg' => 'success',
            'data' => [
                'id' => $admin->id,
                'username' => $admin->username,
                'nickname' => $admin->nickname,
                'avatar' => $admin->avatar,
                'email' => $admin->email,
                'role' => $admin->role,
                'createTime' => $admin->create_time,
                'updateTime' => $admin->update_time
            ]
        ]);
    }
} 