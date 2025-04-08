<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class UpdatePassword extends Validate
{
    protected $rule = [
        'old_password'     => 'require|length:6,20',
        'new_password'     => 'require|length:6,20|different:old_password',
        'confirm_password' => 'require|confirm:new_password'
    ];

    protected $message = [
        'old_password.require'     => '请输入原密码',
        'old_password.length'      => '原密码长度必须在6-20个字符之间',
        'new_password.require'     => '请输入新密码',
        'new_password.length'      => '新密码长度必须在6-20个字符之间',
        'new_password.different'   => '新密码不能与原密码相同',
        'confirm_password.require' => '请输入确认密码',
        'confirm_password.confirm' => '两次输入的新密码不一致'
    ];
} 