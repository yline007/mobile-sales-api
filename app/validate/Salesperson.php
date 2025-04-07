<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Salesperson extends Validate
{
    protected $rule = [
        'name'        => 'require|length:2,50',
        'phone'       => 'require|mobile',
        'store_id'    => 'require|number',
        'employee_id' => 'length:4,50|alphaNum',
        'status'      => 'require|in:0,1',
    ];

    protected $message = [
        'name.require' => '销售员姓名不能为空',
        'name.length' => '销售员姓名长度必须在2-50个字符之间',
        'phone.require' => '联系电话不能为空',
        'phone.mobile' => '联系电话格式不正确',
        'store_id.require' => '所属门店不能为空',
        'store_id.number' => '所属门店ID必须是数字',
        'employee_id.length' => '工号长度必须在4-50个字符之间',
        'employee_id.alphaNum' => '工号只能是字母和数字',
        'status.require' => '状态不能为空',
        'status.in' => '状态只能是0或1',
    ];

    protected $scene = [
        'create' => ['name', 'phone', 'store_id', 'employee_id'],
        'update' => ['name', 'phone', 'store_id', 'employee_id'],
        'status' => ['status'],
    ];
} 