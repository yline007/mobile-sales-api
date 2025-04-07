<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * 系统设置模型
 * Class SystemSetting
 * @package app\model
 */
class SystemSetting extends Model
{
    // 设置表名
    protected $name = 'system_setting';
    
    // 设置字段信息
    protected $schema = [
        'id'          => 'int',
        'key'         => 'string',
        'value'       => 'string',
        'remark'      => 'string',
        'create_time' => 'datetime',
        'update_time' => 'datetime'
    ];
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    
    /**
     * 获取设置值
     * @param string $key 键名
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function getSettingValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->find();
        if (!$setting) {
            return $default;
        }
        return $setting->value;
    }
    
    /**
     * 设置设置值
     * @param string $key 键名
     * @param mixed $value 值
     * @param string $remark 备注
     * @return bool
     */
    public static function setSettingValue(string $key, $value, string $remark = null): bool
    {
        $setting = self::where('key', $key)->find();
        if (!$setting) {
            $setting = new self();
            $setting->key = $key;
        }
        
        $setting->value = (string)$value;
        if ($remark !== null) {
            $setting->remark = $remark;
        }
        
        return $setting->save();
    }
} 