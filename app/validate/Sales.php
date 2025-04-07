<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class Sales extends Validate
{
    protected $rule = [
        'store_id'       => 'number',
        'store_name'     => 'requireWithout:store_id|length:2,50',
        'salesperson_id' => 'number',
        'salesperson_name' => 'requireWithout:salesperson_id|length:2,50',
        'phone_brand_id' => 'number',
        'phone_brand_name' => 'requireWithout:phone_brand_id|length:2,50',
        'phone_model_id' => 'number',
        'phone_model_name' => 'requireWithout:phone_model_id|length:2,100',
        'imei'          => 'require|length:10,30|alphaNum',
        'customer_name' => 'require|length:2,50',
        'customer_phone' => 'require|mobile',
        'remark'        => 'max:500',
    ];

    protected $message = [
        'store_name.requireWithout' => '门店名称不能为空',
        'store_name.length' => '门店名称长度必须在2-50个字符之间',
        'salesperson_name.requireWithout' => '销售员姓名不能为空',
        'salesperson_name.length' => '销售员姓名长度必须在2-50个字符之间',
        'phone_brand_name.requireWithout' => '手机品牌名称不能为空',
        'phone_brand_name.length' => '手机品牌名称长度必须在2-50个字符之间',
        'phone_model_name.requireWithout' => '手机型号名称不能为空',
        'phone_model_name.length' => '手机型号名称长度必须在2-100个字符之间',
        'store_id.number' => '门店ID必须是数字',
        'salesperson_id.number' => '销售员ID必须是数字',
        'phone_brand_id.number' => '手机品牌ID必须是数字',
        'phone_model_id.number' => '手机型号ID必须是数字',
        'imei.require' => 'IMEI号不能为空',
        'imei.length' => 'IMEI号长度必须在10-30个字符之间',
        'imei.alphaNum' => 'IMEI号只能是字母和数字',
        'customer_name.require' => '客户姓名不能为空',
        'customer_name.length' => '客户姓名长度必须在2-50个字符之间',
        'customer_phone.require' => '客户电话不能为空',
        'customer_phone.mobile' => '客户电话格式不正确',
        'remark.max' => '备注最多500个字符',
    ];

    protected $scene = [
        'create' => ['store_id', 'store_name', 'salesperson_id', 'salesperson_name', 
                    'phone_brand_id', 'phone_brand_name', 'phone_model_id', 'phone_model_name', 
                    'imei', 'customer_name', 'customer_phone', 'photo_url', 'remark'],
        'update' => ['store_id', 'store_name', 'salesperson_id', 'salesperson_name', 
                    'phone_brand_id', 'phone_brand_name', 'phone_model_id', 'phone_model_name', 
                    'imei', 'customer_name', 'customer_phone', 'photo_url', 'remark'],
    ];
} 