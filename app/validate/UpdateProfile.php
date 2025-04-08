<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class UpdateProfile extends Validate
{
    protected $rule = [
        'name'  => 'require|length:2,50',
        'phone' => 'require|mobile|length:11'
    ];

    protected $message = [
        'name.require'  => '请输入姓名',
        'name.length'   => '姓名长度必须在2-50个字符之间',
        'phone.require' => '请输入手机号',
        'phone.mobile'  => '手机号格式不正确',
        'phone.length'  => '手机号必须是11位'
    ];
} 