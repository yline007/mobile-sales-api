<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * 销售员模型
 * Class Salesperson
 * @package app\model
 */
class Salesperson extends Model
{
    // 设置表名
    protected $name = 'salesperson';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'phone'       => 'string',
        'store_id'    => 'int',
        'employee_id' => 'string',
        'status'      => 'int',
        'create_time' => 'datetime',
        'update_time' => 'datetime'
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
     * 关联销售记录
     * @return \think\model\relation\HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sales::class, 'salesperson_id', 'id');
    }
} 