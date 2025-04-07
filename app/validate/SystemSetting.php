<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class SystemSetting extends Validate
{
    protected $rule = [
        'key'    => 'require|length:1,50|alphaDash',
        'value'  => 'require',
        'remark' => 'max:255',
    ];

    protected $message = [
        'key.require' => '设置键名不能为空',
        'key.length' => '设置键名长度必须在1-50个字符之间',
        'key.alphaDash' => '设置键名只能是字母、数字、下划线和破折号',
        'value.require' => '设置值不能为空',
        'remark.max' => '备注最多255个字符',
    ];

    protected $scene = [
        'create' => ['key', 'value', 'remark'],
        'update' => ['value', 'remark'],
    ];
} 