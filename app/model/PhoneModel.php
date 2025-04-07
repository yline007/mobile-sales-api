<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * 手机型号模型
 * Class PhoneModel
 * @package app\model
 */
class PhoneModel extends Model
{
    // 设置表名
    protected $name = 'phone_model';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'brand_id'    => 'int',
        'name'        => 'string',
        'image'       => 'string',
        'status'      => 'int',
        'create_time' => 'datetime',
        'update_time' => 'datetime'
    ];
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 关联手机品牌
     * @return \think\model\relation\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(PhoneBrand::class, 'brand_id', 'id');
    }
    
    /**
     * 关联销售记录
     * @return \think\model\relation\HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sales::class, 'phone_model_id', 'id');
    }
} 