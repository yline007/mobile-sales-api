<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * 门店模型
 * Class Store
 * @package app\model
 */
class Store extends Model
{
    // 设置表名
    protected $name = 'store';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'name'        => 'string',
        'address'     => 'string',
        'phone'       => 'string',
        'manager'     => 'string',
        'status'      => 'int',
        'create_time' => 'datetime',
        'update_time' => 'datetime'
    ];
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 关联销售记录
     * @return \think\model\relation\HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sales::class, 'store_id', 'id');
    }
    
    /**
     * 关联销售员
     * @return \think\model\relation\HasMany
     */
    public function salespersons()
    {
        return $this->hasMany(Salesperson::class, 'store_id', 'id');
    }
} 