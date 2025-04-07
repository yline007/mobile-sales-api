<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Sales extends Validate
{
    protected $rule = [
        'store_id'       => 'require|number',
        'salesperson_id' => 'require|number',
        'phone_brand_id' => 'require|number',
        'phone_model_id' => 'require|number',
        'imei'          => 'require|length:15,17|alphaNum',
        'customer_name' => 'require|length:2,50',
        'customer_phone' => 'require|mobile',
        'photo_url'     => 'url',
        'remark'        => 'max:500',
    ];

    protected $message = [
        'store_id.require' => '门店ID不能为空',
        'store_id.number' => '门店ID必须是数字',
        'salesperson_id.require' => '销售员ID不能为空',
        'salesperson_id.number' => '销售员ID必须是数字',
        'phone_brand_id.require' => '手机品牌ID不能为空',
        'phone_brand_id.number' => '手机品牌ID必须是数字',
        'phone_model_id.require' => '手机型号ID不能为空',
        'phone_model_id.number' => '手机型号ID必须是数字',
        'imei.require' => 'IMEI号不能为空',
        'imei.length' => 'IMEI号长度必须在15-17个字符之间',
        'imei.alphaNum' => 'IMEI号只能是字母和数字',
        'customer_name.require' => '客户姓名不能为空',
        'customer_name.length' => '客户姓名长度必须在2-50个字符之间',
        'customer_phone.require' => '客户电话不能为空',
        'customer_phone.mobile' => '客户电话格式不正确',
        'photo_url.url' => '照片URL格式不正确',
        'remark.max' => '备注最多500个字符',
    ];

    protected $scene = [
        'create' => ['store_id', 'salesperson_id', 'phone_brand_id', 'phone_model_id', 'imei', 'customer_name', 'customer_phone', 'photo_url', 'remark'],
        'update' => ['store_id', 'salesperson_id', 'phone_brand_id', 'phone_model_id', 'imei', 'customer_name', 'customer_phone', 'photo_url', 'remark'],
    ];
} 