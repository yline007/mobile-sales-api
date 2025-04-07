<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Phone extends Validate
{
    protected $rule = [
        'name'     => 'require|length:2,100',
        'logo'     => 'url',
        'image'    => 'url',
        'brand_id' => 'require|number',
        'status'   => 'require|in:0,1',
    ];

    protected $message = [
        'name.require' => '名称不能为空',
        'name.length' => '名称长度必须在2-100个字符之间',
        'logo.url' => 'Logo URL格式不正确',
        'image.url' => '图片URL格式不正确',
        'brand_id.require' => '品牌ID不能为空',
        'brand_id.number' => '品牌ID必须是数字',
        'status.require' => '状态不能为空',
        'status.in' => '状态只能是0或1',
    ];

    protected $scene = [
        'createBrand' => ['name', 'logo'],
        'updateBrand' => ['name', 'logo'],
        'createModel' => ['brand_id', 'name', 'image'],
        'updateModel' => ['brand_id', 'name', 'image'],
        'status' => ['status'],
    ];
} 