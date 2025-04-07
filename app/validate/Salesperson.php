<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Salesperson extends Validate
{
    protected $rule = [
        'name' => 'require|length:2,50',
        'phone' => 'require|mobile|unique:salesperson',
        'password' => 'require|length:6,20',
        'store_id' => 'number',
        'employee_id' => 'max:50',
        'code' => 'length:32',
        'old_password' => 'require|length:6,20',
        'new_password' => 'require|length:6,20',
    ];

    protected $message = [
        'name.require' => '请输入姓名',
        'name.length' => '姓名长度必须在2-50个字符之间',
        'phone.require' => '请输入手机号码',
        'phone.mobile' => '手机号码格式不正确',
        'phone.unique' => '该手机号已被注册',
        'password.require' => '请输入密码',
        'password.length' => '密码长度必须在6-20个字符之间',
        'store_id.number' => '门店ID必须是数字',
        'employee_id.max' => '工号不能超过50个字符',
        'code.length' => '微信授权码格式不正确',
        'old_password.require' => '请输入原密码',
        'old_password.length' => '原密码长度必须在6-20个字符之间',
        'new_password.require' => '请输入新密码',
        'new_password.length' => '新密码长度必须在6-20个字符之间',
    ];

    protected $scene = [
        'register' => ['name', 'phone', 'password'],
        'update_password' => ['old_password', 'new_password'],
        'reset_password' => ['phone', 'code', 'new_password'],
    ];
} 