<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'username' => 'require|length:4,20|alphaNum',
        'password' => 'require|length:6,20',
        'nickname' => 'require|length:2,50',
        'email'    => 'email',
        'role'     => 'require|in:super-admin,admin',
        'status'   => 'require|in:0,1',
    ];

    protected $message = [
        'username.require' => '用户名不能为空',
        'username.length' => '用户名长度必须在4-20个字符之间',
        'username.alphaNum' => '用户名只能是字母和数字',
        'password.require' => '密码不能为空',
        'password.length' => '密码长度必须在6-20个字符之间',
        'nickname.require' => '昵称不能为空',
        'nickname.length' => '昵称长度必须在2-50个字符之间',
        'email.email' => '邮箱格式不正确',
        'role.require' => '角色不能为空',
        'role.in' => '角色只能是super-admin或admin',
        'status.require' => '状态不能为空',
        'status.in' => '状态只能是0或1',
    ];

    protected $scene = [
        'create' => ['username', 'password', 'nickname', 'email', 'role'],
        'update' => ['nickname', 'email', 'role'],
        'status' => ['status'],
    ];
} 