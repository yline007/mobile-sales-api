<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * 手机品牌模型
 * Class PhoneBrand
 * @package app\model
 */
class PhoneBrand extends Model
{
    // 设置表名
    protected $name = 'phone_brand';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'logo'        => 'string',
        'status'      => 'int',
        'create_time' => 'datetime',
        'update_time' => 'datetime'
    ];
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 关联手机型号
     * @return \think\model\relation\HasMany
     */
    public function phoneModels()
    {
        return $this->hasMany(PhoneModel::class, 'brand_id', 'id');
    }
    
    /**
     * 关联销售记录
     * @return \think\model\relation\HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sales::class, 'phone_brand_id', 'id');
    }
} 