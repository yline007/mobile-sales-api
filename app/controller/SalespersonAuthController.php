<?php

declare(strict_types=1);

namespace app\controller;

use app\model\Salesperson;
use app\model\Store;
use think\facade\Request;
use think\Response;
use think\exception\ValidateException;
use think\facade\Cache;
use app\service\WxService;

class SalespersonAuthController
{
    /**
     * 销售员注册
     * @return Response
     */
    public function register(): Response
    {
        $data = Request::only([
            'name',
            'phone',
            'password'
        ]);

        try {
            validate(\app\validate\Salesperson::class)
                ->scene('register')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 验证手机号是否已注册
        if (Salesperson::where('phone', $data['phone'])->find()) {
            return json(['code' => 1, 'msg' => '该手机号已注册']);
        }

        // 生成密码盐值和加密密码
        $salt = random_bytes(16);
        $password = password_hash($data['password'] . bin2hex($salt), PASSWORD_DEFAULT);

        // 创建销售员记录
        $salesperson = new Salesperson([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'password' => $password,
            'salt' => bin2hex($salt),
            'status' => 1
        ]);

        if ($salesperson->save()) {
            // 生成token
            $token = $this->createToken($salesperson);
            return json([
                'code' => 0,
                'msg' => '注册成功',
                'data' => [
                    'id' => $salesperson->id,
                    'name' => $salesperson->name,
                    'phone' => $salesperson->phone,
                    'token' => $token
                ]
            ]);
        }

        return json(['code' => 1, 'msg' => '注册失败']);
    }

    /**
     * 销售员登录
     * @return Response
     */
    public function login(): Response
    {
        $data = Request::only(['phone', 'password']);

        try {
            validate(\app\validate\SalespersonLogin::class)
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 获取销售员信息
        $salesperson = Salesperson::where('phone', $data['phone'])->find();
        if (!$salesperson) {
            return json(['code' => 1, 'msg' => '账号或密码错误']);
        }

        // 验证密码
        if (!password_verify($data['password'] . $salesperson->salt, $salesperson->password)) {
            // 记录登录失败次数
            $key = 'login_fails_' . $data['phone'];
            $fails = Cache::get($key, 0) + 1;
            Cache::set($key, $fails, 3600);

            if ($fails >= 5) {
                return json(['code' => 1, 'msg' => '登录失败次数过多，请1小时后再试']);
            }
            return json(['code' => 1, 'msg' => '账号或密码错误']);
        }

        // 验证账号状态
        if ($salesperson->status != 1) {
            return json(['code' => 1, 'msg' => '账号已被禁用']);
        }

        // 清除登录失败记录
        Cache::delete('login_fails_' . $data['phone']);

        // 生成token
        $token = $this->createToken($salesperson);

        return json([
            'code' => 0,
            'msg' => '登录成功',
            'data' => [
                'id' => $salesperson->id,
                'name' => $salesperson->name,
                'phone' => $salesperson->phone,
                'store_id' => $salesperson->store_id,
                'employee_id' => $salesperson->employee_id,
                'token' => $token
            ]
        ]);
    }

    /**
     * 修改密码
     * @return Response
     */
    public function updatePassword(): Response
    {
        // 获取当前登录用户
        $salesperson = $this->getCurrentSalesperson();
        if (!$salesperson) {
            return json(['code' => 1, 'msg' => '请先登录']);
        }

        $data = Request::only(['old_password', 'new_password']);

        try {
            validate(\app\validate\Salesperson::class)
                ->scene('update_password')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 验证原密码
        if (!password_verify($data['old_password'] . $salesperson->salt, $salesperson->password)) {
            return json(['code' => 1, 'msg' => '原密码错误']);
        }

        // 生成新密码
        $salt = random_bytes(16);
        $password = password_hash($data['new_password'] . bin2hex($salt), PASSWORD_DEFAULT);

        // 更新密码
        $salesperson->password = $password;
        $salesperson->salt = bin2hex($salt);

        if ($salesperson->save()) {
            return json(['code' => 0, 'msg' => '密码修改成功']);
        }
        return json(['code' => 1, 'msg' => '密码修改失败']);
    }

    /**
     * 重置密码
     * @return Response
     */
    public function resetPassword(): Response
    {
        $data = Request::only(['phone', 'code', 'new_password']);

        try {
            validate(\app\validate\Salesperson::class)
                ->scene('reset_password')
                ->check($data);
        } catch (ValidateException $e) {
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }

        // 验证短信验证码
        if (!$this->verifySmsCode($data['phone'], $data['code'])) {
            return json(['code' => 1, 'msg' => '验证码错误或已过期']);
        }

        // 获取销售员信息
        $salesperson = Salesperson::where('phone', $data['phone'])->find();
        if (!$salesperson) {
            return json(['code' => 1, 'msg' => '账号不存在']);
        }

        // 生成新密码
        $salt = random_bytes(16);
        $password = password_hash($data['new_password'] . bin2hex($salt), PASSWORD_DEFAULT);

        // 更新密码
        $salesperson->password = $password;
        $salesperson->salt = bin2hex($salt);

        if ($salesperson->save()) {
            return json(['code' => 0, 'msg' => '密码重置成功']);
        }
        return json(['code' => 1, 'msg' => '密码重置失败']);
    }

    /**
     * 生成JWT token
     * @param Salesperson $salesperson
     * @return string
     */
    private function createToken(Salesperson $salesperson): string
    {
        $payload = [
            'id' => $salesperson->id,
            'name' => $salesperson->name,
            'phone' => $salesperson->phone,
            'store_id' => $salesperson->store_id,
            'exp' => time() + 7200 // 2小时过期
        ];

        // 这里使用JWT库生成token
        // return JWT::encode($payload, config('jwt.secret'), 'HS256');
        // 临时返回测试token
        return base64_encode(json_encode($payload));
    }

    /**
     * 获取当前登录销售员信息
     * @return Salesperson|null
     */
    private function getCurrentSalesperson(): ?Salesperson
    {
        $token = Request::header('Authorization');
        if (empty($token)) {
            return null;
        }

        // 解析token
        $token = str_replace('Bearer ', '', $token);
        try {
            // $payload = JWT::decode($token, config('jwt.secret'), ['HS256']);
            // 临时解析测试token
            $payload = json_decode(base64_decode($token), true);
            return Salesperson::find($payload['id']);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 验证短信验证码
     * @param string $phone
     * @param string $code
     * @return bool
     */
    private function verifySmsCode(string $phone, string $code): bool
    {
        $key = 'sms_code_' . $phone;
        $savedCode = Cache::get($key);
        if ($savedCode && $savedCode === $code) {
            Cache::delete($key);
            return true;
        }
        return false;
    }
}
