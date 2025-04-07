<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class SalespersonLogin extends Validate
{
    protected $rule = [
        'phone' => 'require|mobile',
        'password' => 'require|length:6,20',
    ];

    protected $message = [
        'phone.require' => '请输入手机号码',
        'phone.mobile' => '手机号码格式不正确',
        'password.require' => '请输入密码',
        'password.length' => '密码长度必须在6-20个字符之间',
    ];
} 