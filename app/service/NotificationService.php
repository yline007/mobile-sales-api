<?php
declare (strict_types = 1);

namespace app\service;

use Predis\Client;
use think\facade\Log;

class NotificationService
{
    protected $redis;

    public function __construct()
    {
        try {
            // 从环境变量获取 Redis 配置
            $redisConfig = [
                'scheme' => env('REDIS_SCHEME', 'tcp'),
                'host'   => env('REDIS_HOST', '127.0.0.1'),
                'port'   => env('REDIS_PORT', 6379),
            ];
            
            // 如果设置了密码，添加到配置中
            if ($password = env('REDIS_PASSWORD')) {
                $redisConfig['password'] = $password;
            }

            $this->redis = new \Predis\Client($redisConfig);
            
            // 测试连接
            $this->redis->ping();
            Log::info('Redis连接成功');
        } catch (\Exception $e) {
            Log::error('Redis连接失败：' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * 发送销售记录通知
     * @param array $data 销售记录数据
     */
    public function sendSalesNotification(array $data): void
    {
        try {
            $message = [
                'type' => 'new_sale',
                'title' => '新销售记录提醒',
                'content' => sprintf(
                    '销售员 %s 提交了一条新的销售记录，客户：%s，手机型号：%s',
                    $data['salesperson_name'],
                    $data['customer_name'],
                    $data['phone_model_name']
                ),
                'sale_id' => $data['id'],
                'create_time' => $data['create_time'],
                'data' => $data
            ];

            // 将消息添加到 Redis 列表
            $this->redis->rpush('sales_notifications', json_encode($message, JSON_UNESCAPED_UNICODE));
            
            Log::info('销售通知已添加到队列');
        } catch (\Exception $e) {
            Log::error('发送销售通知失败：' . $e->getMessage());
        }
    }

    /**
     * 发送系统通知
     * @param string $title 通知标题
     * @param string $content 通知内容
     * @param array $data 额外数据
     */
    public function sendSystemNotification(string $title, string $content, array $data = []): void
    {
        try {
            $message = [
                'type' => 'system',
                'title' => $title,
                'content' => $content,
                'create_time' => date('Y-m-d H:i:s'),
                'data' => $data
            ];

            // 将消息添加到 Redis 列表
            $this->redis->rpush('sales_notifications', json_encode($message, JSON_UNESCAPED_UNICODE));
            
            Log::info('系统通知已添加到队列');
        } catch (\Exception $e) {
            Log::error('发送系统通知失败：' . $e->getMessage());
        }
    }
} 