<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * 管理员模型
 * Class Admin
 * @package app\model
 */
class Admin extends Model
{
    // 设置表名
    protected $name = 'admin';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'username'    => 'string',
        'password'    => 'string',
        'nickname'    => 'string',
        'avatar'      => 'string',
        'email'       => 'string',
        'status'      => 'int',
        'role'        => 'string',
        'create_time' => 'datetime',
        'update_time' => 'datetime'
    ];
    
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 密码修改器
     * @param string $value
     * @return string
     */
    public function setPasswordAttr(string $value): string
    {
        return md5($value);
    }
    
    /**
     * 关联销售记录
     * @return \think\model\relation\HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sales::class, 'admin_id', 'id');
    }
} 