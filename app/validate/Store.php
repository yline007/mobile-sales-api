<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Store extends Validate
{
    protected $rule = [
        'name'    => 'require|length:2,100',
        'address' => 'length:5,255',
        'phone'   => 'require|mobile',
        'manager' => 'length:2,50',
        'status'  => 'require|in:0,1',
    ];

    protected $message = [
        'name.require' => '门店名称不能为空',
        'name.length' => '门店名称长度必须在2-100个字符之间',
        'address.length' => '门店地址长度必须在5-255个字符之间',
        'phone.require' => '联系电话不能为空',
        'phone.mobile' => '联系电话格式不正确',
        'manager.length' => '店长姓名长度必须在2-50个字符之间',
        'status.require' => '状态不能为空',
        'status.in' => '状态只能是0或1',
    ];

    protected $scene = [
        'create' => ['name', 'address', 'phone', 'manager'],
        'update' => ['name', 'address', 'phone', 'manager'],
        'status' => ['status'],
    ];
} 