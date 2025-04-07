<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * 销售记录模型
 * Class Sales
 * @package app\model
 */
class Sales extends Model
{
    // 设置表名
    protected $name = 'sales';
    
    // 设置字段信息
    protected $schema = [
        'id'             => 'int',
        'store_id'       => 'int',
        'salesperson_id' => 'int',
        'phone_brand_id' => 'int',
        'phone_model_id' => 'int',
        'imei'           => 'string',
        'customer_name'  => 'string',
        'customer_phone' => 'string',
        'photo_url'      => 'string',
        'remark'         => 'string',
        'create_time'    => 'datetime',
        'update_time'    => 'datetime'
    ];
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 关联门店
     * @return \think\model\relation\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
    
    /**
     * 关联销售员
     * @return \think\model\relation\BelongsTo
     */
    public function salesperson()
    {
        return $this->belongsTo(Salesperson::class, 'salesperson_id', 'id');
    }
    
    /**
     * 关联手机品牌
     * @return \think\model\relation\BelongsTo
     */
    public function phoneBrand()
    {
        return $this->belongsTo(PhoneBrand::class, 'phone_brand_id', 'id');
    }
    
    /**
     * 关联手机型号
     * @return \think\model\relation\BelongsTo
     */
    public function phoneModel()
    {
        return $this->belongsTo(PhoneModel::class, 'phone_model_id', 'id');
    }
} 