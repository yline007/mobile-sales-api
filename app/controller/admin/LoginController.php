<?php
declare (strict_types = 1);

namespace app\controller\admin;

use app\BaseController;
use app\model\Admin;
use jwt\JWT;
use think\Request;
use think\Response;
use think\facade\Db;

/**
 * 登录控制器
 * Class LoginController
 * @package app\controller\admin
 */
class LoginController extends BaseController
{
    /**
     * 登录页面（GET请求）
     * 
     * @return Response
     */
    public function loginPage(): Response
    {
        // 返回JSON提示，说明这是一个API接口
        return json([
            'code' => 0,
            'msg' => '这是登录API接口，请使用POST方法传递username和password参数',
            'data' => [
                'method' => 'POST',
                'params' => [
                    'username' => '用户名',
                    'password' => '密码'
                ],
                'example' => [
                    'curl' => 'curl -X POST -H "Content-Type: application/json" -d \'{"username":"admin","password":"123456"}\' http://localhost:8082/api/admin/login',
                    'response' => [
                        'code' => 0,
                        'msg' => '登录成功',
                        'data' => [
                            'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...',
                            'refresh_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...',
                            'user' => [
                                'id' => 1,
                                'username' => 'admin',
                                'nickname' => '管理员',
                                'avatar' => '/uploads/avatar/default.png',
                                'role' => 'admin'
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

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

    /**
     * 测试数据库连接
     *
     * @return Response
     */
    public function testDbConnection(): Response
    {
        try {
            // 尝试执行一个简单的SQL查询
            $version = Db::query('SELECT VERSION() as version');
            
            // 获取数据库配置
            $config = [
                'type' => env('DB_TYPE', 'mysql'),
                'host' => env('DB_HOST', '127.0.0.1'),
                'name' => env('DB_NAME', 'mobile_backend'),
                'user' => env('DB_USER', 'root'),
                'pass' => env('DB_PASS', 'admin123'),
                'port' => env('DB_PORT', 3306),
                'charset' => env('DB_CHARSET', 'utf8')
            ];
            
            // 获取数据库表信息
            $tables = Db::query('SHOW TABLES');
            $tableList = [];
            foreach ($tables as $table) {
                $tableList[] = reset($table);
            }
            
            // 连接成功
            return json([
                'code' => 0,
                'msg' => '数据库连接成功',
                'data' => [
                    'version' => $version[0]['version'],
                    'config' => [
                        'type' => $config['type'],
                        'host' => $config['host'],
                        'name' => $config['name'],
                        'user' => $config['user'],
                        'port' => $config['port'],
                        'charset' => $config['charset']
                    ],
                    'tables' => $tableList,
                    'php_version' => PHP_VERSION,
                    'server_info' => [
                        'os' => PHP_OS,
                        'hostname' => gethostname()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            // 连接失败
            return json([
                'code' => 1,
                'msg' => '数据库连接失败',
                'data' => [
                    'error' => $e->getMessage(),
                    'db_host' => env('DB_HOST', '127.0.0.1'),
                    'php_version' => PHP_VERSION,
                    'server_info' => [
                        'os' => PHP_OS,
                        'hostname' => gethostname()
                    ]
                ]
            ]);
        }
    }
} 